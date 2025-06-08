<?php
// dashboard_buyer.php
require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . func;
require_once BASE_PATH . '/controllers/pembeli/dashboard_buyer.php';
include_once '../components/navbar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembeli - Sistem Barang Bekas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-6">
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Cari Produk</h2>
            <form action="dashboard_buyer.php" method="GET" class="flex space-x-2 mb-4">
                <input type="text" name="search" placeholder="Cari judul atau deskripsi..." value="<?php echo htmlspecialchars($search_query); ?>" class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Cari</button>
                <?php if (!empty($search_query)): ?>
                    <a href="dashboard_buyer.php" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Reset</a>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produk Tersedia</h2>
            <?php if (empty($products)): ?>
                <p class="text-gray-600">Tidak ada produk yang tersedia saat ini.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-full h-48 object-cover">
                            <div class="p-4 flex-grow flex flex-col">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['title']); ?></h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-3"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="text-xl font-bold text-blue-600 mb-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <p class="text-gray-500 text-xs mb-4">Penjual: <?php echo htmlspecialchars($product['seller_username']); ?></p>
                                <div class="mt-auto flex space-x-2">
                                    <a href="../product_detail.php?id=<?php echo $product['id']; ?>" class="flex-grow text-center bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 transition duration-300 text-sm">Lihat Detail</a>
                                    <form action="../../controllers/pembeli/add_to_wishlist.php" method="POST" class="inline-block">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="bg-yellow-500 text-white py-2 px-3 rounded-md hover:bg-yellow-600 transition duration-300 text-sm">Wishlist</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Wishlist Anda</h2>
            <?php if (empty($wishlist_items)): ?>
                <p class="text-gray-600">Wishlist Anda kosong.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-48 object-cover">
                            <div class="p-4 flex-grow flex flex-col">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="text-xl font-bold text-blue-600 mb-2">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                                <div class="mt-auto flex space-x-2">
                                    <a href="../product_detail.php?id=<?php echo $item['id']; ?>" class="flex-grow text-center bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 transition duration-300 text-sm">Lihat Detail</a>
                                    <form action="../../controllers/pembeli/delete_from_wishlist.php" method="POST" class="inline-block">
                                        <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white py-2 px-3 rounded-md hover:bg-red-600 transition duration-300 text-sm" onclick="return confirm('Hapus dari wishlist?');">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Riwayat Pesan Anda</h2>
            <?php if (empty($conversations)): ?>
                <p class="text-gray-600">Anda belum memiliki riwayat pesan.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($conversations as $key => $conversation): ?>
                        <div class="border border-gray-200 p-4 rounded-md bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Percakapan dengan: <span class="text-blue-600"><?php echo htmlspecialchars($conversation['partner_username']); ?></span>
                                <?php if ($conversation['product_title']): ?>
                                    <span class="text-sm text-gray-600 ml-2">(Tentang: <?php echo htmlspecialchars($conversation['product_title']); ?>)</span>
                                <?php endif; ?>
                            </h3>
                            <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border-t border-gray-200 pt-2">
                                <?php foreach ($conversation['messages'] as $msg): ?>
                                    <p class="text-sm <?php echo ($msg['sender_id'] == $user_id ? 'text-right text-gray-700' : 'text-left text-gray-800 font-medium'); ?>">
                                        <span class="<?php echo ($msg['sender_id'] == $user_id ? 'bg-blue-100' : 'bg-gray-200'); ?> px-3 py-1 rounded-lg inline-block">
                                            <?php echo ($msg['sender_id'] == $user_id ? 'Anda' : htmlspecialchars($msg['partner_username'])) . ': ' . nl2br(htmlspecialchars($msg['message_text'])); ?>
                                        </span>
                                        <span class="block text-xs text-gray-500 mt-1"><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></span>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
