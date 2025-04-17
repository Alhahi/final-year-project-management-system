<?php
require_once '../../db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

$receiver_id = $_GET['receiver_id'];
$sender_id = $_SESSION['user_id'];

$conn = OpenCon();
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed']));
}

$sql = "
    SELECT cm.*, u.full_name AS sender_name 
    FROM messages cm
    JOIN users u ON cm.sender_id = u.user_id
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => 'SQL prepare failed: ' . $conn->error]));
}

$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
if (!$stmt->execute()) {
    die(json_encode(['error' => 'SQL execute failed: ' . $stmt->error]));
}

$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
CloseCon($conn);

header('Content-Type: application/json'); // Ensure the response is JSON
echo json_encode($messages);
exit;
?>