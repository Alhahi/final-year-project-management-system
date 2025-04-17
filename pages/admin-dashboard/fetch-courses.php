<?php
require_once "../../db_connection.php";

function getAllCourses() {
    $sql = "SELECT c.*, f.faculty_name 
            FROM courses c
            LEFT JOIN faculties f ON c.faculty_id = f.faculty_id
            ORDER BY c.course_name";
            
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}

function getAllFaculties() {
    $sql = "SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name";
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}
?> 