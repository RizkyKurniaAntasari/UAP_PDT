<?php
// login.php
require_once 'config.php';
require_once 'functions.php';

$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($username) || empty($password)) {
        set_message('error', 'Username dan password harus diisi.');
        redirect('login.php');
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
            redirect('dashboard_seller.php');
        } else {
            redirect('dashboard_buyer.php');
        }
    } else {
        set_message('error', 'Username atau password salah.');
        redirect('login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Barang Bekas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login</h2>
        <?php echo $message; ?>
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username atau Email:</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Login</button>
        </form>
        <p class="mt-6 text-center text-gray-600">Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar sekarang</a></p>
        <p class="mt-2 text-center text-gray-600"><a href="index.php" class="text-blue-600 hover:underline">Kembali ke Beranda</a></p>
    </div>
</body>
</html>
