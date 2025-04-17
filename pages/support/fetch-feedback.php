<?php
require_once "../../db_connection.php";

function getAllFeedbacks() {
    // $user_id = '';
    // $user_id = $_SESSION['user_id'];
    $sql = "select title, feedback, created_at from feedbacks";
    $conn = OpenCon();
    $result = mysqli_query($conn, $sql);
    CloseCon($conn);

    return $result;
}
?>