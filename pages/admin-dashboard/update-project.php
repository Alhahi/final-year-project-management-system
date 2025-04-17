<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    // Get form data
    $project_id = mysqli_real_escape_string($conn, $_POST['project_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $start_date = !empty($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : null;
    $end_date = !empty($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : null;

    try {
        mysqli_begin_transaction($conn);

        // Get proposal_id for the project
        $get_proposal_sql = "SELECT proposal_id FROM projects WHERE project_id = ?";
        $get_proposal_stmt = mysqli_prepare($conn, $get_proposal_sql);
        mysqli_stmt_bind_param($get_proposal_stmt, "i", $project_id);
        mysqli_stmt_execute($get_proposal_stmt);
        $proposal_result = mysqli_stmt_get_result($get_proposal_stmt);
        $proposal_row = mysqli_fetch_assoc($proposal_result);
        $proposal_id = $proposal_row['proposal_id'];

        // Update proposal details
        $proposal_sql = "UPDATE project_proposals SET 
                        title = ?, 
                        description = ?, 
                        student_id = ?, 
                        supervisor_id = ?
                        WHERE proposal_id = ?";
        
        $proposal_stmt = mysqli_prepare($conn, $proposal_sql);
        mysqli_stmt_bind_param($proposal_stmt, "ssiis", 
            $title, 
            $description, 
            $student_id, 
            $supervisor_id,
            $proposal_id
        );
        
        if (!mysqli_stmt_execute($proposal_stmt)) {
            throw new Exception("Error updating proposal details: " . mysqli_error($conn));
        }

        // Update project
        if (!in_array($status, ['ongoing', 'completed', 'failed'])) {
            throw new Exception("Invalid project status");
        }

        $project_sql = "UPDATE projects SET 
                       status = ?, 
                       start_date = ?, 
                       end_date = ?
                       WHERE project_id = ?";
        
        $project_stmt = mysqli_prepare($conn, $project_sql);
        mysqli_stmt_bind_param($project_stmt, "sssi", 
            $status,
            $start_date,
            $end_date,
            $project_id
        );
        
        if (!mysqli_stmt_execute($project_stmt)) {
            throw new Exception("Error updating project: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Project updated successfully!";
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