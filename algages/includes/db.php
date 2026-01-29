<?php
$db_path = __DIR__ . '/../database.sqlite';

try {
    // SQLite connection (default for demo)
    $pdo = new PDO('sqlite:' . $db_path);

    // MySQL connection (Uncomment to use MySQL)
    /*
    $host = 'localhost';
    $db   = 'algages';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    */

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
