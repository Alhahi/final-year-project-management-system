<?php
session_start();
require_once "../../db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="back-button">
        <a href="./manage-projects.php" title="Back to Project Management">
            <i class="fas fa-arrow-left"></i> Back to Project Management
        </a>
    </div>

    <div class="section">
        <h2>Add New Project</h2>
        <div class="dashboard-container">
            <div class="action-panel">
                <form method="POST" action="save-project.php">
                    <div class="form-group">
                        <label for="title">Project Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Project Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="student_id">Student</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">Select Student</option>
                            <?php
                            $conn = OpenCon();
                            $sql = "SELECT user_id, full_name, email FROM users WHERE role = 'student'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='".$row['user_id']."'>".$row['full_name']." (".$row['email'].")</option>";
                            }
                            CloseCon($conn);
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="supervisor_id">Supervisor</label>
                        <select id="supervisor_id" name="supervisor_id" required>
                            <option value="">Select Supervisor</option>
                            <?php
                            $conn = OpenCon();
                            $sql = "SELECT user_id, full_name, email FROM users WHERE role = 'supervisor'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='".$row['user_id']."'>".$row['full_name']." (".$row['email'].")</option>";
                            }
                            CloseCon($conn);
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">Expected End Date</label>
                        <input type="date" id="end_date" name="end_date" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="save-btn">Create Project</button>
                        <a href="manage-projects.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
</body>
</html> 