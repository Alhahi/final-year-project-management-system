<?php
function OpenCon() {
    $servername = "localhost:3307";
    $username = "root";
    $password = "";
    $dbname = "fyp_system";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}

// Function to sanitize input data

function sanitize_input($data) {

    $data = trim($data);

    $data = stripslashes($data);

    $data = htmlspecialchars($data);

    return $data;

}
?>