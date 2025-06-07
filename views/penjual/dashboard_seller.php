<?php
    require_once '../../controllers/penjual/dashboard_seller.php';
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
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard_seller.php" class="text-white text-2xl font-bold">Dashboard Penjual</a>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, <?php echo htmlspecialchars($username); ?> (Penjual)</span>
                <a href="../../controllers/edit_profile.php" class="text-white hover:text-blue-200">Edit Profil</a>
                <a href="../../controllers/messages.php" class="text-white hover:text-blue-200">Pesan</a>
                <a href="../../controllers/auth/logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

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
                                        <!-- <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-16 h-16 object-cover rounded-md"> -->
                                        <?php var_dump($product['image_url']); ?>
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
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:underline text-sm">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</body>
</html>
