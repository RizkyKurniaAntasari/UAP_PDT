<?php

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
