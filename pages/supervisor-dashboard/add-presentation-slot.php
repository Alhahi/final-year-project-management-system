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
    $slot_date = $_POST['slot_date'];
    $slot_time = $_POST['slot_time'];
    $supervisor_id = $_SESSION['user_id'];

    // Validate date and time
    $datetime = $slot_date . ' ' . $slot_time;
    if (strtotime($datetime) < time()) {
        $_SESSION['error_message'] = "Cannot create slots for past dates.";
        header("Location: manage-presentations.php");
        exit();
    }

    // Open database connection
    $conn = OpenCon();

    $query = "INSERT INTO presentations_slots (slot_date, slot_time, status, supervisor_id) 
              VALUES (?, ?, 'available', ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $slot_date, $slot_time, $supervisor_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Presentation slot added successfully.";
    } else {
        $_SESSION['error_message'] = "Error adding presentation slot.";
    }
    
    mysqli_stmt_close($stmt);
    CloseCon($conn);
}

header("Location: manage-presentations.php");
exit(); 