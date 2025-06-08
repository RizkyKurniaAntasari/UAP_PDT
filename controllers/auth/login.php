<?php
// login.php
require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . func;

$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($username) || empty($password)) {
        set_message('error', 'Username dan password harus diisi.');
        redirect('/login.php');
    }

    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        set_message('success', 'Login berhasil! Selamat datang, ' . $user['username'] . '!');
        if ($user['role'] == 'seller') {
            redirect('/views/penjual/dashboard_seller.php');
        } else {
            redirect('/views/pembeli/dashboard_buyer.php');
        }
    } else {
        set_message('error', 'Username atau password salah.');
        redirect('/login.php');
    }
}
