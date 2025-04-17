<?php
function checkAdminRole() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        $_SESSION['error_message'] = "Please log in to access this page.";
        return false;
    }

    // Check if user is an admin
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = "Access denied. You don't have permission to view this page.";
        return false;
    }

    return true;
}

function checkSupervisorRole() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        $_SESSION['error_message'] = "Please log in to access this page.";
        return false;
    }

    // Check if user is a supervisor
    if ($_SESSION['role'] !== 'supervisor') {
        $_SESSION['error_message'] = "Access denied. You don't have permission to view this page.";
        return false;
    }

    return true;
}

function checkStudentRole() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        $_SESSION['error_message'] = "Please log in to access this page.";
        return false;
    }

    // Check if user is a student
    if ($_SESSION['role'] !== 'student') {
        $_SESSION['error_message'] = "Access denied. You don't have permission to view this page.";
        return false;
    }

    return true;
}
?> 