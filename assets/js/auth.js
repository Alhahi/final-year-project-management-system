// Form validation and submission handling
document.addEventListener('DOMContentLoaded', function() {
    // Login form handling
    const loginForm = document.getElementById('loginFormElement');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            formData.append('action', 'login');

            fetch('/fyp-system/auth/auth_handlers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Login failed. Please try again.');
                    showAlert('error', `${errorMsg} (Code: REG-${Date.now()})`);
                }
            })
            .catch(() => {
                console.error('Full error:', error);
                showAlert('error', error.message); // Show actual error message
            });
        });
    }

    // Registration form handling
    const registerForm = document.getElementById('registerFormElement');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateRegistrationForm()) {
                return;
            }

            const formData = new FormData(registerForm);
            formData.append('action', 'register');

            fetch('/fyp-system/auth/auth_handlers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '/fyp-system/pages/login/login-page.php';
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(() => {
                showAlert('error', 'Registration failed. Please try again.');
            });
        });
    }
});

// Password validation
function validatePassword(password) {
    const requirements = {
        length: password.length >= 8,
        number: /[0-9]/.test(password),
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        special: /[!@#$%^&*]/.test(password)
    };

    const requirementList = document.querySelectorAll('.requirement-list li');
    if (requirementList.length > 0) {
        requirementList[0].classList.toggle('valid', requirements.length);
        requirementList[1].classList.toggle('valid', requirements.number);
        requirementList[2].classList.toggle('valid', requirements.lowercase);
        requirementList[3].classList.toggle('valid', requirements.special);
        requirementList[4].classList.toggle('valid', requirements.uppercase);
    }

    return Object.values(requirements).every(Boolean);
}

// Registration form validation
function validateRegistrationForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (!validatePassword(password)) {
        showAlert('error', 'Please meet all password requirements');
        return false;
    }

    if (password !== confirmPassword) {
        showAlert('error', 'Passwords do not match');
        return false;
    }

    return true;
}

// Show alert message
function showAlert(type, message) {
    const alertElement = document.getElementById('loginAlert') || document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    alertElement.textContent = message;
    alertElement.style.display = 'block';

    if (!document.getElementById('loginAlert')) {
        const form = document.querySelector('form');
        form.insertBefore(alertElement, form.firstChild);
    }

    setTimeout(() => {
        alertElement.style.display = 'none';
    }, 3000);
}

// Password visibility toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Logout handling
function handleLogout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'logout');

    fetch('/fyp-system/auth/auth_handlers.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message before redirecting
            alert('Logged out successfully');
            window.location.href = '/fyp-system/pages/login/login-page.php';
        } else {
            alert(data.message || 'Logout failed. Please try again.');
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        alert('Logout failed. Please try again or refresh the page.');
    });
} 