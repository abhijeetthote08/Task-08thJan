<?php

// Connect without specifying database
$host = 'localhost';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS iqac";
if (mysqli_query($conn, $sql)) {
    echo "Database 'iqac' created successfully or already exists.";
} else {
    echo "Error creating database: " . mysqli_error($conn);
}

mysqli_close($conn);

?>