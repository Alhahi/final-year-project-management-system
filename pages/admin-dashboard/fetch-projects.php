<?php
require_once "../../db_connection.php";

function getAllProjects() {
    $sql = "SELECT pp.proposal_id, pp.title as proposal_title, pp.description as proposal_description, 
            pp.status as proposal_status, pp.student_id as proposal_student_id, 
            pp.supervisor_id as proposal_supervisor_id,
            p.project_id, p.title, p.description, p.status as project_status, 
            p.start_date, p.end_date, p.student_id, p.supervisor_id,
            s.full_name as student_name, s.user_id as student_id,
            sv.full_name as supervisor_name, sv.user_id as supervisor_id,
            s.email as student_email,
            sv.email as supervisor_email
            FROM project_proposals pp
            LEFT JOIN projects p ON pp.proposal_id = p.proposal_id
            LEFT JOIN users s ON pp.student_id = s.user_id
            LEFT JOIN users sv ON pp.supervisor_id = sv.user_id
            ORDER BY pp.proposal_id DESC";
            
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}

function getAllProposals() {
    $sql = "SELECT pp.proposal_id, pp.title, pp.description, pp.status,
            s.full_name as student_name, s.email as student_email, s.user_id as student_id,
            sv.full_name as supervisor_name, sv.email as supervisor_email, sv.user_id as supervisor_id
            FROM project_proposals pp
            LEFT JOIN users s ON pp.student_id = s.user_id
            LEFT JOIN users sv ON pp.supervisor_id = sv.user_id
            ORDER BY pp.proposal_id DESC";
            
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}

function getAllActiveProjects() {
    $sql = "SELECT p.*, pp.title, pp.description,
            s.full_name as student_name, s.email as student_email, s.user_id as student_id,
            sv.full_name as supervisor_name, sv.email as supervisor_email, sv.user_id as supervisor_id
            FROM projects p
            JOIN project_proposals pp ON p.proposal_id = pp.proposal_id
            LEFT JOIN users s ON pp.student_id = s.user_id
            LEFT JOIN users sv ON pp.supervisor_id = sv.user_id
            ORDER BY p.start_date DESC";
            
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}
?> 