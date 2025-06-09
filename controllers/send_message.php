<?php
// send_message.php
require_once __DIR__ . '/../src/config.php';
require_once BASE_PATH . func;

check_auth();

$sender_id = get_user_id();
$message = get_message();
$sender = get_user_role() == 'buyer' ? 'pembeli' : 'penjual';
$dashboard = $sender == 'pembeli' ? 'dashboard_buyer.php' : 'dashboard_seller.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = sanitize_input($_POST['receiver_id']);
    $product_id = isset($_POST['product_id']) ? sanitize_input($_POST['product_id']) : null;
    $message_text = ($_POST['message_text']);

    if (empty($receiver_id) || empty($message_text)) {
        set_message('error', 'Penerima dan isi pesan harus diisi.');
        redirect('/views/' .  $sender . '/' . $dashboard);
    }

    if ($sender_id == $receiver_id) {
        set_message('error', 'Anda tidak bisa mengirim pesan ke diri sendiri.');
        redirect('/views/' .  $sender . '/' . $dashboard);
    }

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message_text) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$sender_id, $receiver_id, $product_id, $message_text])) {
        set_message('success', 'Pesan berhasil dikirim!');
    } else {
        set_message('error', 'Terjadi kesalahan saat mengirim pesan.');
    }
    redirect('/views/messages.php');
} else {
    set_message('error', 'Metode request tidak valid.');
    redirect('/views/' .  $sender . '/' . $dashboard);
}
?>
