<?php
$host = 'localhost'; // Host database Anda
$db = 'uap_pdt'; // Nama database yang sudah Anda buat
$user = 'root'; // Username database Anda
$pass = ''; // Password database Anda (kosong jika tidak ada)
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

// Mulai session
session_start();
?>
