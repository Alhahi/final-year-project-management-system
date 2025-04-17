<?php
require_once "../../db_connection.php";

if (isset($_GET['user_id'])) {
    $conn = OpenCon();
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
    
    $sql = "SELECT u.*, 
            s.matric_number, s.course,
            sv.expertise
            FROM users u
            LEFT JOIN students s ON u.user_id = s.student_id
            LEFT JOIN supervisors sv ON u.user_id = sv.supervisor_id
            WHERE u.user_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
    
    CloseCon($conn);
}
?> 