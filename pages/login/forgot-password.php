<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Forgot Password</title>
    <link rel="stylesheet" href="./forgot-password.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="logo">
                <img src="../../assets/images/mmu-logo.png" alt="MMU Logo">
            </div>
            
            <!-- Email Form -->
            <div class="form-section" id="emailForm">
                <h2>Forgot Password</h2>
                <p class="description">Enter your email address and we'll send you a verification code.</p>
                
                <form>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your student email" required>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Send Verification Code</button>
                </form>
                
                <div class="back-to-login">
                    <a href="./login-page.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>

            <!-- Verification Code Form -->
            <div class="form-section" id="verificationForm" style="display: none;">
                <h2>Enter Verification Code</h2>
                <p class="description">We've sent a 6-digit code to your email. Enter it below.</p>
                
                <form>
                    <div class="verification-code">
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                        <input type="text" maxlength="1" pattern="[0-9]" required>
                    </div>
                    
                    <div class="resend-code">
                        <span>Didn't receive the code?</span>
                        <a href="#" class="resend-link">Resend Code</a>
                        <div class="timer">Resend in: <span id="timer">02:00</span></div>
                    </div>

                    <button type="submit" class="submit-btn">Verify Code</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 