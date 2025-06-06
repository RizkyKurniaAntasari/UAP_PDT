<?php
// dashboard_buyer.php
require_once 'config.php';
require_once 'functions.php';

check_auth();
if (get_user_role() !== 'buyer') {
    set_message('error', 'Akses ditolak. Anda bukan pembeli.');
    redirect('dashboard_seller.php'); // Arahkan ke dashboard penjual jika bukan buyer
}

$user_id = get_user_id();
$username = get_username();
$message = get_message();

// Ambil semua produk yang tersedia (status 'available')
$search_query = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$where_clause = '';
$params = [];

if (!empty($search_query)) {
    $where_clause = " WHERE title LIKE ? OR description LIKE ?";
    $params = ["%$search_query%", "%$search_query%"];
}

$stmt_products = $pdo->prepare("SELECT p.*, u.username AS seller_username FROM products p JOIN users u ON p.user_id = u.id" . $where_clause . " AND p.status = 'available' ORDER BY p.created_at DESC");
$stmt_products->execute($params);
$products = $stmt_products->fetchAll();

// Ambil wishlist pembeli
$stmt_wishlist = $pdo->prepare("SELECT w.id AS wishlist_id, p.* FROM wishlists w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt_wishlist->execute([$user_id]);
$wishlist_items = $stmt_wishlist->fetchAll();

// Ambil pesan yang dikirim dan diterima oleh pembeli ini
$stmt_messages = $pdo->prepare("
    SELECT m.*, 
           CASE 
               WHEN m.sender_id = ? THEN 'Anda' 
               ELSE s.username 
           END AS partner_username,
           p.title AS product_title
    FROM messages m
    JOIN users s ON (m.sender_id = s.id AND m.sender_id != ?) OR (m.receiver_id = s.id AND m.receiver_id != ?)
    LEFT JOIN products p ON m.product_id = p.id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmt_messages->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
$all_messages = $stmt_messages->fetchAll();

// Group messages by conversation partner or product
$conversations = [];
foreach ($all_messages as $msg) {
    $key = '';
    if ($msg['product_id']) {
        $key = 'product_' . $msg['product_id'] . '_' . $msg['partner_username'];
    } else {
        $key = 'user_' . $msg['partner_username'];
    }
    
    if (!isset($conversations[$key])) {
        $conversations[$key] = [
            'product_title' => $msg['product_title'],
            'partner_username' => $msg['partner_username'],
            'messages' => []
        ];
    }
    $conversations[$key]['messages'][] = $msg;
}

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
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="dashboard_buyer.php" class="text-white text-2xl font-bold">Dashboard Pembeli</a>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, <?php echo htmlspecialchars($username); ?> (Pembeli)</span>
                <a href="edit_profile.php" class="text-white hover:text-blue-200">Edit Profil</a>
                <a href="messages.php" class="text-white hover:text-blue-200">Pesan</a>
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

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
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="flex-grow text-center bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 transition duration-300 text-sm">Lihat Detail</a>
                                    <form action="add_to_wishlist.php" method="POST" class="inline-block">
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
                                    <a href="product_detail.php?id=<?php echo $item['id']; ?>" class="flex-grow text-center bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 transition duration-300 text-sm">Lihat Detail</a>
                                    <form action="delete_from_wishlist.php" method="POST" class="inline-block">
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
                                            <?php echo ($msg['sender_id'] == $user_id ? 'Anda' : htmlspecialchars($msg['sender_username'])) . ': ' . nl2br(htmlspecialchars($msg['message_text'])); ?>
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
