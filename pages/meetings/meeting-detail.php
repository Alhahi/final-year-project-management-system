<?php
session_start();
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

if (!isset($_GET['meeting_id'])) {
    echo "Meeting ID is required.";
    exit;
}

$meeting_id = $_GET['meeting_id'];

require_once "meetings-fn.php";

// Fetch meeting logs
$existing_log = fetchMeetingLog($meeting_id);
$log_exists = !empty($existing_log);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Log Details</title>
    <link rel="stylesheet" href="meeting-details.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="navbar">
        <a href="meetings-page.php">⬅ Back</a>
        <span>Meeting Log Details</span>
    </div>

    <div class="container">
        <?php if (!$log_exists && $_SESSION['role'] === 'student'): ?>
            <button class="schedule-btn" id="newMeetingLog">
                <i class="fas fa-plus"></i> New Meeting Log
            </button>
        <?php endif; ?>

        <div id="logModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <div class="panel-header">
                    <h3>New Meeting Log</h3>
                </div>
                <form id="meetingForm" action="create-meeting-log.php" method="post">
                    <input type="hidden" name="meeting_id" value="<?php echo $meeting_id; ?>">

                    <div class="form-group">
                        <label for="work_done">Work Done:</label>
                        <textarea name="work_done" required></textarea>

                        <label for="future_work">Future Work:</label>
                        <textarea name="future_work" required></textarea>

                        <label for="other">Problems Encountered & Solutions:</label>
                        <textarea name="other" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Create Meeting</button>
                </form>
            </div>
        </div>

        <?php if (!$log_exists): ?>
            <div class="no-logs-message">
                <h2>No Meeting Logs Found</h2>
            </div>
        <?php else: ?>
            <?php foreach ($existing_log as $log): ?>
                <div class="log-entry">
                    <h2>Meeting Log</h2>
                    <p><strong>Work Done:</strong> <span id="work_done_text"><?php echo nl2br(htmlspecialchars($log['work_done'])); ?></span></p>
                    <p><strong>Future Work:</strong> <span id="future_work_text"><?php echo nl2br(htmlspecialchars($log['future_work'])); ?></span></p>
                    <p><strong>Problems Encountered & Solutions:</strong> <span id="other_text"><?php echo nl2br(htmlspecialchars($log['other'])); ?></span></p>
                    <p><strong>Comments:</strong> <?php echo nl2br(htmlspecialchars($log['comments'])); ?></p>
                    <p><strong>Created At:</strong> <?php echo $log['created_at']; ?></p>

                    <?php if ($user_role === 'student'): ?>
                        <button class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($log)); ?>)">✏ Edit</button>
                    <?php endif; ?>

                    <?php if ($user_role === 'supervisor'): ?>
                        <form action="add-comment.php" method="POST">
                            <input type="hidden" name="log_id" value="<?php echo $log['meeting_log_id']; ?>">
                            <input type="hidden" name="meeting_id" id="meeting_id" value="<?php echo $meeting_id ?>">
                            <label for="comment">Add Comment:</label>
                            <textarea name="comment" required></textarea>
                            <button type="submit" class="submit-btn">Add Comment</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3>Edit Meeting Log</h3>
            <form id="editForm" action="update-meeting-log.php" method="POST">
                <input type="hidden" name="log_id" id="log_id">
                
                <label for="edit_work_done">Work Done:</label>
                <textarea name="work_done" id="edit_work_done" required></textarea>

                <label for="edit_future_work">Future Work:</label>
                <textarea name="future_work" id="edit_future_work" required></textarea>

                <label for="edit_other">Problems Encountered & Solutions:</label>
                <textarea name="other" id="edit_other" required></textarea>

                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const newMeetingLogBtn = document.getElementById("newMeetingLog");
            const logModal = document.getElementById("logModal");
            const closeBtn = document.querySelector("#logModal .close-btn");

            if (newMeetingLogBtn) {
                newMeetingLogBtn.addEventListener("click", function () {
                    logModal.style.display = "block";
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener("click", function () {
                    logModal.style.display = "none";
                });
            }

            // Close modal when clicking outside the modal content
            window.addEventListener("click", function (event) {
                if (event.target === logModal) {
                    logModal.style.display = "none";
                }
            });
        });

        function openEditModal(log) {
            document.getElementById("log_id").value = log.meeting_log_id;
            document.getElementById("edit_work_done").value = log.work_done;
            document.getElementById("edit_future_work").value = log.future_work;
            document.getElementById("edit_other").value = log.other;
            document.getElementById("editModal").style.display = "block";
        }

        function closeEditModal() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>

</body>
</html>
