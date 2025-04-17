<?php
require_once '../../auth/auth_check.php';
require_once '../../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['proposal_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$proposal_id = $_POST['proposal_id'];
$status = $_POST['status'];
$supervisor_id = $_SESSION['user_id'];

// Validate status
$valid_statuses = ['pending', 'approved', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$conn = OpenCon();

// Verify that this proposal belongs to the current supervisor
$check_sql = "SELECT supervisor_id FROM supervisor_proposals WHERE proposal_id = ? AND supervisor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $proposal_id, $supervisor_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    CloseCon($conn);
    exit;
}

// Update the proposal status
$update_sql = "UPDATE supervisor_proposals SET status = ? WHERE proposal_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $status, $proposal_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

CloseCon($conn); 