<?php
// views/penjual/edit_product.php
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
// IMPORTANT: This 'require' should be the controller that handles the GET request for displaying the form.
// If your current setup is that the controller only handles POST and this view handles GET,
// then the GET logic needs to be moved here or to a 'view_product_for_edit' controller.
// For now, let's assume `edit_product.php` controller handles both and *redirects* POST requests,
// and for GET, it populates $product and then this view is loaded.

// If you access this file directly via GET, you need the logic to fetch the product.
// So, we'll bring some of the GET logic from the controller into this view file for initial load.
// Alternatively, your `routes.php` or `index.php` should direct GET requests to a controller
// that prepares `$product` and then loads this view.

// To make this view standalone for GET requests, we'll include the necessary logic here.
check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('/views/pembeli/dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message();
$product = null; // Initialize $product for the view

$id = $_GET['id'] ?? null;

if (!$id) {
    set_message('error', 'ID produk tidak ditemukan.');
    redirect('/views/penjual/dashboard_seller.php');
}

$product_id = sanitize_input($id);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->execute([$product_id, $user_id]);
$product = $stmt->fetch();

if (!$product) {
    set_message('error', 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.');
    redirect('/views/penjual/dashboard_seller.php');
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

    <?php include_once '../components/navbar.php'; ?>

    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Produk</h2>
            <?php if (!empty($message)): ?>
                <div class="<?php echo strpos($message, 'berhasil') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> p-3 rounded-md mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($product): ?>
                <form action="../../controllers/penjual/edit_product.php" method="POST" class="space-y-4" enctype="multipart/form-data">
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
                        <label for="image_url" class="block text-gray-700 text-sm font-semibold mb-2">Gambar Produk:</label>
                        <?php if (!empty($product['image_url'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Product Image" class="max-w-xs h-auto rounded-md shadow-sm">
                                <p class="text-sm text-gray-500 mt-1">Gambar saat ini</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image_url" name="image_url" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar.</p>
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
                        <a href="../../views/penjual/dashboard_seller.php" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-red-600 text-center">Produk tidak dapat dimuat.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>
</body>

</html>