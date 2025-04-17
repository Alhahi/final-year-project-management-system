<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $conn = OpenCon();
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    try {
        mysqli_begin_transaction($conn);

        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "User deleted successfully!";
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    CloseCon($conn);
}
?> 