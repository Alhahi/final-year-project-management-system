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
    <title>FCI FYP System - Register</title>
    <link rel="stylesheet" href="./register-page.css">
    <script src="pw-validation.js" defer></script>
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
        .requirement-list li.valid i {
            color: #4CAF50;
        }
        .requirement-list li.valid span {
            color: #999;
        }
    </style>
</head>

<body>
    <?php echo getNavbar('register'); ?>

    <!-- Registration Form -->
    <div class="form-container" id="registrationForm">
        <div class="form-logo-section">
            <img src="../../assets/images/mmu-logo.png" alt="MMU Logo">
        </div>
        <h2>Student Registration</h2>
        
        <div class="alert" id="loginAlert" style="display: none;"></div>
        
        <form id="registerFormElement">
            <input type="text" name="student_id" placeholder="Student ID" required pattern="^\d{10}$" title="Please enter a valid Student ID, Student ID must be exactly 10 digits">
            <select name="specialization" id="specialization" required>
                <option value="" disabled selected>Select Your Specialization</option>
                <option value="SE">Software Engineering</option>
                <option value="DS">Data Science</option>
                <option value="GD">Game Development</option>
                <option value="CS">Cybersecurity</option>
            </select>
            <input type="email" name="email" placeholder="Student Email" required>
            <input type="text" name="name" placeholder="Full Name" required pattern="^[a-zA-Z\s\p{P}\p{S}]+$" title="Full Name must contain only letters and spaces">
            <div class="wrapper">
                <div class="password-input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            
                <div class="content">
                    <p>Password must contains</p>
                    <ul class="requirement-list">
                        <li>
                            <i class="fa-solid fa-circle"></i>
                            <span>At least 8 characters long</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle"></i>
                            <span>At least 1 number (0...9)</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle"></i>
                            <span>At least 1 lowercase letter (a...z)</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle"></i>
                            <span>At least 1 special symbol (!,@,#,$,%,^,&,*)</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle"></i>
                            <span>At least 1 uppercase letter (A...Z)</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="password-input-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="phone-input-group">
                <select name="country_code" id="country_code" required>
                    <option value="+60">🇲🇾 +60 (Malaysia)</option>
                    <option value="+65">🇸🇬 +65 (Singapore)</option>
                    <option value="+62">🇮🇩 +62 (Indonesia)</option>
                    <option value="+66">🇹🇭 +66 (Thailand)</option>
                    <option value="+84">🇻🇳 +84 (Vietnam)</option>
                    <option value="+63">🇵🇭 +63 (Philippines)</option>
                    <option value="+95">🇲🇲 +95 (Myanmar)</option>
                    <option value="+673">🇧🇳 +673 (Brunei)</option>
                    <option value="+855">🇰🇭 +855 (Cambodia)</option>
                    <option value="+856">🇱🇦 +856 (Laos)</option>
                </select>
                <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" required pattern="^\d+$" title="Enter only digits">
            </div>
            <button type="submit">Register</button>
            <p class="form-footer">Already have an account? <a href="../login/login-page.php">Login here</a></p>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
    
    <script>
    function validatePhoneNumber(countryCode, phoneNumber) {
        // Remove '+' from country code and combine with phone number
        const fullPhone = countryCode.substring(1) + phoneNumber;
        return fullPhone.match(/^\d{10,12}$/);
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const phoneNumber = document.getElementById('phone_number').value.trim();
        const countryCode = document.getElementById('country_code').value;
        
        if (!validatePhoneNumber(countryCode, phoneNumber)) {
            e.preventDefault();
            alert('Phone number including country code should be between 10-12 digits');
            return false;
        }
    });
    </script>
    <script src="../../assets/js/auth.js"></script>
</body>

</html>