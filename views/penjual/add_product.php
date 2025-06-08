<?php
require_once '../../controllers/penjual/add_product.php'
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
    <?php include_once '../components/navbar.php'; ?>

    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Produk Baru</h2>
            <?php echo $message; ?>
            <form action="../../controllers/penjual/add_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                    <a href="../penjual/dashboard_seller.php" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>
</body>

</html>