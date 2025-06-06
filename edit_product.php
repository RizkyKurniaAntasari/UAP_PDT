<?php
// edit_product.php
require_once 'config.php';
require_once 'functions.php';

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message();
$product = null;

if (isset($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
    $product = $stmt->fetch();

    if (!$product) {
        set_message('error', 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.');
        redirect('dashboard_seller.php');
    }
} else {
    set_message('error', 'ID Produk tidak diberikan.');
    redirect('dashboard_seller.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = sanitize_input($_POST['product_id']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);
    $image_url = sanitize_input($_POST['image_url']);
    $status = sanitize_input($_POST['status']);

    if (empty($title) || empty($price) || empty($status)) {
        set_message('error', 'Judul, Harga, dan Status harus diisi.');
        redirect('edit_product.php?id=' . $product_id);
    }

    if (!is_numeric($price) || $price < 0) {
        set_message('error', 'Harga harus berupa angka positif.');
        redirect('edit_product.php?id=' . $product_id);
    }

    // Default image if empty
    if (empty($image_url)) {
        $image_url = "https://placehold.co/400x300/e0e0e0/555555?text=No+Image";
    }

    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, image_url = ?, status = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$title, $description, $price, $image_url, $status, $product_id, $user_id])) {
        set_message('success', 'Produk berhasil diperbarui!');
        redirect('dashboard_seller.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat memperbarui produk.');
        redirect('edit_product.php?id=' . $product_id);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Sistem Barang Bekas</title>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Produk</h2>
            <?php echo $message; ?>
            <?php if ($product): ?>
                <form action="edit_product.php" method="POST" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <div>
                        <label for="title" class="block text-gray-700 text-sm font-semibold mb-2">Judul Produk:</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="description" class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi Produk:</label>
                        <textarea id="description" name="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div>
                        <label for="price" class="block text-gray-700 text-sm font-semibold mb-2">Harga (Rp):</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="image_url" class="block text-gray-700 text-sm font-semibold mb-2">URL Gambar Produk:</label>
                        <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-gray-700 text-sm font-semibold mb-2">Status:</label>
                        <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="available" <?php echo ($product['status'] == 'available' ? 'selected' : ''); ?>>Tersedia</option>
                            <option value="sold" <?php echo ($product['status'] == 'sold' ? 'selected' : ''); ?>>Terjual</option>
                        </select>
                    </div>
                    <div class="flex justify-between space-x-4">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Perbarui Produk</button>
                        <a href="dashboard_seller.php" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-red-600 text-center">Produk tidak dapat dimuat.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
