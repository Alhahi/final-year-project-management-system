<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id']);

    try {
        // Check if course code already exists
        $check_sql = "SELECT course_id FROM courses WHERE course_code = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $course_code);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            throw new Exception("Course code already exists");
        }

        // Insert new course
        $sql = "INSERT INTO courses (course_name, course_code, faculty_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $course_name, $course_code, $faculty_id);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['success_message'] = "Course added successfully!";
        header("Location: manage-courses.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error adding course: " . $e->getMessage();
        header("Location: manage-courses.php");
        exit();
    }

    CloseCon($conn);
}
?> 