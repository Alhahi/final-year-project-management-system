<?php
require_once '../../auth/auth_check.php';
require_once "./fetch-projects.php";

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Manage Projects</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="back-button">
        <a href="./admin-dashboard-page.php" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="section">
        <h2>Project Management</h2>
        
        <?php
        // Display success message if exists
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Clear the message
        }
        
        // Display error message if exists
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message
        }
        ?>

        <div class="dashboard-container">
            <!-- Proposals Table -->
            <div class="action-panel">
                <div class="panel-header">
                    <h3>Project Proposals</h3>
                    <button class="new-proposal-btn" id="newProposalBtn">
                        <i class="fas fa-file-alt"></i> Add Proposal
                    </button>
                </div>
                <div class="table-container">
                    <?php
                    $proposals = getAllProposals();
                    if (mysqli_num_rows($proposals) > 0) {
                        echo '<table class="project-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th>Supervisor</th>
                                    <th>Supervisor ID</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                        
                        while ($proposal = $proposals->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$proposal['title']."</td>";
                            echo "<td>".substr($proposal['description'], 0, 100)."..."."</td>";
                            echo "<td>".$proposal['student_name']."<br><small>".$proposal['student_email']."</small></td>";
                            echo "<td>".$proposal['student_id']."</td>";
                            echo "<td>".$proposal['supervisor_name']."<br><small>".$proposal['supervisor_email']."</small></td>";
                            echo "<td>".$proposal['supervisor_id']."</td>";
                            echo "<td><span class='status-badge ".$proposal['status']."'>".$proposal['status']."</span></td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' data-type='proposal' data-id='".$proposal['proposal_id']."'><i class='fas fa-edit'></i> Edit</button>";
                            echo "<button class='delete-btn' data-type='proposal' data-id='".$proposal['proposal_id']."'><i class='fas fa-trash'></i> Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <p>No proposals found</p>
                            <p>Click "Add Proposal" to get started</p>
                        </div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Projects Table -->
            <div class="action-panel">
                <div class="panel-header">
                    <h3>Active Projects</h3>
                    <button class="new-project-btn" id="newProjectBtn">
                        <i class="fas fa-plus"></i> Add Project
                    </button>
                </div>
                <div class="table-container">
                    <?php
                    $projects = getAllActiveProjects();
                    if (mysqli_num_rows($projects) > 0) {
                        echo '<table class="project-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th>Supervisor</th>
                                    <th>Supervisor ID</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                        
                        while ($project = $projects->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$project['title']."</td>";
                            echo "<td>".substr($project['description'], 0, 100)."..."."</td>";
                            echo "<td>".$project['student_name']."<br><small>".$project['student_email']."</small></td>";
                            echo "<td>".$project['student_id']."</td>";
                            echo "<td>".$project['supervisor_name']."<br><small>".$project['supervisor_email']."</small></td>";
                            echo "<td>".$project['supervisor_id']."</td>";
                            echo "<td><span class='status-badge ".$project['status']."'>".$project['status']."</span></td>";
                            echo "<td>".date('M d, Y', strtotime($project['start_date']))."</td>";
                            echo "<td>".date('M d, Y', strtotime($project['end_date']))."</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' data-type='project' data-id='".$project['project_id']."'><i class='fas fa-edit'></i> Edit</button>";
                            echo "<button class='delete-btn' data-type='project' data-id='".$project['project_id']."'><i class='fas fa-trash'></i> Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="empty-state">
                            <i class="fas fa-project-diagram"></i>
                            <p>No active projects found</p>
                            <p>Approve a proposal or click "Add Project" to get started</p>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before the footer -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Project/Proposal</h3>
                <span class="close-btn" id="editCloseBtn">&times;</span>
            </div>
            <form id="editForm" method="POST" action="update-project.php">
                <input type="hidden" id="edit_proposal_id" name="proposal_id">
                <input type="hidden" id="edit_project_id" name="project_id">
                
                <div class="form-group">
                    <label for="edit_title">Title</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_student_id">Student</label>
                    <select id="edit_student_id" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php
                        $conn = OpenCon();
                        $sql = "SELECT user_id, full_name, email FROM users WHERE role = 'student'";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='".$row['user_id']."' data-email='".$row['email']."'>".$row['full_name']." (".$row['email'].")</option>";
                        }
                        CloseCon($conn);
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_supervisor_id">Supervisor</label>
                    <select id="edit_supervisor_id" name="supervisor_id" required>
                        <option value="">Select Supervisor</option>
                        <?php
                        $conn = OpenCon();
                        $sql = "SELECT user_id, full_name, email FROM users WHERE role = 'supervisor'";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='".$row['user_id']."' data-email='".$row['email']."'>".$row['full_name']." (".$row['email'].")</option>";
                        }
                        CloseCon($conn);
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" required>
                        <?php
                        // Show different status options based on record type
                        echo '<span id="proposal_statuses" style="display:none;">';
                        echo '<option value="available">Available</option>';
                        echo '<option value="pending">Pending</option>';
                        echo '<option value="approved">Approved</option>';
                        echo '<option value="rejected">Rejected</option>';
                        echo '</span>';
                        
                        echo '<span id="project_statuses" style="display:none;">';
                        echo '<option value="ongoing">Ongoing</option>';
                        echo '<option value="completed">Completed</option>';
                        echo '<option value="failed">Failed</option>';
                        echo '</span>';
                        ?>
                    </select>
                </div>

                <div id="project_dates">
                    <div class="form-group">
                        <label for="edit_start_date">Start Date</label>
                        <input type="date" id="edit_start_date" name="start_date">
                    </div>

                    <div class="form-group">
                        <label for="edit_end_date">End Date</label>
                        <input type="date" id="edit_end_date" name="end_date">
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" id="editCancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>

    <script>
    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type; // 'proposal' or 'project'
            const id = this.dataset.id;
            const confirmMessage = type === 'proposal' ? 
                'Are you sure you want to delete this proposal?' : 
                'Are you sure you want to delete this project?';

            if (confirm(confirmMessage)) {
                fetch(`./delete-${type}.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `${type}_id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(`Error deleting ${type}: ${data.message}`);
                    }
                });
            }
        });
    });

    // Edit functionality
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            
            fetch(`./get-${type}.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    // Common fields
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_description').value = data.description;
                    
                    // Pre-select student and supervisor
                    const studentSelect = document.getElementById('edit_student_id');
                    const supervisorSelect = document.getElementById('edit_supervisor_id');
                    
                    // Find and select the student option
                    Array.from(studentSelect.options).forEach(option => {
                        if (option.value === data.student_id.toString()) {
                            option.selected = true;
                        }
                    });
                    
                    // Find and select the supervisor option
                    Array.from(supervisorSelect.options).forEach(option => {
                        if (option.value === data.supervisor_id.toString()) {
                            option.selected = true;
                        }
                    });
                    
                    // Set the form action based on type
                    document.getElementById('editForm').action = `update-${type}.php`;
                    
                    // Set the appropriate ID field
                    if (type === 'proposal') {
                        document.getElementById('edit_proposal_id').value = data.proposal_id;
                        document.getElementById('edit_project_id').value = '';
                        // Show/hide fields specific to proposals
                        document.getElementById('project_dates').style.display = 'none';
                        // Update status options for proposals
                        const proposalStatuses = ['available', 'pending', 'approved', 'rejected'];
                        const statusSelect = document.getElementById('edit_status');
                        statusSelect.innerHTML = ''; // Clear existing options
                        proposalStatuses.forEach(status => {
                            const option = document.createElement('option');
                            option.value = status;
                            option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            option.selected = status === data.status;
                            statusSelect.appendChild(option);
                        });
                    } else {
                        document.getElementById('edit_project_id').value = data.project_id;
                        document.getElementById('edit_proposal_id').value = '';
                        // Show/hide and fill fields specific to projects
                        document.getElementById('project_dates').style.display = 'block';
                        document.getElementById('edit_start_date').value = data.start_date;
                        document.getElementById('edit_end_date').value = data.end_date;
                        // Update status options for projects
                        const projectStatuses = ['ongoing', 'completed', 'failed'];
                        const statusSelect = document.getElementById('edit_status');
                        statusSelect.innerHTML = ''; // Clear existing options
                        projectStatuses.forEach(status => {
                            const option = document.createElement('option');
                            option.value = status;
                            option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            option.selected = status === data.status;
                            statusSelect.appendChild(option);
                        });
                    }

                    document.getElementById('editModal').style.display = 'block';
                });
        });
    });

    // Add modal close handlers
    document.getElementById('editCloseBtn').onclick = function() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById('editCancelBtn').onclick = function() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Add this to your existing script section
    document.getElementById('newProjectBtn').addEventListener('click', function() {
        window.location.href = './add-project.php';
    });

    document.getElementById('newProposalBtn').addEventListener('click', function() {
        window.location.href = './add-proposal.php';
    });

    // When loading proposal data
    function loadProposalData(data) {
        // ... existing field population code ...
        
        // Set the status dropdown to the current value
        document.getElementById('status').value = data.status;
        
        // ... rest of your code ...
    }
    </script>
</body>
</html> 