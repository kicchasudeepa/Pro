<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loginRegisterationSystem";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection failed. Please check your credentials.");
}

// Check if database exists and create it if not
$db_check = $conn->select_db($dbname);
if (!$db_check) {
    $create_db_query = "CREATE DATABASE $dbname";
    if ($conn->query($create_db_query) === TRUE) {
        echo "Database created successfully.";
        $conn->select_db($dbname);
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Set charset
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error setting charset: " . $conn->error);
    die("Charset initialization failed.");
}
?>
