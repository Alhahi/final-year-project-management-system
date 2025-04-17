<?php
session_start();
require_once '../../auth/role_check.php';
require_once '../../db_connection.php';

if (!checkSupervisorRole()) {
    $_SESSION['error_message'] = "Access denied. Supervisor privileges required.";
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slot_id = $_POST['slot_id'];
    $slot_date = $_POST['slot_date'];
    $slot_time = $_POST['slot_time'];

    // Validate date and time
    $datetime = $slot_date . ' ' . $slot_time;
    if (strtotime($datetime) < time()) {
        $_SESSION['error_message'] = "Cannot update to past dates.";
        header("Location: manage-presentations.php");
        exit();
    }

    $conn = OpenCon();

    // Check if slot exists and is available
    $check_query = "SELECT status FROM presentations_slots WHERE presentation_slot_id = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $slot_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $slot = mysqli_fetch_assoc($result);

    if ($slot && $slot['status'] === 'available') {
        $update_query = "UPDATE presentations_slots SET slot_date = ?, slot_time = ? WHERE presentation_slot_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $slot_date, $slot_time, $slot_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Presentation slot updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating presentation slot.";
        }
    } else {
        $_SESSION['error_message'] = "Cannot update booked or non-existent slot.";
    }

    mysqli_stmt_close($stmt);
    CloseCon($conn);
}

header("Location: manage-presentations.php");
exit(); 