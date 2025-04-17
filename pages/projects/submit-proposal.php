<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $submission_type = mysqli_real_escape_string($conn, $_POST['submission_type']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    $user_id = $_SESSION['user_id'];
    
    try {
        mysqli_begin_transaction($conn);

        // Get student_id from students table
        $sql_get_student = "SELECT student_id FROM students WHERE student_id = ?";
        $stmt_student = mysqli_prepare($conn, $sql_get_student);
        mysqli_stmt_bind_param($stmt_student, "i", $user_id);
        mysqli_stmt_execute($stmt_student);
        $result = mysqli_stmt_get_result($stmt_student);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $student_id = $row['student_id'];
            
            if ($submission_type === 'supervisor') {
                // For supervisor submission
                $status = 'pending';
                
                $sql = "INSERT INTO supervisor_proposals (student_id, supervisor_id, title, description, status) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iisss", 
                    $student_id,
                    $supervisor_id,
                    $title,
                    $description,
                    $status
                );
            } else {
                // For admin submission
                $status = 'pending';
                
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
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error submitting proposal: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            
            $_SESSION['success_message'] = "Proposal submitted successfully!";
            header("Location: projects-page.php");
            exit();
        } else {
            throw new Exception("Student record not found");
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: projects-page.php");
        exit();
    }

    CloseCon($conn);
} else {
    header("Location: projects-page.php");
    exit();
}
?> 