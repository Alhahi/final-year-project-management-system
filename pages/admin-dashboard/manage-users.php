<?php
require_once '../../auth/auth_check.php';
require_once "./fetch-users.php";

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add this array for country codes
$countryCodes = array(
    "+60" => "Malaysia (+60)",
    "+65" => "Singapore (+65)",
    "+62" => "Indonesia (+62)",
    "+66" => "Thailand (+66)",
    "+84" => "Vietnam (+84)",
    "+63" => "Philippines (+63)",
    "+95" => "Myanmar (+95)",
    "+855" => "Cambodia (+855)",
    "+856" => "Laos (+856)",
    "+673" => "Brunei (+673)"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Manage Users</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="back-button">
        <a href="./admin-dashboard-page.php" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="section">
        <h2>User Management</h2>
        
        <?php
        // Display success message if exists
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Clear the message
        }
        
        // Display error message if exists
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message
        }
        ?>

        <div class="dashboard-container">
            <div class="action-panel">
                <div class="panel-header">
                    <h3>All Users</h3>
                    <button class="new-user-btn" id="newUserBtn">
                        <i class="fas fa-plus"></i> Add New User
                    </button>
                </div>
                <div class="users-list">
                    <?php
                        $result = getAllUsers();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='user-item'>";
                            echo "<div class='user-header'>";
                            echo "<h4 class='username'><i class='fas fa-user'></i> ".$row['full_name']."</h4>";
                            echo "<p class='user-email'><i class='fas fa-envelope'></i> ".$row['email']."</p>";
                            echo "</div>";
                            echo "<div class='user-details'>";
                            echo "<span class='user-role'><i class='fas fa-user-tag'></i> Role: ".$row['role']."</span>";
                            
                            if ($row['role'] === 'student') {
                                echo "<span class='user-info'><i class='fas fa-id-card'></i> ".$row['matric_number']."</span>";
                                echo "<span class='user-info'><i class='fas fa-graduation-cap'></i> ".$row['course']."</span>";
                            } elseif ($row['additional_info']) {
                                $icon = $row['role'] === 'supervisor' ? 'fa-info-circle' : 'fa-user-shield';
                                echo "<span class='user-info'><i class='fas ".$icon."'></i> ".$row['additional_info']."</span>";
                            }
                            
                            echo "<span class='user-phone'><i class='fas fa-phone'></i> ".$row['phone_number']."</span>";
                            echo "<span class='user-date'><i class='fas fa-calendar-alt'></i> Joined: ".date('M d, Y', strtotime($row['created_at']))."</span>";
                            echo "</div>";
                            echo "<div class='user-actions'>";
                            echo "<button class='edit-btn' data-userid='".$row['user_id']."'><i class='fas fa-edit'></i> Edit</button>";
                            echo "<button class='delete-btn' data-userid='".$row['user_id']."'><i class='fas fa-trash'></i> Delete</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="addUserModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New User</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form id="addUserForm" method="POST" action="./add-user.php">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="country_code">Country Code</label>
                    <select id="country_code" name="country_code" required>
                        <?php
                        foreach ($countryCodes as $code => $country) {
                            $selected = ($code === "+60") ? "selected" : "";
                            echo "<option value=\"$code\" $selected>$country</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                    </select>
                    <small class="helper-text">For students, matric number and course are required.</small>
                    <small class="helper-text">For supervisors, expertise is required.</small>
                </div>
                <div id="studentFields" style="display: block;">
                    <div class="form-group">
                        <label for="matric_number">Matric Number</label>
                        <input type="text" 
                               id="matric_number" 
                               name="matric_number" 
                               required 
                               pattern="[0-9]{10}" 
                               placeholder="1211100123"
                               title="Please enter a valid 10-digit matric number">
                    </div>
                    <div class="form-group">
                        <label for="course">Course</label>
                        <select id="course" name="course" required>
                            <option value="">Select Course</option>
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Game Development">Game Development</option>
                        </select>
                    </div>
                </div>
                <div id="supervisorFields" style="display: none;">
                    <div class="form-group">
                        <label for="expertise">Expertise</label>
                        <textarea id="expertise" name="expertise"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Add User</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="editUserModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit User</h3>
                <span class="close-btn" id="editCloseBtn">&times;</span>
            </div>
            <form id="editUserForm" method="POST" action="./update-user.php">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="form-group">
                    <label for="edit_full_name">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit_phone_number">Phone Number</label>
                    <input type="tel" id="edit_phone_number" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="edit_country_code">Country Code</label>
                    <select id="edit_country_code" name="country_code" required>
                        <?php
                        foreach ($countryCodes as $code => $country) {
                            echo "<option value=\"$code\">$country</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select id="edit_role" name="role" required>
                        <option value="student">Student</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="edit_studentFields" style="display: none;">
                    <div class="form-group">
                        <label for="edit_matric_number">Matric Number</label>
                        <input type="text" 
                               id="edit_matric_number" 
                               name="matric_number"
                               pattern="[0-9]{10}" 
                               placeholder="1211100123"
                               title="Please enter a valid 10-digit matric number">
                    </div>
                    <div class="form-group">
                        <label for="edit_course">Course</label>
                        <select id="edit_course" name="course">
                            <option value="">Select Course</option>
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Game Development">Game Development</option>
                        </select>
                    </div>
                </div>
                <div id="edit_supervisorFields" style="display: none;">
                    <div class="form-group">
                        <label for="edit_expertise">Expertise</label>
                        <textarea id="edit_expertise" name="expertise"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" id="editCancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>

    <script>
    // Modal functionality
    const modal = document.getElementById('addUserModal');
    const addBtn = document.getElementById('newUserBtn');
    const closeBtn = document.querySelector('.close-btn');
    const cancelBtn = document.querySelector('.cancel-btn');
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('studentFields');
    const supervisorFields = document.getElementById('supervisorFields');

    addBtn.onclick = function() {
        modal.style.display = "block";
    }

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Show/hide role-specific fields
    roleSelect.onchange = function() {
        if (this.value === 'student') {
            studentFields.style.display = 'block';
            supervisorFields.style.display = 'none';
            // Make fields required for students
            document.getElementById('matric_number').required = true;
            document.getElementById('course').required = true;
        } else if (this.value === 'supervisor') {
            studentFields.style.display = 'none';
            supervisorFields.style.display = 'block';
            // Remove required attribute when not student
            document.getElementById('matric_number').required = false;
            document.getElementById('course').required = false;
        } else {
            studentFields.style.display = 'none';
            supervisorFields.style.display = 'none';
            // Remove required attribute when not student
            document.getElementById('matric_number').required = false;
            document.getElementById('course').required = false;
        }
    }

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this user?')) {
                const userId = this.dataset.userid;
                fetch('./delete-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert('Error deleting user');
                    }
                });
            }
        });
    });

    // Edit functionality
    const editModal = document.getElementById('editUserModal');
    const editCloseBtn = document.getElementById('editCloseBtn');
    const editCancelBtn = document.getElementById('editCancelBtn');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userid;
            fetch(`./get-user.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_full_name').value = data.full_name;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_phone_number').value = data.phone_number;
                    
                    // Set role in dropdown
                    const roleSelect = document.getElementById('edit_role');
                    for (let i = 0; i < roleSelect.options.length; i++) {
                        if (roleSelect.options[i].value === data.role) {
                            roleSelect.selectedIndex = i;
                            break;
                        }
                    }

                    // Update country code select
                    const countryCodeSelect = document.getElementById('edit_country_code');
                    for (let i = 0; i < countryCodeSelect.options.length; i++) {
                        if (countryCodeSelect.options[i].value === data.country_code) {
                            countryCodeSelect.selectedIndex = i;
                            break;
                        }
                    }

                    // Show/hide and populate role-specific fields
                    if (data.role === 'student') {
                        document.getElementById('edit_studentFields').style.display = 'block';
                        document.getElementById('edit_supervisorFields').style.display = 'none';
                        document.getElementById('edit_matric_number').value = data.matric_number;
                        
                        // Set course in dropdown
                        const courseSelect = document.getElementById('edit_course');
                        for (let i = 0; i < courseSelect.options.length; i++) {
                            if (courseSelect.options[i].value === data.course) {
                                courseSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        document.getElementById('edit_matric_number').required = true;
                        document.getElementById('edit_course').required = true;
                    } else if (data.role === 'supervisor') {
                        document.getElementById('edit_studentFields').style.display = 'none';
                        document.getElementById('edit_supervisorFields').style.display = 'block';
                        document.getElementById('edit_expertise').value = data.expertise;
                        document.getElementById('edit_matric_number').required = false;
                        document.getElementById('edit_course').required = false;
                    } else {
                        document.getElementById('edit_studentFields').style.display = 'none';
                        document.getElementById('edit_supervisorFields').style.display = 'none';
                        document.getElementById('edit_matric_number').required = false;
                        document.getElementById('edit_course').required = false;
                    }

                    editModal.style.display = 'block';
                });
        });
    });

    // Add role change handler for edit form
    document.getElementById('edit_role').addEventListener('change', function() {
        if (this.value === 'student') {
            document.getElementById('edit_studentFields').style.display = 'block';
            document.getElementById('edit_supervisorFields').style.display = 'none';
            document.getElementById('edit_matric_number').required = true;
            document.getElementById('edit_course').required = true;
        } else if (this.value === 'supervisor') {
            document.getElementById('edit_studentFields').style.display = 'none';
            document.getElementById('edit_supervisorFields').style.display = 'block';
            document.getElementById('edit_matric_number').required = false;
            document.getElementById('edit_course').required = false;
        } else {
            document.getElementById('edit_studentFields').style.display = 'none';
            document.getElementById('edit_supervisorFields').style.display = 'none';
            document.getElementById('edit_matric_number').required = false;
            document.getElementById('edit_course').required = false;
        }
    });

    editCloseBtn.onclick = function() {
        editModal.style.display = "none";
    }

    editCancelBtn.onclick = function() {
        editModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
    }

    // Add this after the existing script
    document.addEventListener('DOMContentLoaded', function() {
        // Since student is default role, make these fields required initially
        document.getElementById('matric_number').required = true;
        document.getElementById('course').required = true;
    });
    </script>
</body>
</html> 