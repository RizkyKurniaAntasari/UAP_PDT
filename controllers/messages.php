<?php
// controllers/messages.php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

// Pastikan user sudah login
check_auth();

$user_id = get_user_id();
$username = get_username();
$role = get_user_role();
$message = get_message(); // Mengambil flash message jika ada

// Ambil semua pesan yang dikirim dan diterima oleh user ini
$stmt_messages = $pdo->prepare("
    SELECT m.*, 
           sender.username AS sender_username, 
           receiver.username AS receiver_username,
           p.title AS product_title
    FROM messages m
    JOIN users sender ON m.sender_id = sender.id
    JOIN users receiver ON m.receiver_id = receiver.id
    LEFT JOIN products p ON m.product_id = p.id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmt_messages->execute([$user_id, $user_id]);
$all_messages = $stmt_messages->fetchAll();

// Group messages by conversation partner or product
$conversations = [];
foreach ($all_messages as $msg) {
    $partner_id = ($msg['sender_id'] == $user_id) ? $msg['receiver_id'] : $msg['sender_id'];
    $partner_username = ($msg['sender_id'] == $user_id) ? $msg['receiver_username'] : $msg['sender_username'];

    $key = '';
    if ($msg['product_id']) {
        // Percakapan terkait produk spesifik
        $key = 'product_' . $msg['product_id'] . '_' . $partner_id;
    } else {
        // Percakapan personal tidak terkait produk
        $key = 'user_' . $partner_id;
    }

    if (!isset($conversations[$key])) {
        $conversations[$key] = [
            'product_id' => $msg['product_id'],
            'product_title' => $msg['product_title'],
            'partner_id' => $partner_id,
            'partner_username' => $partner_username,
            'messages' => []
        ];
    }
    // Tambahkan pesan ke percakapan yang sesuai
    $conversations[$key]['messages'][] = $msg;
}

// Data yang dibutuhkan di view sudah disiapkan:
// $user_id, $username, $role, $message, $conversations
?>