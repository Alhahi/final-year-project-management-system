<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project_id'])) {
    $conn = OpenCon();
    $project_id = mysqli_real_escape_string($conn, $_POST['project_id']);
    
    try {
        mysqli_begin_transaction($conn);
        
        // Get the proposal_id before deleting the project
        $get_proposal_sql = "SELECT proposal_id FROM projects WHERE project_id = ?";
        $get_proposal_stmt = mysqli_prepare($conn, $get_proposal_sql);
        mysqli_stmt_bind_param($get_proposal_stmt, "i", $project_id);
        mysqli_stmt_execute($get_proposal_stmt);
        $proposal_result = mysqli_stmt_get_result($get_proposal_stmt);
        $proposal_row = mysqli_fetch_assoc($proposal_result);
        $proposal_id = $proposal_row['proposal_id'];

        // Delete the project
        $sql = "DELETE FROM projects WHERE project_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $project_id);
        mysqli_stmt_execute($stmt);

        // Update the proposal status back to pending
        $update_proposal_sql = "UPDATE project_proposals SET status = 'pending' WHERE proposal_id = ?";
        $update_proposal_stmt = mysqli_prepare($conn, $update_proposal_sql);
        mysqli_stmt_bind_param($update_proposal_stmt, "i", $proposal_id);
        mysqli_stmt_execute($update_proposal_stmt);
        
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Project deleted successfully!";
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Error deleting project: " . $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    CloseCon($conn);
}
?> 