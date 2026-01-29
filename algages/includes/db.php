<?php
/**
 * Database Connection Configuration
 *
 * This application is strictly configured for MySQL/PDO.
 * Update the credentials below to match your environment.
 */

$host = 'localhost';
$db   = 'algages_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . ". Please ensure MySQL is running and credentials in includes/db.php are correct.");
}
?>
