<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$conn = OpenCon();

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

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
        header("Location: view-projects.php");
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
$stmt->bind_param("ii", $project_id, $_SESSION['user_id']);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    $_SESSION['error_message'] = "Project not found or access denied.";
    header("Location: view-projects.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assess Project - FYP System</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
</head>
<body>
    <?php echo getNavbar('supervisor'); ?>
    
    <?php if ($hasAccess): ?>
    <div class="section">
        <h2>Assess Project: <?php echo htmlspecialchars($project['title']); ?></h2>
        <p>Student: <?php echo htmlspecialchars($project['student_name']); ?></p>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="assessment-form">
            <div class="form-group">
                <label>Project Planning (out of 25):</label>
                <input type="number" name="project_planning" required min="0" max="25" step="0.1">
            </div>
            
            <div class="form-group">
                <label>Technical Implementation (out of 25):</label>
                <input type="number" name="technical_implementation" required min="0" max="25" step="0.1">
            </div>
            
            <div class="form-group">
                <label>Documentation (out of 25):</label>
                <input type="number" name="documentation" required min="0" max="25" step="0.1">
            </div>
            
            <div class="form-group">
                <label>Presentation (out of 25):</label>
                <input type="number" name="presentation" required min="0" max="25" step="0.1">
            </div>
            
            <div class="form-group">
                <label>Feedback:</label>
                <textarea name="feedback" required rows="6"></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Submit Assessment</button>
        </form>
    </div>
    <?php endif; ?>

    <style>
        .assessment-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</body>
</html> 
