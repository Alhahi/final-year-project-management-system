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
                $content = mysqli_real_escape_string($conn, $_POST['content']);
                $sql = "INSERT INTO announcements (title, content) VALUES ('$title', '$content')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Announcement created successfully!";
                } else {
                    $_SESSION['error_message'] = "Error creating announcement: " . mysqli_error($conn);
                }
                break;

            case 'update':
                $id = mysqli_real_escape_string($conn, $_POST['id']);
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $content = mysqli_real_escape_string($conn, $_POST['content']);
                $sql = "UPDATE announcements SET title='$title', content='$content' WHERE announcement_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Announcement updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating announcement: " . mysqli_error($conn);
                }
                break;

            case 'delete':
                $id = mysqli_real_escape_string($conn, $_POST['id']);
                $sql = "DELETE FROM announcements WHERE announcement_id=$id";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Announcement deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting announcement: " . mysqli_error($conn);
                }
                break;
        }
        header("Location: manage-announcements.php");
        exit();
    }
}

// Fetch all announcements
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Close the database connection
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link rel="stylesheet" href="./manage-announcements.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="back-button">
        <a href="./admin-dashboard-page.php" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="section">
        <h2>Manage Announcements</h2>
        
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
                    <h3>Announcements</h3>
                    <button class="new-announcement-btn" id="newAnnouncementBtn">
                        <i class="fas fa-plus"></i> Add Announcement
                    </button>
                </div>

                <div class="announcements-list">
                    <table class="announcement-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Created At</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['updated_at'])); ?></td>
                                    <td class="action-buttons">
                                        <button class="edit-btn" data-id="<?php echo $row['announcement_id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="delete-btn" data-id="<?php echo $row['announcement_id']; ?>">
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

    <!-- Add/Edit Announcement Modal -->
    <div class="modal" id="announcementModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Announcement</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form id="announcementForm" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" id="announcement_id">

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="4" required></textarea>
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
        const modal = document.getElementById('announcementModal');
        const newBtn = document.getElementById('newAnnouncementBtn');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.querySelector('.cancel-btn');
        const form = document.getElementById('announcementForm');

        newBtn.onclick = function() {
            form.reset();
            form.action.value = 'create';
            document.getElementById('modalTitle').textContent = 'Add New Announcement';
            modal.style.display = 'block';
        }

        closeBtn.onclick = cancelBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Edit functionality
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.onclick = function() {
                const id = this.dataset.id;
                fetch(`get-announcement.php?id=${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            return;
                        }
                        document.getElementById('announcement_id').value = data.announcement_id;
                        document.getElementById('title').value = data.title;
                        document.getElementById('content').value = data.content;
                        form.action.value = 'update';
                        document.getElementById('modalTitle').textContent = 'Edit Announcement';
                        modal.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching announcement details');
                    });
            }
        });

        // Delete functionality
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.onclick = function() {
                if (confirm('Are you sure you want to delete this announcement?')) {
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