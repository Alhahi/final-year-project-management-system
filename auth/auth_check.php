<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Store the current URL in session to redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: /fyp-system/pages/login/login-page.php');
    exit();
}

// Function to check if user has specific role
function checkRole($allowed_roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: /fyp-system/index.php');
        exit();
    }
}

// Function to check if session is valid
function validateSession() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !isset($_SESSION['email'])) {
        session_destroy();
        header('Location: /fyp-system/pages/login/login-page.php');
        exit();
    }
}

// Validate session on every page load
validateSession();
?> 
