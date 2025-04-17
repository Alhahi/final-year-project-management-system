<?php
require_once '../../auth/auth_check.php';
require_once '../../auth/role_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$hasAccess = checkStudentRole();

$conn = OpenCon();

require_once 'fetch-goals.php';
$goalsData = fetchGoals();

// Fetch total and completed goals
$query = "SELECT COUNT(*) AS total_goals, 
                 SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_goals
          FROM goals WHERE user_id = ?";
$stmt = $conn->prepare($query);
$user_id = $_SESSION['user_id'];
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$goal_data = $result->fetch_assoc();
$completed_goals = $goal_data['completed_goals'] ?? 0;
$total_goals = $goal_data['total_goals'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Progress</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./progress-page.css">
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
            margin-top: 50px;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
<?php echo getNavbar('progress'); ?>

<?php
// Display error message if exists
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']); // Clear the message after displaying
}
?>

<?php if ($hasAccess): ?>

<!-- Progress Section -->
<div class="section" id="progress">
    <h2>Progress Tracking</h2>
    <div class="progress-overview">
        <div class="progress-stats">
            <div class="stat-card">
                <h3>Completed Tasks</h3>
                <p class="stat-number"><?php echo $completed_goals; ?>/<?php echo $total_goals ?></p>
                <p class="stat-subtitle">Tasks Completed</p>
            </div>
        </div>
        <div class="project-timeline">
            <h3>Project Timeline</h3>
        </div>
    </div>

    <div class="weekly-goals">
        <div class="section-header">
            <h3>Weekly Goals</h3>
            <button class="add-goal-btn" id="addGoalBtn">
                <i class="fas fa-plus"></i> Add New Goal
            </button>
        </div>
        
        <div class="goals-container">
        <div class="goals-list" id="currentWeekGoals">
            <?php if (empty($goalsData)): ?>
                <p class="empty-message">No goals available. Start by adding a new goal!</p>
                <?php else: ?>
                    <?php foreach ($goalsData as $goal): ?>
                    <div class="goal-item">
                        <div class="goal-header">
                            <h5><?php echo $goal['goal']; ?></h5>
                            <span class="goal-priority priority-<?php echo $goal['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $goal['status'])); ?>
                            </span>
                        </div>
                        <div class="goal-footer">
                            <span class="goal-deadline"><i class="fas fa-calendar"></i> Due: <?php echo date('F j, Y', strtotime($goal['due_date'])); ?></span>
                            <button class="edit-goal-btn" data-goal-id="<?php echo $goal['goal_id']; ?>">Edit</button>
                            <button class="delete-goal-btn" data-goal-id="<?php echo $goal['goal_id']; ?>">Delete</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Edit Goal Modal -->
            <div class="modal" id="editGoalModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit Goal</h3>
                        <button class="close-btn">&times;</button>
                    </div>
                    <form id="editGoalForm" action="edit-goal.php" method="post">
                        <input type="hidden" id="editGoalId" name="goal_id">
                        <div class="form-group">
                            <label for="editGoalTitle">Goal Title</label>
                            <input type="text" id="editGoalTitle" name="goal" required placeholder="Enter goal title">
                        </div>
                        <div class="form-group">
                            <label for="editGoalDeadline">Deadline</label>
                            <input type="date" id="editGoalDeadline" name="due_date" required>
                        </div>
                        <div class="form-group">
                            <label for="editGoalStatus">Status</label>
                            <select id="editGoalStatus" name="status" required>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="save-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Goal Setting Modal -->
        <div class="modal" id="goalModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Set Weekly Goal</h3>
                    <button class="close-btn">&times;</button>
                </div>
                <form id="goalForm" action="create-goal.php" method="post">
                    <div class="form-group">
                        <label for="goalTitle">Goal Title</label>
                        <input type="text" id="goalTitle" name="goal" required placeholder="Enter goal title">
                    </div>
                    <div class="form-group">
                        <label for="goalDeadline">Deadline</label>
                        <input type="date" id="goalDeadline" name="due_date" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="save-btn">Create Goal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
<script>
document.getElementById('addGoalBtn').addEventListener('click', function() {
    document.getElementById('goalModal').style.display = 'block';
});

// Use delegation to handle close buttons correctly
document.querySelectorAll('.close-btn').forEach(function(closeBtn) {
    closeBtn.addEventListener('click', function() {
        // Close the modal that triggered the event
        this.closest('.modal').style.display = 'none';
    });
});

// Open edit modal for editing goal details
document.querySelectorAll('.edit-goal-btn').forEach(button => {
    button.addEventListener('click', function() {
        const goalId = this.getAttribute('data-goal-id');
        
        // Get the current goal data (goal title, deadline, status)
        const goalItem = this.closest('.goal-item');
        const title = goalItem.querySelector('h5').textContent;
        const deadline = goalItem.querySelector('.goal-deadline').textContent.split(": ")[1];
        const status = goalItem.querySelector('.goal-priority').textContent.toLowerCase().replace(" ", "_");

        // Pre-fill the form with the goal data
        document.getElementById('editGoalId').value = goalId;
        document.getElementById('editGoalTitle').value = title;
        // Ensure the deadline is in the correct format for <input type="date">
        const formattedDate = new Date(deadline).toISOString().split('T')[0]; // Convert to YYYY-MM-DD
        document.getElementById('editGoalDeadline').value = formattedDate;

        // Open the edit modal
        document.getElementById('editGoalModal').style.display = 'block';
    });
});

// Close the modal when clicking outside of the modal content area
window.addEventListener('click', function(event) {
    const editModal = document.getElementById('editGoalModal');
    const goalModal = document.getElementById('goalModal');
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
    if (event.target === goalModal) {
        goalModal.style.display = 'none';
    }
});

document.querySelectorAll('.delete-goal-btn').forEach(button => {
    button.addEventListener('click', function() {
        const goalId = this.getAttribute('data-goal-id');

        if (confirm("Are you sure you want to delete this goal?")) {
            fetch('delete-goal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `goal_id=${goalId}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes("Error")) {
                    alert("Failed to delete goal.");
                } else {
                    window.location.reload(); // Refresh page to reflect changes
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});


</script>

<footer>
    <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
</footer>
</html>
