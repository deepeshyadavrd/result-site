<?php
// db.php - set your DB credentials here
$host = '127.0.0.1';
$dbname = 'results_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $db = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
