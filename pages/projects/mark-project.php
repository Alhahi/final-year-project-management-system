<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$conn = OpenCon();

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
$supervisor_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_planning = floatval($_POST['project_planning']);
    $technical_implementation = floatval($_POST['technical_implementation']);
    $documentation = floatval($_POST['documentation']);
    $presentation = floatval($_POST['presentation']);
    $feedback = sanitize_input($_POST['feedback']);
    
    // Calculate total marks
    $total_marks = $project_planning + $technical_implementation + $documentation + $presentation;
    
    $query = "INSERT INTO assessments (project_id, title, total_marks, project_planning, 
              technical_implementation, documentation, presentation, feedback) 
              VALUES (?, 'Final Assessment', ?, ?, ?, ?, ?, ?)";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iddddds", $project_id, $total_marks, $project_planning, 
                      $technical_implementation, $documentation, $presentation, $feedback);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Assessment submitted successfully!";
        header("Location: assess-projects.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error submitting assessment: " . $conn->error;
    }
}

// Get project details
$project_query = "SELECT p.*, u.full_name as student_name 
                 FROM projects p 
                 JOIN users u ON p.student_id = u.user_id 
                 WHERE p.project_id = ? AND p.supervisor_id = ?";
$stmt = $conn->prepare($project_query);
$stmt->bind_param("ii", $project_id, $supervisor_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    $_SESSION['error_message'] = "Project not found or access denied.";
    header("Location: assess-projects.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Project - FYP System</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php echo getNavbar('supervisor'); ?>

    <?php if ($hasAccess): ?>
    <div class="section">
        <div class="assessment-header">
            <h2>Mark Project</h2>
            <div class="project-info">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <p>Student: <?php echo htmlspecialchars($project['student_name']); ?></p>
                <p>Submission Date: <?php echo date('d M Y', strtotime($project['start_date'])); ?></p>
            </div>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="assessment-form">
            <div class="criteria-section">
                <h3>Assessment Criteria</h3>
                <p class="criteria-note">Each section is marked out of 25 points. Total marks will be out of 100.</p>
                
                <div class="form-group">
                    <label>Project Planning</label>
                    <div class="criteria-description">
                        <p>Evaluate the project planning, objectives, timeline, and methodology.</p>
                    </div>
                    <input type="number" name="project_planning" required min="0" max="25" step="0.1">
                    <span class="max-marks">/25</span>
                </div>

                <div class="form-group">
                    <label>Technical Implementation</label>
                    <div class="criteria-description">
                        <p>Assess code quality, architecture, functionality, and technical achievement.</p>
                    </div>
                    <input type="number" name="technical_implementation" required min="0" max="25" step="0.1">
                    <span class="max-marks">/25</span>
                </div>

                <div class="form-group">
                    <label>Documentation</label>
                    <div class="criteria-description">
                        <p>Evaluate the clarity, completeness, and quality of project documentation.</p>
                    </div>
                    <input type="number" name="documentation" required min="0" max="25" step="0.1">
                    <span class="max-marks">/25</span>
                </div>

                <div class="form-group">
                    <label>Presentation</label>
                    <div class="criteria-description">
                        <p>Assess communication skills, demonstration quality, and presentation delivery.</p>
                    </div>
                    <input type="number" name="presentation" required min="0" max="25" step="0.1">
                    <span class="max-marks">/25</span>
                </div>
            </div>

            <div class="feedback-section">
                <h3>Feedback</h3>
                <div class="form-group">
                    <label>Detailed Feedback</label>
                    <textarea name="feedback" required rows="6" placeholder="Provide detailed feedback about the project, including strengths, areas for improvement, and specific recommendations."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit Assessment</button>
                <a href="assess-projects.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    <style>
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

    .assessment-form {
        max-width: 800px;
        margin: 0 auto;
    }

    .criteria-section,
    .feedback-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .criteria-note {
        color: #666;
        font-style: italic;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .criteria-description {
        margin-bottom: 10px;
        color: #666;
    }

    .form-group input {
        width: 100px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    .max-marks {
        margin-left: 10px;
        color: #666;
    }

    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .submit-btn,
    .cancel-btn {
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .submit-btn {
        background-color: #28a745;
        color: white;
        border: none;
    }

    .cancel-btn {
        background-color: #6c757d;
        color: white;
    }

    .submit-btn:hover {
        background-color: #218838;
    }

    .cancel-btn:hover {
        background-color: #5a6268;
    }

    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    </style>
    <?php endif; ?>
</body>
</html> 