<?php
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole();
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Manage Presentations</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./supervisor-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php if ($hasAccess): ?>
    <div class="section">
        <!-- Back button -->
        <div class="back-section">
            <a href="supervisor-dashboard-page.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <h2>Manage Presentation Slots</h2>
        
        <!-- Add New Slot Form -->
        <div class="form-container">
            <h3>Add New Presentation Slot</h3>
            <form action="add-presentation-slot.php" method="POST" class="standard-form">
                <div class="form-group">
                    <label for="slot_date">Date:</label>
                    <input type="date" id="slot_date" name="slot_date" required>
                </div>
                
                <div class="form-group">
                    <label for="slot_time">Time:</label>
                    <input type="time" id="slot_time" name="slot_time" required>
                </div>
                
                <button type="submit" class="primary-button">
                    <i class="fas fa-plus"></i> Add Slot
                </button>
            </form>
        </div>

        <!-- Presentation Slots Table -->
        <div class="table-container">
            <h3>All Presentation Slots</h3>
            <div class="table-responsive">
                <table class="standard-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Student</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = OpenCon();
                        if (!$conn) {
                            echo '<tr><td colspan="5">Database connection failed</td></tr>';
                        } else {
                            $supervisor_id = $_SESSION['user_id'];
                            
                            // Modified query to only show slots created by the current supervisor
                            $query = "SELECT ps.*, u.full_name 
                                     FROM presentations_slots ps 
                                     LEFT JOIN users u ON ps.user_id = u.user_id 
                                     WHERE ps.supervisor_id = ? 
                                     ORDER BY ps.slot_date ASC, ps.slot_time ASC";
                            
                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "i", $supervisor_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = $row['status'] === 'available' ? 'status-available' : 'status-booked';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['slot_date']))); ?></td>
                                        <td><?php echo htmlspecialchars(date('h:i A', strtotime($row['slot_time']))); ?></td>
                                        <td>
                                            <select class="status-select" onchange="updateStatus(<?php echo $row['presentation_slot_id']; ?>, this.value)">
                                                <option value="available" <?php echo $row['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                                <option value="booked" <?php echo $row['status'] === 'booked' ? 'selected' : ''; ?>>Booked</option>
                                            </select>
                                        </td>
                                        <td><?php echo $row['status'] === 'booked' ? htmlspecialchars($row['full_name']) : '-'; ?></td>
                                        <td class="action-buttons">
                                            <button onclick="editSlot(<?php echo $row['presentation_slot_id']; ?>)" 
                                                    class="edit-button">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteSlot(<?php echo $row['presentation_slot_id']; ?>)" 
                                                    class="delete-button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" style="text-align: center;">No presentation slots found</td></tr>';
                            }
                            mysqli_stmt_close($stmt);
                            CloseCon($conn);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
    .section {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .standard-form {
        display: flex;
        gap: 20px;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .table-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .standard-table {
        width: 100%;
        border-collapse: collapse;
    }

    .standard-table th,
    .standard-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .standard-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
    }

    .status-available {
        background-color: #28a745;
        color: white;
    }

    .status-booked {
        background-color: #dc3545;
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .primary-button {
        background-color: #0066cc;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .primary-button:hover {
        background-color: #0052a3;
    }

    .edit-button {
        background-color: #ffc107;
        color: #000;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-button:hover {
        background-color: #e0a800;
    }

    .delete-button:hover {
        background-color: #c82333;
    }

    .status-select {
        padding: 6px 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
        background-color: white;
        cursor: pointer;
    }

    .status-select option[value="available"] {
        background-color: #28a745;
        color: white;
    }

    .status-select option[value="booked"] {
        background-color: #dc3545;
        color: white;
    }

    .back-section {
        margin-bottom: 20px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: #f8f9fa;
        color: #2c3e50;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        background-color: #e9ecef;
        color: #1a237e;
        transform: translateX(-5px);
    }

    .back-button i {
        font-size: 0.9rem;
    }
    </style>

    <script>
    function editSlot(slotId) {
        window.location.href = `edit-presentation-slot.php?id=${slotId}`;
    }

    function deleteSlot(slotId) {
        if (confirm('Are you sure you want to delete this presentation slot?')) {
            window.location.href = `delete-presentation-slot.php?id=${slotId}`;
        }
    }

    function updateStatus(slotId, newStatus) {
        if (confirm('Are you sure you want to update the status of this slot?')) {
            window.location.href = `update-slot-status.php?id=${slotId}&status=${newStatus}`;
        }
    }
    </script>

    <?php endif; ?>
</body>
</html> 