<?php
// index.php
require_once __DIR__ . '/src/config.php';
require_once BASE_PATH . func;
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    ($_SESSION['role'] == 'seller') ? redirect_views('/penjual/dashboard_seller.php') : redirect_views('/pembeli/dashboard_buyer.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PosterTalk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang di <span class="text-blue-600">PosterTalk</span>!</h1>
        <p class="text-gray-600 mb-8">
            PosterTalk adalah platform jual beli poster digital. Temukan karya favoritmu atau unggah poster milikmu sendiri. Chat langsung dengan penjual atau pembeli!
        </p>
        <div class="space-y-4">
            <a href="login.php" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition duration-300">Login</a>
            <a href="register.php" class="block w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition duration-300">Daftar Akun Baru</a>
        </div>
    </div>
</body>

</html>
