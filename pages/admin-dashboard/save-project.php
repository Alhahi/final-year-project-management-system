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
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    try {
        mysqli_begin_transaction($conn);

        // First create a proposal
        $proposal_sql = "INSERT INTO project_proposals (title, description, student_id, supervisor_id, status) 
                        VALUES (?, ?, ?, ?, 'approved')";
        
        $proposal_stmt = mysqli_prepare($conn, $proposal_sql);
        mysqli_stmt_bind_param($proposal_stmt, "ssii", 
            $title, 
            $description, 
            $student_id, 
            $supervisor_id
        );
        
        if (!mysqli_stmt_execute($proposal_stmt)) {
            throw new Exception("Error creating proposal: " . mysqli_error($conn));
        }

        $proposal_id = mysqli_insert_id($conn);

        // Create the project
        $project_sql = "INSERT INTO projects (proposal_id, start_date, end_date, status) 
                       VALUES (?, ?, ?, 'ongoing')";
        
        $project_stmt = mysqli_prepare($conn, $project_sql);
        mysqli_stmt_bind_param($project_stmt, "iss", 
            $proposal_id,
            $start_date,
            $end_date
        );
        
        if (!mysqli_stmt_execute($project_stmt)) {
            throw new Exception("Error creating project: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Project created successfully!";
        header("Location: manage-projects.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: add-project.php");
        exit();
    }

    CloseCon($conn);
} else {
    header("Location: manage-projects.php");
    exit();
}
?> 