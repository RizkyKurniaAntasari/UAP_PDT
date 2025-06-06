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

    if (empty($title) || empty($price)) {
        set_message('error', 'Judul dan Harga harus diisi.');
        redirect('add_product.php');
    }

    if (!is_numeric($price) || $price < 0) {
        set_message('error', 'Harga harus berupa angka positif.');
        redirect('add_product.php');
    }

    // Proses Upload Gambar
    $image_path = null;

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = basename($_FILES['image_file']['name']);
        $file_size = $_FILES['image_file']['size'];
        $file_type = mime_content_type($file_tmp);

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file_type, $allowed_types)) {
            set_message('error', 'Format gambar hanya boleh JPG, JPEG, atau PNG.');
            redirect('add_product.php');
        }

        // Rename file agar unik
        $new_name = uniqid('img_') . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
        $destination = 'uploads/' . $new_name;

        if (!move_uploaded_file($file_tmp, $destination)) {
            set_message('error', 'Gagal mengunggah gambar.');
            redirect('add_product.php');
        }

        $image_path = $destination;
    } else {
        set_message('error', 'Gambar produk wajib diunggah.');
        redirect('add_product.php');
    }

    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO products (user_id, title, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $description, $price, $image_path])) {
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
            <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                    <label for="image_file" class="block text-gray-700 text-sm font-semibold mb-2">Gambar Produk (JPG, JPEG, PNG):</label>
                    <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-sm text-gray-500 mt-1">Gambar wajib diunggah dan hanya mendukung JPG, JPEG, atau PNG.</p>
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
