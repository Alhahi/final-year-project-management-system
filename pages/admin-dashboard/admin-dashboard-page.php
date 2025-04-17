<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkAdminRole(); // Store the result of the role check
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';

// Get database counts
$conn = OpenCon();

// Count total students
$students_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'student'";
$students_result = mysqli_query($conn, $students_sql);
$students_count = mysqli_fetch_assoc($students_result)['count'];

// Count total supervisors
$supervisors_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'supervisor'";
$supervisors_result = mysqli_query($conn, $supervisors_sql);
$supervisors_count = mysqli_fetch_assoc($supervisors_result)['count'];

// Count total admin proposals
$proposals_sql = "SELECT COUNT(*) as count FROM project_proposals";
$proposals_result = mysqli_query($conn, $proposals_sql);
$proposals_count = mysqli_fetch_assoc($proposals_result)['count'];

// Count supervisor proposals
$supervisor_proposals_sql = "SELECT COUNT(*) as count FROM supervisor_proposals";
$supervisor_proposals_result = mysqli_query($conn, $supervisor_proposals_sql);
$supervisor_proposals_count = mysqli_fetch_assoc($supervisor_proposals_result)['count'];

// Count available proposals
$available_sql = "SELECT COUNT(*) as count FROM project_proposals WHERE status = 'available'";
$available_result = mysqli_query($conn, $available_sql);
$available_count = mysqli_fetch_assoc($available_result)['count'];

CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Admin Dashboard</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
    <style>
        .alert {
            padding: 15px;
            margin: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>

<div class="back-button">
        <a href="../../index.php" title="Back to Home Page">
            <i class="fas fa-arrow-left"></i> Back to Home Page
        </a>
</div>

<?php
// Display error message if exists
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']); // Clear the message after displaying
}
?>

<?php if ($hasAccess): ?>
<!-- Admin Dashboard -->
<div class="section" id="adminDashboard">
    <h2>Admin Dashboard</h2>
    <div class="dashboard-overview">
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number"><?php echo $students_count; ?></div>
                <p>Registered Students</p>
            </div>
            <div class="stat-card">
                <h3>Total Supervisors</h3>
                <div class="stat-number"><?php echo $supervisors_count; ?></div>
                <p>Active Supervisors</p>
            </div>
            <div class="stat-card">
                <h3>Admin Proposals</h3>
                <div class="stat-number"><?php echo $proposals_count; ?></div>
                <p>Total Admin Proposals</p>
            </div>
            <div class="stat-card">
                <h3>Supervisor Proposals</h3>
                <div class="stat-number"><?php echo $supervisor_proposals_count; ?></div>
                <p>Direct to Supervisors</p>
            </div>
            <div class="stat-card">
                <h3>Available Proposals</h3>
                <div class="stat-number"><?php echo $available_count; ?></div>
                <p>Open for Students</p>
            </div>
        </div>
        
        <div class="admin-actions">
            <div class="action-panel">
                <h3>System Management</h3>
                <div class="admin-controls">
                    <a href="./manage-users.php" class="admin-btn" id="manageUsersBtn">
                        <i class="fas fa-user-plus"></i> Manage Users
                    </a>
                    <a href="./manage-projects.php" class="admin-btn">
                        <i class="fas fa-project-diagram"></i> Manage Projects
                    </a>
                    <a href="./manage-announcements.php" class="admin-btn">
                        <i class="fas fa-bullhorn"></i> Manage Announcements
                    </a>
                    <a href="./manage-dates.php" class="admin-btn">
                        <i class="fas fa-calendar"></i> Manage Important Dates
                    </a>
                </div>
            </div>
        </div>

        <!-- Add this new section for user management -->
        <div class="user-management-panel" style="display: none;">
            <div class="action-panel">
                <div class="panel-header">
                    <h3>User Management</h3>
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
                            if ($row['additional_info']) {
                                $icon = $row['role'] === 'student' ? 'fa-id-card' : 'fa-info-circle';
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

            <!-- Add/Edit User Modal -->
            <div class="modal" id="userModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Add New User</h3>
                        <button class="close-btn">&times;</button>
                    </div>
                    <form id="userForm">
                        <div class="form-group">
                            <label for="userName">Full Name</label>
                            <input type="text" id="userName" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">Email</label>
                            <input type="email" id="userEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="userRole">Role</label>
                            <select id="userRole" required>
                                <option value="student">Student</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="userStatus">Status</label>
                            <select id="userStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="userPassword">Password</label>
                            <input type="password" id="userPassword" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<footer>
    <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
</footer>
</body>
</html>
