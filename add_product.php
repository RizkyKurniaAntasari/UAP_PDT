<?php
// add_product.php
require_once 'config.php';
require_once 'functions.php';

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);
    $image_url = sanitize_input($_POST['image_url']); // Untuk demo, ini adalah URL gambar

    if (empty($title) || empty($price)) {
        set_message('error', 'Judul dan Harga harus diisi.');
        redirect('add_product.php');
    }

    // Validasi harga harus angka
    if (!is_numeric($price) || $price < 0) {
        set_message('error', 'Harga harus berupa angka positif.');
        redirect('add_product.php');
    }

    // Default image if empty
    if (empty($image_url)) {
        $image_url = "https://placehold.co/400x300/e0e0e0/555555?text=No+Image";
    }

    $stmt = $pdo->prepare("INSERT INTO products (user_id, title, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $description, $price, $image_url])) {
        set_message('success', 'Produk berhasil ditambahkan!');
        redirect('dashboard_seller.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat menambahkan produk.');
        redirect('add_product.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Sistem Barang Bekas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard_seller.php" class="text-white text-2xl font-bold">Dashboard Penjual</a>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, <?php echo htmlspecialchars(get_username()); ?></span>
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Produk Baru</h2>
            <?php echo $message; ?>
            <form action="add_product.php" method="POST" class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-700 text-sm font-semibold mb-2">Judul Produk:</label>
                    <input type="text" id="title" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="description" class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi Produk:</label>
                    <textarea id="description" name="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label for="price" class="block text-gray-700 text-sm font-semibold mb-2">Harga (Rp):</label>
                    <input type="number" id="price" name="price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="image_url" class="block text-gray-700 text-sm font-semibold mb-2">URL Gambar Produk (Opsional):</label>
                    <input type="file" id="file" name="file" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: https://example.com/gambar.jpg">
                    <p class="text-sm text-gray-500 mt-1">Jika kosong, gambar placeholder akan digunakan.</p>
                </div>
                <div class="flex justify-between space-x-4">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-300">Tambah Produk</button>
                    <a href="dashboard_seller.php" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
