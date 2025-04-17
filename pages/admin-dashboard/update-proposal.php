<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    // Get form data
    $proposal_id = mysqli_real_escape_string($conn, $_POST['proposal_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    try {
        mysqli_begin_transaction($conn);

        // Validate status
        if (!in_array($status, ['pending', 'approved', 'rejected', 'available'])) {
            throw new Exception("Invalid proposal status");
        }

        // Update proposal
        $sql = "UPDATE project_proposals SET 
                title = ?, 
                description = ?, 
                student_id = ?, 
                supervisor_id = ?, 
                status = ?
                WHERE proposal_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssiisi", 
            $title, 
            $description, 
            $student_id, 
            $supervisor_id, 
            $status, 
            $proposal_id
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating proposal: " . mysqli_error($conn));
        }

        // If status is changed to approved, create a project
        if ($status === 'approved') {
            // Check if project already exists
            $check_sql = "SELECT project_id FROM projects WHERE proposal_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "i", $proposal_id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) === 0) {
                // Create new project
                $project_sql = "INSERT INTO projects (proposal_id, status) VALUES (?, 'not_started')";
                $project_stmt = mysqli_prepare($conn, $project_sql);
                mysqli_stmt_bind_param($project_stmt, "i", $proposal_id);
                
                if (!mysqli_stmt_execute($project_stmt)) {
                    throw new Exception("Error creating project: " . mysqli_error($conn));
                }
            }
        }

        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Proposal updated successfully!";
        header("Location: manage-projects.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: manage-projects.php");
        exit();
    }

    CloseCon($conn);
} else {
    header("Location: manage-projects.php");
    exit();
}
?> 