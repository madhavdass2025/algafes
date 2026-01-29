<?php
/**
 * Database Connection Configuration (MySQLi)
 */

$host = 'localhost';
$db   = 'algages_db';
$user = 'root';
$pass = '';

// Establish connection
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Error: " . mysqli_connect_error() . ". Please ensure MySQL is running and credentials in includes/db.php are correct.");
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>
