<?php
require_once "../../db_connection.php";

if (isset($_GET['id'])) {
    $conn = OpenCon();
    $project_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "SELECT p.*, pp.title, pp.description, pp.student_id, pp.supervisor_id,
            s.full_name as student_name, s.email as student_email,
            sv.full_name as supervisor_name, sv.email as supervisor_email
            FROM projects p
            JOIN project_proposals pp ON p.proposal_id = pp.proposal_id
            LEFT JOIN users s ON pp.student_id = s.user_id
            LEFT JOIN users sv ON pp.supervisor_id = sv.user_id
            WHERE p.project_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Project not found']);
    }
    
    CloseCon($conn);
}
?> 