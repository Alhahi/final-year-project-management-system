<?php
session_start();
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $slot_id = $_GET['id'];
    $new_status = $_GET['status'];
    
    $conn = OpenCon();
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        if ($new_status === 'available') {
            // If changing to available, remove user assignment
            $update_query = "UPDATE presentations_slots 
                           SET status = ?, user_id = NULL 
                           WHERE presentation_slot_id = ?";
        } else {
            // If changing to booked, keep existing user_id
            $update_query = "UPDATE presentations_slots 
                           SET status = ? 
                           WHERE presentation_slot_id = ?";
        }
        
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $slot_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($conn);
            $_SESSION['success_message'] = "Presentation slot status updated successfully.";
        } else {
            throw new Exception("Error updating slot status.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    mysqli_stmt_close($stmt);
    CloseCon($conn);
}

header("Location: manage-presentations.php");
exit(); 