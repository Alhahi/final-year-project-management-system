<?php
require_once '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $goal_id = $_POST['goal_id'];
    $goal_title = $_POST['goal'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    // Open database connection
    $conn = OpenCon();

    // Update goal details query
    $query = "UPDATE goals SET goal = ?, due_date = ?, status = ? WHERE goal_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $goal_title, $due_date, $status, $goal_id);

    if ($stmt->execute()) {
        header("Location: progress-page.php"); // Redirect to the progress page after update
    } else {
        echo "Error updating goal.";
    }

    CloseCon($conn);
}
?>
