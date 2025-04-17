<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole(); // Store the result of the role check
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$conn = OpenCon(); // Establish database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Supervisor Dashboard</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./supervisor-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
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
    unset($_SESSION['error_message']);
}
?>

<?php if ($hasAccess): ?>
<!-- Supervisor Dashboard -->
<div class="section" id="supervisorDashboard">
    <h2>Supervisor Dashboard</h2>
    <div class="dashboard-buttons">
        <button style="background-color: #0066cc; color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 20px 10px 30px 0; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#0052a3'" onmouseout="this.style.backgroundColor='#0066cc'" onclick="window.location.href='manage-proposals.php'">
            <i class="fas fa-tasks" style="font-size: 18px;"></i> Manage Student Proposals
        </button>
        <button style="background-color: #28a745; color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 20px 10px 30px 0; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#218838'" onmouseout="this.style.backgroundColor='#28a745'" onclick="window.location.href='manage-presentations.php'">
            <i class="fas fa-calendar-plus" style="font-size: 18px;"></i> Manage Presentation Slots
        </button>
        <button style="background-color: #dc3545; color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 20px 0 30px 0; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#c82333'" onmouseout="this.style.backgroundColor='#dc3545'" onclick="window.location.href='../projects/assess-projects.php'">
            <i class="fas fa-clipboard-check" style="font-size: 18px;"></i> Assess Projects
        </button>
    </div>
    <div class="dashboard-overview">
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Assigned Students</h3>
                <div class="stat-number">
                    <?php
                    $assigned_query = "SELECT COUNT(*) as count FROM projects WHERE supervisor_id = ?";
                    $stmt = $conn->prepare($assigned_query);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $count = $result->fetch_assoc()['count'];
                    echo $count;
                    ?>
                </div>
                <p>Active Projects</p>
            </div>
            <div class="stat-card">
                <h3>Pending Reviews</h3>
                <div class="stat-number">
                    <?php
                    $pending_query = "SELECT COUNT(*) as count 
                                    FROM projects p 
                                    LEFT JOIN assessments a ON p.project_id = a.project_id 
                                    WHERE p.supervisor_id = ? AND a.assessment_id IS NULL";
                    $stmt = $conn->prepare($pending_query);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $count = $result->fetch_assoc()['count'];
                    echo $count;
                    ?>
                </div>
                <p>Projects to Assess</p>
            </div>
            <div class="stat-card">
                <h3>Upcoming Meetings</h3>
                <div class="stat-number">3</div>
                <p>This Week</p>
            </div>
        </div>
        
        <div class="supervisor-actions">
            <div class="action-panel">
                <h3>Projects Pending Assessment</h3>
                <div class="project-list">
                    <?php
                    $projects_query = "SELECT p.*, u.full_name as student_name 
                                     FROM projects p 
                                     JOIN users u ON p.student_id = u.user_id 
                                     LEFT JOIN assessments a ON p.project_id = a.project_id 
                                     WHERE p.supervisor_id = ? AND a.assessment_id IS NULL";
                    $stmt = $conn->prepare($projects_query);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($project = $result->fetch_assoc()) {
                            echo '<div class="project-item">';
                            echo '<h4>' . htmlspecialchars($project['student_name']) . ' - ' . htmlspecialchars($project['title']) . '</h4>';
                            echo '<p>Submitted: ' . date('M d, Y', strtotime($project['start_date'])) . '</p>';
                            echo '<a href="../projects/assess-project.php?project_id=' . $project['project_id'] . '" class="assess-btn">Assess Project</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No projects pending assessment.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this new section for Presentation Slots -->
    <div class="presentation-slots-section">
        <h3>Recent Presentation Slots</h3>
        <div class="table-responsive">
            <table class="presentation-slots-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Student</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $supervisor_id = $_SESSION['user_id'];
                    $query = "SELECT ps.*, u.full_name 
                             FROM presentations_slots ps 
                             LEFT JOIN users u ON ps.user_id = u.user_id 
                             WHERE ps.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                             ORDER BY ps.slot_date ASC, ps.slot_time ASC 
                             LIMIT 5";
                    
                    $result = mysqli_query($conn, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status_class = $row['status'] === 'available' ? 'status-available' : 'status-booked';
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['slot_date']))) . "</td>";
                        echo "<td>" . htmlspecialchars(date('H:i', strtotime($row['slot_time']))) . "</td>";
                        echo "<td><span class='{$status_class}'>" . htmlspecialchars(ucfirst($row['status'])) . "</span></td>";
                        echo "<td>" . ($row['status'] === 'booked' ? htmlspecialchars($row['full_name']) : '-') . "</td>";
                        echo "<td>";
                        if ($row['status'] === 'available') {
                            echo "<button class='edit-slot-btn' onclick='editSlot(" . $row['presentation_slot_id'] . ")'><i class='fas fa-edit'></i></button>";
                            echo "<button class='delete-slot-btn' onclick='deleteSlot(" . $row['presentation_slot_id'] . ")'><i class='fas fa-trash'></i></button>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.dashboard-buttons {
    display: flex;
    gap: 10px;
}

.presentation-slots-section {
    margin-top: 30px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.presentation-slots-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.presentation-slots-table th,
.presentation-slots-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.presentation-slots-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.status-available {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.status-booked {
    background-color: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.edit-slot-btn,
.delete-slot-btn {
    padding: 6px 10px;
    margin: 0 4px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.edit-slot-btn {
    background-color: #ffc107;
    color: #000;
}

.delete-slot-btn {
    background-color: #dc3545;
    color: white;
}

.edit-slot-btn:hover {
    background-color: #e0a800;
}

.delete-slot-btn:hover {
    background-color: #c82333;
}

.assess-btn {
    display: inline-block;
    padding: 8px 16px;
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 10px;
}

.assess-btn:hover {
    background-color: #c82333;
}

.project-item {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    border-left: 4px solid #dc3545;
}

.project-item h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.project-item p {
    margin: 5px 0;
    color: #666;
}
</style>

<script>
function editSlot(slotId) {
    // Implement edit functionality
    window.location.href = `edit-presentation-slot.php?id=${slotId}`;
}

function deleteSlot(slotId) {
    if (confirm('Are you sure you want to delete this presentation slot?')) {
        // Implement delete functionality
        window.location.href = `delete-presentation-slot.php?id=${slotId}`;
    }
}
</script>

<?php endif; ?>