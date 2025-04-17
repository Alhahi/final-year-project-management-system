<?php
require_once "../../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Process adding the comment
    $comment = $_POST['comment'];
    $log_id = $_POST['log_id']; // Meeting log id to attach the comment

    $conn = OpenCon();
    
    $sql = "UPDATE meeting_logs SET comments = CONCAT(comments, ?) WHERE meeting_log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([ "\n" . $comment, $log_id ]); // Append the comment to existing comments

    $stmt->close();
    CloseCon($conn);
    header("Location: meetings-page.php");
}

?>
