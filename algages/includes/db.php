<?php
// MySQL Configuration (Primary)
$host = 'localhost';
$db   = 'algages';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$db_path = __DIR__ . '/../database.sqlite';

try {
    /*
    // Uncomment this block to use MySQL
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    */

    // Defaulting to SQLite for immediate preview/portability in sandbox
    $pdo = new PDO('sqlite:' . $db_path);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
