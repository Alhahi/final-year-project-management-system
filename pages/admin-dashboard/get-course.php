<?php
require_once "../../db_connection.php";

if (isset($_GET['course_id'])) {
    $conn = OpenCon();
    $course_id = mysqli_real_escape_string($conn, $_GET['course_id']);
    
    $sql = "SELECT * FROM courses WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Course not found']);
    }
    
    CloseCon($conn);
}
?> 