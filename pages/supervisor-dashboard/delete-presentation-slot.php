<?php
session_start();
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';

if (isset($_GET['id'])) {
    $slot_id = $_GET['id'];
    $supervisor_id = $_SESSION['user_id'];
    
    $conn = OpenCon();
    
    // Check if the slot belongs to this supervisor
    $check_query = "SELECT * FROM presentations_slots 
                    WHERE presentation_slot_id = ? AND supervisor_id = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $slot_id, $supervisor_id);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_get_result($stmt)->num_rows === 0) {
        $_SESSION['error_message'] = "Access denied or slot not found.";
        header("Location: manage-presentations.php");
        exit();
    }
    
    // If check passes, proceed with deletion
    $delete_query = "DELETE FROM presentations_slots WHERE presentation_slot_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $slot_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Presentation slot deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting presentation slot.";
    }
    
    mysqli_stmt_close($stmt);
    CloseCon($conn);
}

header("Location: manage-presentations.php");
exit(); 