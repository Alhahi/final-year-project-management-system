<?php
require_once '../../db_connection.php';

$conn = OpenCon();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goal_id'])) {
    $goal_id = $_POST['goal_id'];

    $stmt = $conn->prepare("DELETE FROM goals WHERE goal_id = ?");
    $stmt->bind_param("i", $goal_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Goal deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting goal."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
