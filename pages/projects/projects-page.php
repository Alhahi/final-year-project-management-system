<?php
session_start();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./projects-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
    <?php echo getNavbar('projects'); ?>

    <div class="section">
        <div class="page-header">
            <h2>Project Management</h2>
        </div>
        
        <div class="project-actions-container">
            <!-- Submit Project Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <h4>Submit Final Project</h4>
                <p>Submit your completed project for supervisor assessment.</p>
                <button class="action-btn" onclick="window.location.href='submit-project.php'">
                    Submit Project
                </button>
            </div>

            <!-- Submit to Supervisor Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h4>Submit Proposal to Supervisor</h4>
                <p>Submit your project proposal directly to a supervisor for review.</p>
                <button class="action-btn" onclick="openModal('supervisorModal')">
                    Submit Proposal
                </button>
            </div>

            <!-- Submit to Admin Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h4>Submit Proposal to Admin</h4>
                <p>Submit your project proposal to admin.</p>
                <button class="action-btn" onclick="openModal('adminModal')">
                    Submit Proposal
                </button>
            </div>

            <!-- View Available Projects Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h4>Available Project Proposals</h4>
                <p>View the list of available project proposals.</p>
                <button class="action-btn" onclick="window.location.href='view-projects.php'">
                    View Proposals
                </button>
            </div>
        </div>

        <!-- Add the presentation slots section here -->
        <div class="panel-header">
            <h3>Available Presentation Slots</h3>
        </div>
        <div class="presentation-slots-section">
            <div class="table-responsive">
                <?php
                $conn = OpenCon();
                $user_id = $_SESSION['user_id'];

                // Check if student has already booked a slot
                $check_booking = "SELECT ps.*, u.full_name as supervisor_name 
                                 FROM presentations_slots ps 
                                 LEFT JOIN users u ON ps.supervisor_id = u.user_id 
                                 WHERE ps.user_id = ? AND ps.status = 'booked'";
                $stmt = mysqli_prepare($conn, $check_booking);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $booked_result = mysqli_stmt_get_result($stmt);
                $has_booking = mysqli_fetch_assoc($booked_result);

                if ($has_booking) {
                    echo "<div class='booked-slot-section'>
                            <div class='section-header'>
                                <h4>Your Presentation Schedule</h4>
                            </div>
                            <table class='submission-table'>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Supervisor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>" . date('l, d M Y', strtotime($has_booking['slot_date'])) . "</td>
                                        <td>" . date('h:i A', strtotime($has_booking['slot_time'])) . "</td>
                                        <td>" . htmlspecialchars($has_booking['supervisor_name']) . "</td>
                                        <td><span class='status-badge confirmed'>Confirmed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                          </div>";
                } else {
                    // Show available slots with supervisor names
                    $query = "SELECT ps.*, u.full_name as supervisor_name 
                             FROM presentations_slots ps 
                             LEFT JOIN users u ON ps.supervisor_id = u.user_id 
                             WHERE ps.status = 'available' 
                             AND ps.slot_date >= CURDATE()
                             ORDER BY ps.slot_date ASC, ps.slot_time ASC";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        ?>
                        <table class="submission-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Supervisor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($slot = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($slot['slot_date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($slot['slot_time'])); ?></td>
                                        <td><?php echo htmlspecialchars($slot['supervisor_name'] ?? 'Not Assigned'); ?></td>
                                        <td>
                                            <button onclick="bookSlot(<?php echo $slot['presentation_slot_id']; ?>)" 
                                                    class="action-btn">
                                                <i class="fas fa-calendar-check"></i> Book Slot
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo "<div class='alert alert-warning'>No presentation slots available at the moment.</div>";
                    }
                }
                CloseCon($conn);
                ?>
            </div>
        </div>

        <!-- Project Submissions -->
        <div class="panel-header">
            <h3>My Proposals</h3>
        </div>
        <table class="submission-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Submission Date</th>
                    <th>Submitted To</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = OpenCon();
                $student_id = $_SESSION['user_id'];
                
                // Get supervisor proposals
                $supervisor_sql = "SELECT sp.*, u.full_name as supervisor_name 
                                 FROM supervisor_proposals sp 
                                 JOIN users u ON sp.supervisor_id = u.user_id 
                                 WHERE sp.student_id = ?";
                
                $stmt = $conn->prepare($supervisor_sql);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $supervisor_result = $stmt->get_result();

                // Get admin proposals
                $admin_sql = "SELECT pp.*, u.full_name as supervisor_name 
                            FROM project_proposals pp 
                            LEFT JOIN users u ON pp.supervisor_id = u.user_id 
                            WHERE pp.student_id = ?";
                
                $stmt = $conn->prepare($admin_sql);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $admin_result = $stmt->get_result();

                // Display supervisor proposals
                while ($row = $supervisor_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['submitted_at']) . "</td>";
                    echo "<td>Supervisor: " . htmlspecialchars($row['supervisor_name']) . "</td>";
                    echo "<td>";
                    $status_class = '';
                    switch($row['status']) {
                        case 'pending':
                            $status_class = 'status-badge pending';
                            break;
                        case 'approved':
                            $status_class = 'status-badge approved';
                            break;
                        case 'rejected':
                            $status_class = 'status-badge rejected';
                            break;
                    }
                    echo "<span class='" . $status_class . "'>" . ucfirst($row['status']) . " by Supervisor</span>";
                    echo "</td>";
                    echo "</tr>";
                }

                // Display admin proposals
                while ($row = $admin_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['submitted_at']) . "</td>";
                    echo "<td>Admin" . ($row['supervisor_name'] ? " (Preferred: " . htmlspecialchars($row['supervisor_name']) . ")" : "") . "</td>";
                    echo "<td>";
                    $status_class = '';
                    switch($row['status']) {
                        case 'pending':
                            $status_class = 'status-badge pending';
                            break;
                        case 'approved':
                            $status_class = 'status-badge approved';
                            break;
                        case 'rejected':
                            $status_class = 'status-badge rejected';
                            break;
                        case 'available':
                            $status_class = 'status-badge active';
                            break;
                    }
                    echo "<span class='" . $status_class . "'>" . ucfirst($row['status']) . " by Admin</span>";
                    echo "</td>";
                    echo "</tr>";
                }
                CloseCon($conn);
                ?>
            </tbody>
        </table>
    </div>

    <!-- Supervisor Submission Modal -->
    <div id="supervisorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Submit Proposal to Supervisor</h3>
                <span class="close" onclick="closeModal('supervisorModal')">&times;</span>
            </div>
            <form action="submit-proposal.php" method="POST">
                <input type="hidden" name="submission_type" value="supervisor">
                
                <div class="form-group">
                    <label for="supervisor">Select Supervisor</label>
                    <select id="supervisor" name="supervisor_id" required>
                        <option value="">Choose a supervisor</option>
                        <?php
                        $conn = OpenCon();
                        $sql = "SELECT u.user_id, u.full_name, u.email 
                               FROM users u 
                               JOIN supervisors s ON u.user_id = s.supervisor_id 
                               WHERE u.role = 'supervisor'";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='".$row['user_id']."'>".$row['full_name']." (".$row['email'].")</option>";
                        }
                        CloseCon($conn);
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Project Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Submit Proposal</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('supervisorModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Submission Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Submit Proposal to Admin</h3>
                <span class="close" onclick="closeModal('adminModal')">&times;</span>
            </div>
            <form action="submit-proposal.php" method="POST">
                <input type="hidden" name="submission_type" value="admin">
                
                <div class="form-group">
                    <label for="admin_supervisor">Preferred Supervisor</label>
                    <select id="admin_supervisor" name="supervisor_id" required>
                        <option value="">Choose a supervisor</option>
                        <?php
                        $conn = OpenCon();
                        $sql = "SELECT u.user_id, u.full_name, u.email 
                               FROM users u 
                               JOIN supervisors s ON u.user_id = s.supervisor_id 
                               WHERE u.role = 'supervisor'";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='".$row['user_id']."'>".$row['full_name']." (".$row['email'].")</option>";
                        }
                        CloseCon($conn);
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="admin_title">Project Title</label>
                    <input type="text" id="admin_title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="admin_description">Project Description</label>
                    <textarea id="admin_description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Submit Proposal</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('adminModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }

    function bookSlot(slotId) {
        if (confirm('Are you sure you want to book this presentation slot?')) {
            window.location.href = `book-presentation-slot.php?id=${slotId}`;
        }
    }
    </script>
</body>
</html>
