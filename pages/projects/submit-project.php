<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';

$conn = OpenCon();

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['user_id'];
    $supervisor_id = $_POST['supervisor_id'];
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    
    // Insert the project directly
    $project_query = "INSERT INTO projects (student_id, supervisor_id, title, description, start_date, status) 
                     VALUES (?, ?, ?, ?, CURDATE(), 'ongoing')";
    $stmt = $conn->prepare($project_query);
    $stmt->bind_param("iiss", $student_id, $supervisor_id, $title, $description);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Project submitted successfully!";
        header("Location: projects-page.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error submitting project: " . $conn->error;
    }
}

// Get list of supervisors
$supervisor_query = "SELECT u.user_id, u.full_name, s.expertise 
                    FROM users u 
                    JOIN supervisors s ON u.user_id = s.supervisor_id";
$supervisors = $conn->query($supervisor_query);

// Check if user already has a project
$check_project_query = "SELECT p.*, u.full_name as supervisor_name 
                       FROM projects p 
                       LEFT JOIN users u ON p.supervisor_id = u.user_id 
                       WHERE p.student_id = ?";
$stmt = $conn->prepare($check_project_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$project_result = $stmt->get_result();
$existing_project = $project_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Project - FYP System</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
</head>
<body>

    <div class="section">
        <h2 style="color: #0066cc;">Submit Project</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <?php if ($existing_project): ?>
            <div class="alert alert-info">
                <h3>Existing Project</h3>
                <p>You have already submitted a project:</p>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($existing_project['title']); ?></p>
                <p><strong>Supervisor:</strong> <?php echo htmlspecialchars($existing_project['supervisor_name']); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($existing_project['status'])); ?></p>
            </div>
        <?php else: ?>
            <form method="POST" class="project-form">
                <div class="form-group">
                    <label for="supervisor">Select Supervisor:</label>
                    <select name="supervisor_id" required>
                        <option value="">Choose a supervisor</option>
                        <?php while ($supervisor = $supervisors->fetch_assoc()): ?>
                            <option value="<?php echo $supervisor['user_id']; ?>">
                                <?php echo htmlspecialchars($supervisor['full_name']); ?> 
                                (<?php echo htmlspecialchars($supervisor['expertise']); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Project Title:</label>
                    <input type="text" name="title" required maxlength="255">
                </div>
                
                <div class="form-group">
                    <label for="description">Project Description:</label>
                    <textarea name="description" required rows="6"></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Project</button>
            </form>
        <?php endif; ?>
    </div>

    <style>
        .section {
            padding: 20px;
            margin: 20px;
        }
        
        .project-form {
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
        .form-group select,
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
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            max-width: 800px;
            margin: 20px auto;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>
</body>
</html> 