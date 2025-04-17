<?php
require_once "../../db_connection.php";

function fetchAllForums() {
    $sql = "
        SELECT f.forum_id, u.full_name, f.title, f.content, f.created_at 
        FROM forums f
        join users u on f.user_id = u.user_id
        ORDER BY f.created_at DESC;
    ";

    // Open database connection
    $conn = OpenCon();

    // Execute query and handle errors
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch all rows as an associative array
    $forums = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    CloseCon($conn);

    return $forums;
}

function fetchForumById($forumId) {
    $sql = "
        SELECT u.full_name, f.title, f.content, f.created_at 
        FROM forums f
        join users u on f.user_id = u.user_id
        where f.forum_id=".$forumId."
        ORDER BY f.created_at DESC;
    ";

    // Open database connection
    $conn = OpenCon();

    // Execute query and handle errors
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch all rows as an associative array
    $forums = mysqli_fetch_assoc($result);

    // Close the database connection
    CloseCon($conn);

    return $forums;
}

function fetchCommentsByForumId($forumId) {
    $sql = "
        SELECT u.full_name, f.comment, f.created_at 
        FROM forum_comments f
        join users u on f.user_id = u.user_id
        where f.forum_id=".$forumId."
        ORDER BY f.created_at DESC;
    ";

    // Open database connection
    $conn = OpenCon();

    // Execute query and handle errors
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch all rows as an associative array
    $forums = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    CloseCon($conn);

    return $forums;
}
?>
