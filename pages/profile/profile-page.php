<?php
session_start();
require_once '../../db_connection.php';
require_once '../../components/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /fyp-system/pages/login/login-page.php');
    exit;
}

// Open database connection
$conn = OpenCon();

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, role, country_code, phone_number FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Get additional student data if user is a student
if ($user_data['role'] === 'student') {
    $stmt = $conn->prepare("SELECT matric_number as student_id, course as specialization FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($student_data = $result->fetch_assoc()) {
        $user_data = array_merge($user_data, $student_data);
    }
    $stmt->close();
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $country_code = $_POST['country_code'];
    $phone_number = $_POST['phone_number'];
    $specialization = $_POST['specialization'];
    
    try {
        $conn->begin_transaction();
        
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, country_code = ?, phone_number = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $full_name, $email, $country_code, $phone_number, $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Update students table if user is a student
        if ($user_data['role'] === 'student') {
            $stmt = $conn->prepare("UPDATE students SET course = ? WHERE student_id = ?");
            $stmt->bind_param("si", $specialization, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        
        $conn->commit();
        
        // Update session data
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        
        $response = [
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'full_name' => $full_name,
                'email' => $email,
                'country_code' => $country_code,
                'phone_number' => $phone_number,
                'specialization' => $specialization
            ]
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $response = ['success' => false, 'message' => 'Failed to update profile.'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    CloseCon($conn);
    exit;
}

// Don't forget to close the connection at the end of the file
CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Profile</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./profile-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('profile'); ?>

    <!-- Profile Section -->
    <div class="section content-section" id="profile">
        <h2>Profile</h2>
        <div class="profile-container">
            <form class="profile-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="phone-input-group">
                            <select name="country_code" id="country_code">
                                <option value="+60" <?php echo ($user_data['country_code'] === '+60') ? 'selected' : ''; ?>>🇲🇾 +60 (Malaysia)</option>
                                <option value="+65" <?php echo ($user_data['country_code'] === '+65') ? 'selected' : ''; ?>>🇸🇬 +65 (Singapore)</option>
                                <option value="+62" <?php echo ($user_data['country_code'] === '+62') ? 'selected' : ''; ?>>🇮🇩 +62 (Indonesia)</option>
                                <option value="+66" <?php echo ($user_data['country_code'] === '+66') ? 'selected' : ''; ?>>🇹🇭 +66 (Thailand)</option>
                                <option value="+84" <?php echo ($user_data['country_code'] === '+84') ? 'selected' : ''; ?>>🇻🇳 +84 (Vietnam)</option>
                                <option value="+63" <?php echo ($user_data['country_code'] === '+63') ? 'selected' : ''; ?>>🇵🇭 +63 (Philippines)</option>
                                <option value="+95" <?php echo ($user_data['country_code'] === '+95') ? 'selected' : ''; ?>>🇲🇲 +95 (Myanmar)</option>
                                <option value="+673" <?php echo ($user_data['country_code'] === '+673') ? 'selected' : ''; ?>>🇧🇳 +673 (Brunei)</option>
                                <option value="+855" <?php echo ($user_data['country_code'] === '+855') ? 'selected' : ''; ?>>🇰🇭 +855 (Cambodia)</option>
                                <option value="+856" <?php echo ($user_data['country_code'] === '+856') ? 'selected' : ''; ?>>🇱🇦 +856 (Laos)</option>
                            </select>
                            <input type="text" name="phone_number" id="phone" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>" pattern="^\d+$" title="Enter only digits">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <select name="specialization" id="specialization" <?php echo $user_data['role'] !== 'student' ? 'disabled' : ''; ?>>
                            <option value="" disabled>Select Your Specialization</option>
                            <option value="SE" <?php echo ($user_data['specialization'] ?? '') === 'SE' ? 'selected' : ''; ?>>Software Engineering</option>
                            <option value="DS" <?php echo ($user_data['specialization'] ?? '') === 'DS' ? 'selected' : ''; ?>>Data Science</option>
                            <option value="GD" <?php echo ($user_data['specialization'] ?? '') === 'GD' ? 'selected' : ''; ?>>Game Development</option>
                            <option value="CS" <?php echo ($user_data['specialization'] ?? '') === 'CS' ? 'selected' : ''; ?>>Cybersecurity</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="resetForm()">Cancel</button>
                    <button type="button" class="save-btn" onclick="updateProfile()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
    
    <script>
    // Store original values for reset functionality
    let originalData = {
        fullName: '<?php echo htmlspecialchars($user_data['full_name']); ?>',
        email: '<?php echo htmlspecialchars($user_data['email']); ?>',
        phoneNumber: '<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>',
        countryCode: '<?php echo htmlspecialchars($user_data['country_code'] ?? '+60'); ?>',
        specialization: '<?php echo htmlspecialchars($user_data['specialization'] ?? ''); ?>'
    };
    
    function resetForm() {
        document.getElementById('fullName').value = originalData.fullName;
        document.getElementById('email').value = originalData.email;
        document.getElementById('phone').value = originalData.phoneNumber;
        document.getElementById('country_code').value = originalData.countryCode;
        document.getElementById('specialization').value = originalData.specialization;
    }
    
    function validateEmail(email) {
        return email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
    }
    
    function validatePhoneNumber(countryCode, phoneNumber) {
        // Remove '+' from country code and combine with phone number
        const fullPhone = countryCode.substring(1) + phoneNumber;
        return fullPhone.match(/^\d{10,12}$/);
    }
    
    function updateProfile() {
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phoneNumber = document.getElementById('phone').value.trim();
        const countryCode = document.getElementById('country_code').value;
        const specialization = document.getElementById('specialization').value;
        
        // Validation
        if (!fullName) {
            alert('Full name is required');
            return;
        }
        
        if (!email || !validateEmail(email)) {
            alert('Please enter a valid email address');
            return;
        }
        
        if (!validatePhoneNumber(countryCode, phoneNumber)) {
            alert('Phone number including country code should be between 10-12 digits');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('full_name', fullName);
        formData.append('email', email);
        formData.append('country_code', countryCode);
        formData.append('phone_number', phoneNumber);
        formData.append('specialization', specialization);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update original data with new values
                originalData = {
                    fullName: data.data.full_name,
                    email: data.data.email,
                    phoneNumber: data.data.phone_number,
                    countryCode: data.data.country_code,
                    specialization: data.data.specialization
                };
                
                // Show success message
                alert(data.message);
            } else {
                alert(data.message || 'Failed to update profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile.');
        });
    }
    </script>
</body>
</html>
