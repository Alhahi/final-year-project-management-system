<?php
session_start();
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';


if (isset($_GET['id'])) {
    $slot_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $conn = OpenCon();

    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if student already has a booking
        $check_query = "SELECT * FROM presentations_slots WHERE user_id = ? AND status = 'booked'";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            throw new Exception("You already have a booked presentation slot.");
        }

        // Check if slot is still available
        $slot_query = "SELECT * FROM presentations_slots 
                      WHERE presentation_slot_id = ? AND status = 'available'
                      AND slot_date >= CURDATE()
                      FOR UPDATE";
        $stmt = mysqli_prepare($conn, $slot_query);
        mysqli_stmt_bind_param($stmt, "i", $slot_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows === 0) {
            throw new Exception("This slot is no longer available.");
        }

        // Book the slot
        $book_query = "UPDATE presentations_slots 
                      SET status = 'booked', user_id = ? 
                      WHERE presentation_slot_id = ?";
        $stmt = mysqli_prepare($conn, $book_query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $slot_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error booking the slot.");
        }

        mysqli_commit($conn);
        $_SESSION['success_message'] = "Presentation slot booked successfully!";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
    }

    CloseCon($conn);
}

header("Location: projects-page.php");
exit(); 

?>