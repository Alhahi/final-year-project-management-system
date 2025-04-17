<?php
// Database connection
require_once "../../db_connection.php";
$conn = OpenCon();
session_start();

$successMessage = $errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_id = $_POST['meeting_id'];
    $user_id = $_SESSION['user_id'];
    $work_done = $_POST['work_done'];
    $future_work = $_POST['future_work'];
    $other = $_POST['other'];

    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO meeting_logs (meeting_id, user_id, work_done, future_work, other) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $meeting_id, $user_id, $work_done, $future_work, $other);

    if ($stmt->execute()) {
        $successMessage = "Meeting log added successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    header("Location: meetings-page.php");
    $stmt->close();
}

$conn->close();
?>