<?php
session_start();
require_once "../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = OpenCon();
    
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);
    
    try {
        mysqli_begin_transaction($conn);
        
        // Update basic user information
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, country_code = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone_number, $country_code, $user_id);
        mysqli_stmt_execute($stmt);
        
        // Update role-specific information
        if ($role === 'student') {
            $matric_number = mysqli_real_escape_string($conn, $_POST['matric_number']);
            $course = mysqli_real_escape_string($conn, $_POST['course']);
            
            $sql = "INSERT INTO students (student_id, matric_number, course) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    matric_number = VALUES(matric_number), 
                    course = VALUES(course)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $user_id, $matric_number, $course);
            mysqli_stmt_execute($stmt);
        }
        elseif ($role === 'supervisor') {
            $expertise = mysqli_real_escape_string($conn, $_POST['expertise']);
            
            $sql = "INSERT INTO supervisors (supervisor_id, expertise) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    expertise = VALUES(expertise)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $user_id, $expertise);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: manage-users.php");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Error updating user: " . $e->getMessage();
        header("Location: manage-users.php");
        exit();
    }
    
    CloseCon($conn);
}
?> 