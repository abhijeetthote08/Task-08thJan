<?php

// Database connection settings
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'iqac';

// Establish connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check for connection errors
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>