<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
$conn = OpenCon();

$supervisor_id = $_SESSION['user_id'];

// Get all projects assigned to this supervisor
$query = "SELECT p.*, u.full_name as student_name,
          CASE 
              WHEN a.assessment_id IS NOT NULL THEN 'Assessed'
              ELSE 'Pending Assessment'
          END as assessment_status,
          a.total_marks
          FROM projects p 
          JOIN users u ON p.student_id = u.user_id 
          LEFT JOIN assessments a ON p.project_id = a.project_id
          WHERE p.supervisor_id = ?
          ORDER BY p.start_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assess Projects - FYP System</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php echo getNavbar('supervisor'); ?>

    <?php if ($hasAccess): ?>
    <div class="section">
        <h2>Assess Student Projects</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="projects-list">
            <table class="assessment-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Project Title</th>
                        <th>Submission Date</th>
                        <th>Status</th>
                        <th>Marks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($project = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                <td><?php echo date('d M Y', strtotime($project['start_date'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $project['assessment_status'])); ?>">
                                        <?php echo $project['assessment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($project['total_marks']) {
                                        echo number_format($project['total_marks'], 1) . '/100';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($project['assessment_status'] === 'Pending Assessment'): ?>
                                        <a href="mark-project.php?project_id=<?php echo $project['project_id']; ?>" 
                                           class="assess-btn">
                                            <i class="fas fa-clipboard-check"></i> Mark Project
                                        </a>
                                    <?php else: ?>
                                        <a href="view-assessment.php?project_id=<?php echo $project['project_id']; ?>" 
                                           class="view-btn">
                                            <i class="fas fa-eye"></i> View Assessment
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-projects">No projects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <style>
    .assessment-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .assessment-table th,
    .assessment-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .assessment-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.9em;
    }

    .status-badge.assessed {
        background-color: #28a745;
        color: white;
    }

    .status-badge.pending-assessment {
        background-color: #ffc107;
        color: #000;
    }

    .assess-btn,
    .view-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9em;
    }

    .assess-btn {
        background-color: #dc3545;
        color: white;
    }

    .view-btn {
        background-color: #17a2b8;
        color: white;
    }

    .assess-btn:hover {
        background-color: #c82333;
    }

    .view-btn:hover {
        background-color: #138496;
    }

    .no-projects {
        text-align: center;
        padding: 20px;
        color: #666;
    }

    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
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
