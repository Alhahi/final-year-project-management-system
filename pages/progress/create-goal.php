<?php
require_once '../../auth/auth_check.php'; // Ensure the user is authenticated
require_once '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    // Ensure user_id exists in the session
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        // Handle error or redirect if user is not logged in
        echo "User not logged in!";
        exit;
    }

    $goal = $_POST['goal'];        // Goal description
    $due_date = $_POST['due_date']; // Due date

    $conn = OpenCon();

    // Insert goal into the database
    $query = "INSERT INTO goals (user_id, goal, due_date) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $goal, $due_date);

    if ($stmt->execute()) {
        // Redirect or success message
        header("Location: progress-page.php"); // Adjust the redirect as needed
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }
}
?>
