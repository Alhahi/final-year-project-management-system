<?php
require_once "../../db_connection.php";

if (isset($_GET['id'])) {
    $conn = OpenCon();
    $proposal_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "SELECT pp.*,
            s.full_name as student_name, s.email as student_email,
            sv.full_name as supervisor_name, sv.email as supervisor_email
            FROM project_proposals pp
            LEFT JOIN users s ON pp.student_id = s.user_id
            LEFT JOIN users sv ON pp.supervisor_id = sv.user_id
            WHERE pp.proposal_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $proposal_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $response = array(
            'proposal_id' => $row['proposal_id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'status' => $row['status'],
            'student_id' => $row['student_id'],
            'supervisor_id' => $row['supervisor_id'],
            'student_name' => $row['student_name'],
            'student_email' => $row['student_email'],
            'supervisor_name' => $row['supervisor_name'],
            'supervisor_email' => $row['supervisor_email']
        );
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Proposal not found']);
    }
    
    CloseCon($conn);
}
?> 