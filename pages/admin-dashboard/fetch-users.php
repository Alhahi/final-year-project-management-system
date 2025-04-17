<?php
require_once "../../db_connection.php";

function getAllUsers() {
    $sql = "SELECT u.user_id, u.full_name, u.email, u.role, u.phone_number, u.created_at,
            CASE 
                WHEN s.student_id IS NOT NULL THEN CONCAT(s.matric_number, ' (', s.course, ')')
                WHEN sv.supervisor_id IS NOT NULL THEN 'Supervisor'
                WHEN a.admin_id IS NOT NULL THEN 'Admin'
            END as additional_info,
            s.matric_number,
            s.course
            FROM users u
            LEFT JOIN students s ON u.user_id = s.student_id
            LEFT JOIN supervisors sv ON u.user_id = sv.supervisor_id
            LEFT JOIN admins a ON u.user_id = a.admin_id
            ORDER BY u.created_at DESC";
            
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}
?> 