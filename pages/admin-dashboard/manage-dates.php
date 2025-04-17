<?php
require_once '../../db_connection.php';
require_once '../../auth/role_check.php';
$hasAccess = checkAdminRole();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = OpenCon();

// Handle POST requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                $created_by = $_SESSION['user_id'];
                
                $sql = "INSERT INTO important_dates (title, description, date, created_by) 
                        VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $date, $created_by);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Date added successfully!";
                } else {
                    $_SESSION['error_message'] = "Error adding date: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
                break;

            case 'update':
                $id = mysqli_real_escape_string($conn, $_POST['id']);
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                
                $sql = "UPDATE important_dates SET title=?, description=?, date=? WHERE date_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $date, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Date updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating date: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
                break;

            case 'delete':
                $id = mysqli_real_escape_string($conn, $_POST['id']);
                $sql = "DELETE FROM important_dates WHERE date_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Date deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting date: " . mysqli_error($conn);
                }
                break;
        }
        header("Location: manage-dates.php");
        exit();
    }
}

// Fetch all important dates with creator information
$sql = "SELECT d.*, u.full_name as creator_name 
        FROM important_dates d 
        LEFT JOIN users u ON d.created_by = u.user_id 
        ORDER BY d.date ASC";
$result = mysqli_query($conn, $sql);

CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Important Dates - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link rel="stylesheet" href="./manage-dates.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="back-button">
        <a href="./admin-dashboard-page.php" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="section">
        <h2>Manage Important Dates</h2>
        
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="dashboard-container">
            <div class="action-panel">
                <div class="panel-header">
                    <h3>Important Dates</h3>
                    <button class="new-date-btn" id="newDateBtn">
                        <i class="fas fa-plus"></i> Add Date
                    </button>
                </div>

                <div class="dates-list">
                    <table class="dates-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['creator_name'] ?? 'Unknown'); ?></td>
                                    <td class="action-buttons">
                                        <button class="edit-btn" data-id="<?php echo $row['date_id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="delete-btn" data-id="<?php echo $row['date_id']; ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Date Modal -->
    <div class="modal" id="dateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Date</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form id="dateForm" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" id="date_id">

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('dateModal');
        const newBtn = document.getElementById('newDateBtn');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.querySelector('.cancel-btn');
        const form = document.getElementById('dateForm');

        newBtn.onclick = function() {
            form.reset();
            form.action.value = 'create';
            document.getElementById('modalTitle').textContent = 'Add New Date';
            modal.style.display = 'block';
        }

        closeBtn.onclick = cancelBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Edit functionality
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.onclick = function() {
                const id = this.dataset.id;
                fetch(`get-date.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('date_id').value = data.date_id;
                        document.getElementById('title').value = data.title;
                        document.getElementById('description').value = data.description;
                        document.getElementById('date').value = data.date;
                        form.action.value = 'update';
                        document.getElementById('modalTitle').textContent = 'Edit Date';
                        modal.style.display = 'block';
                    });
            }
        });

        // Delete functionality
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.onclick = function() {
                if (confirm('Are you sure you want to delete this date?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${this.dataset.id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    </script>
</body>
</html>