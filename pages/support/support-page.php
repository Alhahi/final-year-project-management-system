<?php
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../components/form/feedback-form.php';
require_once "./fetch-feedback.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Support</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./support-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('support'); ?>

<!-- Support Section -->
<div class="section" id="support">
    <h2>Services & Support</h2>
    <div class="support-container">
        <!-- Support Categories -->
        <div class="support-categories">
            <div class="category-card">
                <i class="fas fa-book"></i>
                <h3>Documentation</h3>
                <p>Access user guides and documentation</p>
                <a href="../guidelines/project-guidelines.php" class="documentation-btn">Guidelines</a>
            </div>
            <div class="category-card">
                <i class="fas fa-users"></i>
                <h3>Community Help</h3>
                <p>Connect with other students and share solutions</p>
                <a href="../communication/forum.php" class="community-help-btn">Join Forum</a>
            </div>
        </div>

        <!-- Previous Feedback -->
        <div class="action-panel">
            <div class="panel-header">
                <h3>Feedbacks</h3>
                <button class="new-feedback-btn" id="newFeedbackBtn">
                    <i class="fas fa-plus"></i> New Feedback
                </button>
            </div>
            <div class="feedback-list">
                <?php
                    $result = getAllFeedbacks();
                    if ($result->num_rows === 0) {
                        echo "<p>No previous feedbacks found.</p>";
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='feedback-item'>";
                            echo "<div class='feedback-header'>";
                            echo "<h4 class='feedback-subject'><i class='fas fa-comment'></i> ".$row['title']."</h4>";
                            echo "<p class='feedback-description'>".$row['feedback']."</p>";
                            echo "</div>";
                            echo "<div class='feedback-footer'>";
                            echo "<span class='feedback-date'><i class='fas fa-calendar-alt'></i> Submitted: ".$row['created_at']."</span>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                ?>
            </div>

        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
</footer>

<script>
const feedbackModal = document.getElementById("feedbackModal");
const openModalBtn = document.getElementById("newFeedbackBtn");
const closeBtn = document.querySelector(".close-btn");

// Show the modal
openModalBtn.addEventListener("click", () => {
    feedbackModal.style.display = "block";
});

// Hide the modal when the close button is clicked
closeBtn.addEventListener("click", () => {
    feedbackModal.style.display = "none";
});

// Hide the modal when clicking outside the modal content
window.addEventListener("click", (event) => {
    if (event.target === feedbackModal) {
        feedbackModal.style.display = "none";
    }
});

</script>

</body>
</html>