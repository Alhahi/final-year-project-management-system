<?php
require_once '../../db_connection.php';
session_start();

// Ensure that user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to comment.";
    exit;
}

$user_id = $_SESSION['user_id'];
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Ensure forum_id is passed
$forum_id = isset($_POST['forum_id']) ? (int)$_POST['forum_id'] : (isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0);

if ($forum_id && $comment) {
    // Sanitize the comment before insertion
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
    
    // Open database connection
    $conn = OpenCon();
    
    // Prepare and execute the insert statement
    $sql = "INSERT INTO forum_comments (forum_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $forum_id, $user_id, $comment);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./forum-details.css">
</head>
<body>
<div class="back-button">
    <a href="./forum.php" title="Back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
<?php
    require './fetch-forum.php';

    // Fetch the forum ID from the URL (e.g., forum.php?id=1)
    $forumId = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;

    if ($forumId) {
        // Fetch the selected forum details from the database
        $forum = fetchForumById($forumId);

        if ($forum) {
            $username = htmlspecialchars($forum['full_name']);
            $title = htmlspecialchars($forum['title']);
            $content = htmlspecialchars($forum['content']);
            $createdAt = htmlspecialchars($forum['created_at']);
            echo "
            <div class='forum-detail'>
                <h2>$title</h2>
                <p class='meta'>Posted by: <span class='author'>$username</span> on $createdAt</p>
                <p class='content'>$content</p>
            </div>";

            // Fetch comments related to the forum
            $comments = fetchCommentsByForumId($forumId);
            if (!empty($comments)) {
                echo "<h3>Comments:</h3><div class='comments-section'>";
                foreach ($comments as $comment) {
                    $commentContent = htmlspecialchars($comment['comment']);
                    $commentAuthor = htmlspecialchars($comment['full_name']);
                    $commentCreatedAt = htmlspecialchars($comment['created_at']);
                    echo "
                    <div class='comment'>
                        <p class='comment-meta'><strong>$commentAuthor</strong> on $commentCreatedAt</p>
                        <p class='comment-content'>$commentContent</p>
                    </div>";
                }
                echo "</div>";
            } else {
                echo "<p class='comment'>No comments yet. Be the first to comment!</p>";
            }

            // Comment submission form
            echo "
            <div class='comment-form'>
                <h3>Post a Comment</h3>
                <form action='' method='POST'>
                    <input type='hidden' name='forum_id' value='$forumId'>
                    <div class='form-group'>
                        <textarea name='comment' id='content' rows='4' placeholder='Write your comment here...' required></textarea>
                    </div>
                    <button type='submit' class='submit-btn'>Submit Comment</button>
                </form>
            </div>";
        } else {
            echo "<p>Forum topic not found.</p>";
        }
    } else {
        echo "<p>Invalid forum ID.</p>";
    }
?>

</body>
</html>
