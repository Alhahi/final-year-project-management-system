<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    // Set default status as 'pending' if not provided
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : 'pending';

    try {
        mysqli_begin_transaction($conn);

        // Insert new proposal
        $sql = "INSERT INTO project_proposals (student_id, supervisor_id, title, description, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisss", 
            $student_id, 
            $supervisor_id, 
            $title, 
            $description, 
            $status
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating proposal: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Proposal created successfully!";
        header("Location: manage-projects.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: add-proposal.php");
        exit();
    }

    CloseCon($conn);
} else {
    header("Location: manage-projects.php");
    exit();
}
?> 