<?php
session_start();
require_once '../../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$sender_id = $_SESSION['user_id'];
$receiver_id = $data['receiver_id'];
$message = $data['message'];

$conn = OpenCon();
$sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();
$stmt->close();
CloseCon($conn);

echo json_encode(['success' => true]);
?>