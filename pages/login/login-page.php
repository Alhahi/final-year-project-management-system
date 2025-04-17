<?php
session_start();

// If user is already logged in, redirect to appropriate page
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $redirect_url = '/fyp-system/';
    switch ($_SESSION['role']) {
        case 'admin':
            $redirect_url .= 'pages/admin-dashboard/admin-dashboard-page.php';
            break;
        case 'supervisor':
            $redirect_url .= 'pages/supervisor-dashboard/supervisor-dashboard-page.php';
            break;
        case 'student':
            $redirect_url .= 'index.php';
            break;
    }
    header('Location: ' . $redirect_url);
    exit();
}

require_once '../../components/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Login</title>
    <link rel="stylesheet" href="./login-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            display: none;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <?php echo getNavbar('login'); ?>

    <!-- Login Section -->
    <div class="login-container">
        <div class="form-container" id="loginForm">
            <div class="form-logo-section">
                <img src="../../assets/images/mmu-logo.png" alt="MMU Logo">
            </div>
            <h2>Welcome Back</h2>
            <p class="subtitle">Please login to your account</p>
            
            <div class="alert" id="loginAlert" style="display: none;"></div>
            
            <form id="loginFormElement">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="password-input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <button type="submit">Login</button>
                <p class="form-footer">Don't have an account? <a href="../register/register-page.php">Register here</a></p>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
    
    <script src="../../assets/js/auth.js"></script>
</body>
</html>
