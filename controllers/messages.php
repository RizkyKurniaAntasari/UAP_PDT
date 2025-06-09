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

try {
    // Panggil stored procedure
    $stmt = $pdo->prepare("CALL get_user_conversations(?)"); // prosedure
    $stmt->execute([$user_id]);

    // Ambil semua pesan
    $all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Proses grouping percakapan
    $conversations = [];
    foreach ($all_messages as $msg) {
        $partner_id = ($msg['sender_id'] == $user_id) ? $msg['receiver_id'] : $msg['sender_id'];
        $partner_username = ($msg['sender_id'] == $user_id) ? $msg['receiver_username'] : $msg['sender_username'];

        $key = '';
        if ($msg['product_id']) {
            $key = 'product_' . $msg['product_id'] . '_' . $partner_id;
        } else {
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

        $conversations[$key]['messages'][] = $msg;
    }

} catch (PDOException $e) {
    // Error dari stored procedure
    set_message('error', 'Gagal mengambil data percakapan: ' . $e->getMessage());
    redirect('/views/' . ($role === 'buyer' ? 'pembeli/dashboard_buyer.php' : 'penjual/dashboard_seller.php'));
}

// Siapkan data ke view
// $user_id, $username, $role, $message, $conversations tersedia
?>