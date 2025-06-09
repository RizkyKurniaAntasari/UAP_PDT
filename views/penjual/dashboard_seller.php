<?php
require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . func;
require_once __DIR__ . '/../../controllers/penjual/dashboard_seller.php';

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - Sistem Barang Bekas</title>
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
    <?php
    // var_dump($_SESSION['role']); // cek isi role
    // var_dump($_SESSION['user_id']); // cek user id yang login
    // var_dump($product['user_id']); // cek user id pemilik produk
    ?>
    <div class="container mx-auto p-6">
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produk Anda</h2>
            <a href="add_product.php" class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-300 mb-4 inline-block">Tambah Produk Baru</a>

            <?php if (empty($products)): ?>
                <p class="text-gray-600 mt-4">Anda belum mengunggah produk apapun.</p>
            <?php else: ?>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700 rounded-tl-lg">Gambar</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Judul</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Harga</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Tanggal Unggah</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700 rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-16 h-16 object-cover rounded-md">
                                        <!-- <img src="/PDT/uploads/img_684481b49f62c.jpeg   " class="w-16 h-16 object-cover rounded-md"> -->
                                    </td>
                                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td class="py-3 px-4 text-gray-800">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo ($product['status'] == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($product['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 text-sm"><?php echo date('d M Y H:i', strtotime($product['created_at'])); ?></td>
                                    <td class="py-3 px-4 space-x-2">
                                        <a href="../../views/penjual/edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:underline text-sm">Edit</a>
                                        <a href="../../controllers/penjual/delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Semua Produk yang Diposting</h2>
            <?php if (empty($products)): ?>
                <p class="text-gray-600">Tidak ada produk yang tersedia saat ini.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($all_products as $product): ?>
                        <a href="../product_detail.php?id=<?php echo urlencode($product['id']); ?>" class="block">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-200">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-full h-48 object-cover">
                                <div class="p-4 flex-grow flex flex-col">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['title']); ?></h3>
                                    <p class="text-gray-600 text-sm mb-2 line-clamp-3"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p class="text-xl font-bold text-blue-600 mb-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                    <p class="text-gray-500 text-xs mb-4">Penjual: <?php echo htmlspecialchars($product['seller_username']); ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Pesan Masuk</h2>
            <?php if (empty($received_messages)): ?>
                <p class="text-gray-600">Anda belum menerima pesan apapun.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($received_messages as $msg): ?>
                        <div class="border border-gray-200 p-4 rounded-md bg-gray-50">
                            <p class="text-gray-800 font-semibold">Dari: <span class="text-blue-600"><?php echo htmlspecialchars($msg['sender_username']); ?></span></p>
                            <?php if ($msg['product_title']): ?>
                                <p class="text-gray-700 text-sm">Tentang Produk: <span class="font-medium"><?php echo htmlspecialchars($msg['product_title']); ?></span></p>
                            <?php endif; ?>
                            <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></p>
                            <p class="text-gray-500 text-xs mt-2">Dikirim pada: <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>
</body>

</html>