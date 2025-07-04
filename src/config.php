<?php
$env = parse_ini_file('.env');
$host = $env['DB_HOST']; // Host database Anda
$db = $env['DB_NAME']; // Nama database yang sudah Anda buat
$user = $env['DB_USER']; // Username database Anda
$pass = $env['DB_PASS']; // Password database Anda (kosong jika tidak ada)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/' . basename(dirname(__DIR__))); //  sebagai nama projek
define('config', '/src/config.php');
define('func', '/src/functions.php');
// Mulai session
session_start();
?>
