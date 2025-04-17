<?php
require_once '../../auth/auth_check.php';
require_once '../../auth/role_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$hasAccess = checkStudentRole();

$conn = OpenCon();

// Get user's role and ID
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get assessment data based on role
if ($role === 'student') {
    $query = "SELECT p.title as project_title, 
                     a.total_marks, 
                     a.project_planning, 
                     a.technical_implementation, 
                     a.documentation, 
                     a.presentation, 
                     a.feedback,
                     CURRENT_TIMESTAMP as assessment_date,
                     u.full_name as supervisor_name
              FROM projects p
              JOIN users u ON p.supervisor_id = u.user_id
              LEFT JOIN assessments a ON p.project_id = a.project_id
              WHERE p.student_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assessment = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Assessment</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./assessment-page.css">
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
            margin-top: 100px;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
<?php echo getNavbar('assessment'); ?>

<?php
// Display error message if exists
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']); // Clear the message after displaying
}
?>

<?php if ($hasAccess): ?>

<!-- Assessment Section -->
<div class="section" id="assessment">
    <h2>Assessment Dashboard</h2>
    
    <?php if ($role === 'student'): ?>
        <?php if ($assessment): ?>
            <div class="assessment-overview">
                <!-- Assessment Summary Card -->
                <div class="assessment-cards">
                    <div class="assessment-card">
                        <h3><?php echo htmlspecialchars($assessment['project_title']); ?></h3>
                        <?php if ($assessment['total_marks']): ?>
                            <div class="score-display">
                                <span class="score"><?php echo number_format($assessment['total_marks'], 1); ?></span>/100
                            </div>
                            <div class="assessment-details">
                                <p class="assessment-date">Submitted: <?php echo date('F j, Y', strtotime($assessment['assessment_date'])); ?></p>
                                <p class="assessment-status completed">Completed</p>
                            </div>
                        <?php else: ?>
                            <div class="score-display">
                                <span class="score pending">Pending</span>
                            </div>
                            <div class="assessment-details">
                                <p class="assessment-status upcoming">Not Assessed Yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($assessment['total_marks']): ?>
                    <!-- Detailed Rubric Section -->
                    <div class="rubric-container">
                        <h3>Project Assessment Breakdown</h3>
                        <div class="rubric-section">
                            <div class="rubric-item">
                                <div class="criteria">
                                    <h4>Project Planning</h4>
                                    <p>Clear objectives, timeline, and methodology</p>
                                </div>
                                <div class="score-breakdown">
                                    <span class="points"><?php echo number_format($assessment['project_planning'], 1); ?>/25</span>
                                </div>
                            </div>

                            <div class="rubric-item">
                                <div class="criteria">
                                    <h4>Technical Implementation</h4>
                                    <p>Code quality, architecture, and functionality</p>
                                </div>
                                <div class="score-breakdown">
                                    <span class="points"><?php echo number_format($assessment['technical_implementation'], 1); ?>/25</span>
                                </div>
                            </div>

                            <div class="rubric-item">
                                <div class="criteria">
                                    <h4>Documentation</h4>
                                    <p>Clear and comprehensive documentation</p>
                                </div>
                                <div class="score-breakdown">
                                    <span class="points"><?php echo number_format($assessment['documentation'], 1); ?>/25</span>
                                </div>
                            </div>

                            <div class="rubric-item">
                                <div class="criteria">
                                    <h4>Presentation</h4>
                                    <p>Communication and demonstration skills</p>
                                </div>
                                <div class="score-breakdown">
                                    <span class="points"><?php echo number_format($assessment['presentation'], 1); ?>/25</span>
                                </div>
                            </div>

                            <div class="total-marks">
                                <h4>Total Score: <?php echo number_format($assessment['total_marks'], 1); ?>/100</h4>
                            </div>
                        </div>

                        <!-- Feedback Section -->
                        <div class="feedback-section">
                            <h3>Supervisor Feedback</h3>
                            <div class="feedback-content">
                                <p class="feedback-text"><?php echo nl2br(htmlspecialchars($assessment['feedback'])); ?></p>
                                <p class="feedback-author">- <?php echo htmlspecialchars($assessment['supervisor_name']); ?></p>
                                <p class="feedback-date"><?php echo date('F j, Y', strtotime($assessment['assessment_date'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-project">
                <p>No project found. Please submit a project first.</p>
                <a href="../projects/submit-project.php" class="submit-project-btn">Submit Project</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php endif; ?>
<footer>
    <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
</footer>

</body>
</html>
