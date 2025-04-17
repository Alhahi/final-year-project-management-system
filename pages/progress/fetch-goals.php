<?php
function fetchGoals() {
    require_once '../../db_connection.php';

    $conn = OpenCon();

    $query = "SELECT *
    FROM goals WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    session_start();
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $goals = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $goals[] = $row;
        }
    }

    CloseCon($conn);
    return $goals;
}
?>