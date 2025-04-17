<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $feedback = $_POST['feedback'];
    addFeedback($title, $feedback);
    header("Location: /fyp-system/pages/support/support-page.php");
}

function addFeedback($title, $feedback) {
    require_once '../../db_connection.php';
    $conn = OpenCon();
    session_start();
    
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

    // Use a prepared statement
    $stmt = $conn->prepare("INSERT INTO feedbacks (user_id, title, feedback) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $feedback);

    if ($stmt->execute()) {
        echo "Feedback added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/fyp-system/components/form/feedback-form.css">
    </head>
    <body>
        <!-- Modal Container -->
        <div id="feedbackModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <div class="panel-header">
                    <h3>Submit Feedback</h3>
                </div>
                <form id="feedbackForm" class="feedback-form" method="post" action="">
                    <div class="form-group">
                        <label for="title">Subject</label>
                        <input type="text" id="title" name="title" required placeholder="Brief description of the issue">
                    </div>
                    <div class="form-group">
                        <label for="feedback">Description</label>
                        <textarea id="feedback" name="feedback" rows="4" required placeholder="Please provide detailed information..."></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Feedback</button>
                </form>
            </div>
        </div>
    </body>
</html>

