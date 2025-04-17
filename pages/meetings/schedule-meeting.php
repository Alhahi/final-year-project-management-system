<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['meetingTitle'];
    $date = $_POST['meetingDate'];
    $start_time = $_POST['meetingStartTime'];
    $end_time = $_POST['meetingEndTime'];
    $location = $_POST['meetingLocation'];
    $description = $_POST['meetingDescription'];

    // Validate input
    if (!$title || !$date || !$start_time || !$end_time || !$location) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    require_once '../../db_connection.php';
    $db = OpenCon();
    session_start();
    $user_id = $_SESSION['user_id'];

    // Insert into database
    $query = "INSERT INTO meetings (user_id, title, description, date, start_time, end_time, venue, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, 'upcoming')";
    $stmt = $db->prepare($query);
    $stmt->bind_param('issssss', $user_id, $title, $description, $date, $start_time, $end_time, $location);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
        header("Location: meetings-page.php");
    } else {
        http_response_code(500);
        echo $stmt->error;
        echo json_encode(['error' => 'Database error occurred.']);
    }
}

?>