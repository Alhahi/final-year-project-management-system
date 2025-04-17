<?php
require_once "../../db_connection.php";

function getInboxEmails() {
    if (!isset($_SESSION['user_id'])) {
        die("User is not logged in.");
    }

    $user_id = $_SESSION['user_id'];
    $sql = "
        SELECT u.full_name, f.subject, f.message, f.sent_at 
        FROM emails f
        JOIN users u ON f.sender_id = u.user_id
        WHERE f.receiver_id = ?
        ORDER BY f.sent_at DESC
    ";

    $conn = OpenCon();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    CloseCon($conn);

    return $result;
}

function getSentEmails() {
    if (!isset($_SESSION['user_id'])) {
        die("User is not logged in.");
    }

    $user_id = $_SESSION['user_id'];
    $sql = "
        SELECT u.full_name, f.subject, f.message, f.sent_at 
        FROM emails f
        JOIN users u
        ON f.receiver_id = u.user_id
        WHERE f.sender_id = ?
        ORDER BY sent_at DESC
    ";
    $conn = OpenCon();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    CloseCon($conn);

    return $result;
}
?>
