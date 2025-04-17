<?php
function fetchMeetings($status = null) {
    require_once '../../db_connection.php';

    $conn = OpenCon();

    // SQL query
    $query = "SELECT *, u.full_name 
    FROM meetings m
    LEFT JOIN users u on m.user_id = u.user_id";
    if ($status) {
        $query .= " WHERE status = '" . $conn->real_escape_string($status) . "'";
    }
    $query .= " ORDER BY date ASC, start_time ASC";

    $result = $conn->query($query);
    $meetings = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $meetings[] = $row;
        }
    }

    CloseCon($conn);
    return $meetings;
}

function fetchMeetingLog($meeting_id) {
    require_once '../../db_connection.php';
    $db = OpenCon();

    // Query to fetch the meeting log for the given meeting_id
    $query = "SELECT * FROM meeting_logs WHERE meeting_id = ?";

    // Prepare the SQL statement
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $meeting_id);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    $logs = [];
    // Check if we have meeting logs
    if ($result->num_rows > 0) {
        // Fetch the meeting log details
        $logs = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
    CloseCon($db);
    return $logs;
}

?>