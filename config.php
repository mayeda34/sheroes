<?php

// Database configuration
$host = "localhost";        // or 127.0.0.1
$username = "root";         // default username for XAMPP
$password = "";             // default password for XAMPP (leave empty)
$database = "sheroes";   // your database name
// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8
$conn->set_charset("utf8");
?>
