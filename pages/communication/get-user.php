<?php
require_once '../../db_connection.php';
function getUsers() {
    $conn = OpenCon();
    $sql = "SELECT user_id, full_name FROM users WHERE user_id != ?"; // Exclude the logged-in user
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    CloseCon($conn);
    return $result;
}
?>