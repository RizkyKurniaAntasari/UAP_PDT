<?php
// register.php
require_once 'config.php';
require_once 'functions.php';

$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);
    $role = sanitize_input($_POST['role']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        set_message('error', 'Semua kolom harus diisi.');
        redirect('register.php');
    }

    if ($password !== $confirm_password) {
        set_message('error', 'Konfirmasi password tidak cocok.');
        redirect('register.php');
    }

    // Cek apakah username atau email sudah terdaftar
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        set_message('error', 'Username atau Email sudah terdaftar.');
        redirect('register.php');
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password, $role])) {
        set_message('success', 'Registrasi berhasil! Silakan login.');
        redirect('login.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat registrasi.');
        redirect('register.php');
    }
}
?>