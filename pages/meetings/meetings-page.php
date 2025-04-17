<?php
session_start();
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
require_once '../../auth/auth_check.php';
require_once '../../auth/role_check.php';
require_once '../../components/navbar.php';
require_once 'meetings-fn.php';

function calculateDuration($startTime, $endTime) {
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    $duration = $end - $start;

    return gmdate("H:i", $duration) . ' hours';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Meetings</title>
    <!-- <link rel="stylesheet" href="../../index.css"> -->
    <link rel="stylesheet" href="./meetings-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('meetings'); ?>
    <!-- Meetings Section -->
    <div class="section content-section" id="meetings">
        <h2>Meetings Management</h2>
        <div class="meetings-container">
            <div class="upcoming-meetings">
                <h3>Upcoming Meetings</h3>
                <?php
                if ($user_role === 'supervisor') {
                    echo '<button class="schedule-btn" id="scheduleNewMeeting">';
                    echo '<i class="fas fa-plus"></i> Schedule Meeting';
                    echo '</button>';
                }
                ?>
                
                <!-- Modal Container -->
                <div id="emailModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <div class="panel-header">
                            <h3>Schedule New Meeting</h3>
                        </div>
                        <form id="meetingForm" action="schedule-meeting.php" method="post">
                            <div class="form-group">
                                <label for="meetingTitle">Meeting Title:</label>
                                <input type="text" id="meetingTitle" name="meetingTitle" required>
                            </div>

                            <div class="form-group">
                                <div class="datetime-inputs">
                                    <label for="meetingDate">Date:</label>
                                    <input type="date" id="meetingDate" name="meetingDate" required>
                                    <label for="meetingStartTime">Start Time:</label>
                                    <input type="time" id="meetingStartTime" name="meetingStartTime" required>
                                    <label for="meetingEndTime">End Time:</label>
                                    <input type="time" id="meetingEndTime" name="meetingEndTime" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="meetingLocation">Location:</label>
                                <select id="meetingLocation" name="meetingLocation" required>
                                    <option value="">Select Location</option>
                                    <option value="Online (Google Meet)">Online (Google Meet)</option>
                                    <option value="Online (Zoom)">Online (Zoom)</option>
                                    <option value="FCI Meeting Room 1">FCI Meeting Room 1</option>
                                    <option value="FCI Meeting Room 2">FCI Meeting Room 2</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="meetingDescription">Description:</label>
                                <textarea id="meetingDescription" name="meetingDescription" rows="4"></textarea>
                            </div>                        
                            <button type="submit" class="submit-btn">Create Meeting</button>
                        </form>
                    </div>
                </div>
                <div class="meetings-list">
                <?php
                $upcomingMeetings = fetchMeetings('upcoming');
                if (!empty($upcomingMeetings)) {
                    foreach ($upcomingMeetings as $meeting) {
                        echo '<div class="meeting-item">';
                        echo '<div class="meeting-info">';
                        echo '<h4>' . htmlspecialchars($meeting['title']) . '</h4>';
                        echo '<p><i class="fas fa-user"></i> ' . htmlspecialchars($meeting['full_name']) . '</p>';
                        echo '<p><i class="fas fa-calendar"></i> ' . htmlspecialchars($meeting['date']);
                        echo '<p><i class="fas fa-clock"></i> ' . htmlspecialchars($meeting['start_time']) . ' - '.htmlspecialchars($meeting['end_time']).'</p>';
                        echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($meeting['venue']) . '</p>';
                        echo '<p><i class="fas fa-circle-info"></i> ' . htmlspecialchars($meeting['description']) . '</p>';
                        echo '</div>';
                        echo '<div class="meeting-actions">';
                        echo '<span class="meeting-status ' . htmlspecialchars($meeting['status']) . '">' . ucfirst($meeting['status']) . '</span>';
                        echo '<a href="meeting-detail.php?meeting_id='. $meeting['meeting_id'] .'">';
                        echo '<button class="action-btn view-notes-btn">View Meeting Log</button>';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No upcoming meetings found.</p>';
                }
                ?>
            </div>

            
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const scheduleButton = document.getElementById("scheduleNewMeeting");
    const modal = document.getElementById("emailModal");
    const closeModal = document.querySelector(".close-btn");
    const meetingForm = document.getElementById("meetingForm");
    const meetingDate = document.getElementById("meetingDate");
    const startTimeInput = document.getElementById("meetingStartTime");
    const endTimeInput = document.getElementById("meetingEndTime");

    // Open Modal when clicking "Schedule Meeting"
    if (scheduleButton) {
        scheduleButton.addEventListener("click", function () {
            modal.style.display = "block";
        });
    }

    // Close Modal when clicking "X" button
    if (closeModal) {
        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Close Modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Disable past dates in the date picker
    if (meetingDate) {
        let today = new Date().toISOString().split("T")[0];
        meetingDate.setAttribute("min", today);
    }

    // Validate start and end time
    function validateTime() {
        let startTime = startTimeInput.value;
        let endTime = endTimeInput.value;

        if (startTime && endTime && startTime >= endTime) {
            alert("End time must be later than start time.");
            endTimeInput.value = ""; // Reset end time input
        }
    }

    if (startTimeInput && endTimeInput) {
        startTimeInput.addEventListener("change", validateTime);
        endTimeInput.addEventListener("change", validateTime);
    }

    // Form Validation on Submit
    if (meetingForm) {
        meetingForm.addEventListener("submit", function (event) {
            let title = document.getElementById("meetingTitle").value.trim();
            let date = meetingDate.value;
            let startTime = startTimeInput.value;
            let endTime = endTimeInput.value;
            let location = document.getElementById("meetingLocation").value;

            if (!title || !date || !startTime || !endTime || !location) {
                alert("Please fill in all required fields.");
                event.preventDefault();
                return;
            }

            if (startTime >= endTime) {
                alert("End time must be later than start time.");
                event.preventDefault();
            }
        });
    }
});

</script>
</html>