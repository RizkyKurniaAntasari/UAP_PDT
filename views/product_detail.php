<?php
require_once __DIR__ . '/../controllers/product_detail.php';
include_once __DIR__ . '/../views/components/navbar.php';

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// DEBUG: Cek isi product
// var_dump($product);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Sistem Barang Bekas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<div class="container mx-auto p-6">

    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md flex flex-col md:flex-row gap-8">
            <!-- Kolom Gambar -->
            <div class="md:w-1/2">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($product['title']); ?>"
                    class="w-full h-96 object-cover rounded-lg shadow-sm">
            </div>

            <!-- Kolom Informasi Produk dan Aksi -->
            <div class="md:w-1/2">
                <!-- Informasi Produk -->
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($product['title']); ?></h1>
                <p class="text-2xl font-bold text-blue-600 mb-4">
                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                </p>
                <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <p class="text-gray-600 text-sm mb-2">
                    Diunggah oleh: <span class="font-semibold"><?php echo htmlspecialchars($product['seller_username']); ?></span>
                </p>
                <p class="text-gray-600 text-sm mb-6">
                    Tanggal Unggah: <?php echo date('d M Y H:i', strtotime($product['created_at'])); ?>
                </p>

                <?php if ($role === 'seller'): ?>
                    <!-- Tampilan Aksi Untuk Seller -->
                    <p class="text-gray-600 italic mb-4">Ini adalah produk Anda.</p>
                    <div class="flex space-x-2">
                        <a href="../views/penjual/edit_product.php?id=<?php echo $product['id']; ?>"
                            class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                            Edit Produk
                        </a>
                        <a href="../controllers/penjual/delete_product.php?id=<?php echo $product['id']; ?>"
                            class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                            Hapus Produk
                        </a>
                    </div>
                <?php elseif ($role === 'buyer'): ?>
                    <!-- Tampilan Aksi Untuk Buyer -->
                    <div class="flex space-x-4 mb-6">
                        <form action="../controllers/pembeli/add_to_wishlist.php" method="POST" class="inline-block">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="bg-yellow-500 text-white py-3 px-6 rounded-md hover:bg-yellow-600 transition duration-300 text-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 22l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Tambah ke Wishlist
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Kirim Pesan ke Penjual</h3>
                        <form action="../controllers/send_message.php" method="POST" class="space-y-4">
                            <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($product['user_id']); ?>">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <div>
                                <label for="message_text" class="block text-gray-700 text-sm font-semibold mb-2">Pesan Anda:</label>
                                <textarea id="message_text" name="message_text" rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Tanyakan tentang produk ini..." required></textarea>
                            </div>
                            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300 w-full">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>

</html>