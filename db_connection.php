<?php
$host = ($_SERVER['SERVER_NAME'] === 'localhost') ? '127.0.0.1' : 'localhost';
$db   = 'c1_planfix';
$user = ($_SERVER['SERVER_NAME'] === 'localhost') ? 'root' : 'c1sltadmin';
$pass = ($_SERVER['SERVER_NAME'] === 'localhost') ? 'Kym7HEbeS6#' : 'dnE3@JyA';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Verbindungsfehler: ' . $e->getMessage());
}
?>
