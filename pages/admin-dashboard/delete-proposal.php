<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proposal_id'])) {
    $conn = OpenCon();
    $proposal_id = mysqli_real_escape_string($conn, $_POST['proposal_id']);
    
    try {
        mysqli_begin_transaction($conn);
        
        // First check if this proposal has an associated project
        $check_sql = "SELECT p.project_id, pp.status 
                     FROM project_proposals pp
                     LEFT JOIN projects p ON pp.proposal_id = p.proposal_id
                     WHERE pp.proposal_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $proposal_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);

        if ($check_row && $check_row['project_id']) {
            throw new Exception("Cannot delete this proposal as it has an associated project. Please delete the project first.");
        }
        
        // Delete the proposal
        $sql = "DELETE FROM project_proposals WHERE proposal_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $proposal_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Proposal deleted successfully!";
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    CloseCon($conn);
}
?> 