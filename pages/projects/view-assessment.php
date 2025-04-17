<?php
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';
$conn = OpenCon();

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
$supervisor_id = $_SESSION['user_id'];

// Get project and assessment details
$query = "SELECT p.*, 
                 u.full_name as student_name,
                 a.total_marks,
                 a.project_planning,
                 a.technical_implementation,
                 a.documentation,
                 a.presentation,
                 a.feedback
          FROM projects p 
          JOIN users u ON p.student_id = u.user_id
          JOIN assessments a ON p.project_id = a.project_id
          WHERE p.project_id = ? AND p.supervisor_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $project_id, $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
$assessment = $result->fetch_assoc();

if (!$assessment) {
    $_SESSION['error_message'] = "Assessment not found or access denied.";
    header("Location: assess-projects.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assessment - FYP System</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
</head>
<body>
    
    <div class="section">
        <div class="assessment-header">
            <h2>Assessment Details</h2>
            <div class="project-info">
                <h3><?php echo htmlspecialchars($assessment['title']); ?></h3>
                <p>Student: <?php echo htmlspecialchars($assessment['student_name']); ?></p>
                <p>Submission Date: <?php echo date('d M Y', strtotime($assessment['start_date'])); ?></p>
            </div>
        </div>

        <div class="assessment-details">
            <div class="marks-section">
                <h3>Marks Breakdown</h3>
                <div class="marks-grid">
                    <div class="mark-item">
                        <label>Project Planning:</label>
                        <span><?php echo number_format($assessment['project_planning'], 1); ?>/25</span>
                    </div>
                    
                    <div class="mark-item">
                        <label>Technical Implementation:</label>
                        <span><?php echo number_format($assessment['technical_implementation'], 1); ?>/25</span>
                    </div>
                    
                    <div class="mark-item">
                        <label>Documentation:</label>
                        <span><?php echo number_format($assessment['documentation'], 1); ?>/25</span>
                    </div>
                    
                    <div class="mark-item">
                        <label>Presentation:</label>
                        <span><?php echo number_format($assessment['presentation'], 1); ?>/25</span>
                    </div>
                    
                    <div class="mark-item total">
                        <label>Total Marks:</label>
                        <span><?php echo number_format($assessment['total_marks'], 1); ?>/100</span>
                    </div>
                </div>
            </div>

            <div class="feedback-section">
                <h3>Feedback</h3>
                <div class="feedback-content">
                    <?php echo nl2br(htmlspecialchars($assessment['feedback'])); ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="assess-projects.php" class="back-btn">Back to Projects</a>
            </div>
        </div>
    </div>

    <style>
        .section {
            padding: 20px;
            margin: 20px;
        }

        .assessment-header {
            margin-bottom: 30px;
        }

        .project-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .project-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .project-info p {
            margin: 5px 0;
            color: #666;
        }

        .assessment-details {
            max-width: 800px;
            margin: 0 auto;
        }

        .marks-section,
        .feedback-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .marks-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .mark-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mark-item.total {
            grid-column: 1 / -1;
            background: #e9ecef;
            font-weight: bold;
        }

        .feedback-content {
            margin-top: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .action-buttons {
            margin-top: 30px;
            text-align: center;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</body>
</html> 
