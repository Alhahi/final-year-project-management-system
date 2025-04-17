<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    // Get basic user data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);

    try {
        // Start transaction
        mysqli_begin_transaction($conn);

        // Insert into users table
        $sql = "INSERT INTO users (email, password, full_name, role, phone_number, country_code) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $email, $password, $full_name, $role, $phone_number, $country_code);
        mysqli_stmt_execute($stmt);
        
        $user_id = mysqli_insert_id($conn);

        // Insert role-specific data
        if ($role === 'student') {
            if (empty($_POST['matric_number']) || empty($_POST['course'])) {
                throw new Exception("Matric number and course are required for students");
            }
            
            $matric_number = mysqli_real_escape_string($conn, $_POST['matric_number']);
            $course = mysqli_real_escape_string($conn, $_POST['course']);
            
            // Check if matric number already exists
            $check_sql = "SELECT student_id FROM students WHERE matric_number = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "s", $matric_number);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                throw new Exception("Matric number already exists");
            }
            
            $sql = "INSERT INTO students (student_id, matric_number, course) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $user_id, $matric_number, $course);
            mysqli_stmt_execute($stmt);
        } 
        elseif ($role === 'supervisor') {
            $expertise = mysqli_real_escape_string($conn, $_POST['expertise']);
            
            $sql = "INSERT INTO supervisors (supervisor_id, expertise) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $user_id, $expertise);
            mysqli_stmt_execute($stmt);
        }
        elseif ($role === 'admin') {
            $sql = "INSERT INTO admins (admin_id) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
        }

        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "User added successfully!";
        header("Location: manage-users.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Error adding user: " . $e->getMessage();
        header("Location: manage-users.php");
        exit();
    }

    CloseCon($conn);
}
?> 