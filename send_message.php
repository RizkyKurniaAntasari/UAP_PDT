<?php
// send_message.php
require_once 'config.php';
require_once 'functions.php';

check_auth();

$sender_id = get_user_id();
$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = sanitize_input($_POST['receiver_id']);
    $product_id = isset($_POST['product_id']) ? sanitize_input($_POST['product_id']) : null;
    $message_text = sanitize_input($_POST['message_text']);

    if (empty($receiver_id) || empty($message_text)) {
        set_message('error', 'Penerima dan isi pesan harus diisi.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard_buyer.php'); // Kembali ke halaman sebelumnya
    }

    if ($sender_id == $receiver_id) {
        set_message('error', 'Anda tidak bisa mengirim pesan ke diri sendiri.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard_buyer.php');
    }

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message_text) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$sender_id, $receiver_id, $product_id, $message_text])) {
        set_message('success', 'Pesan berhasil dikirim!');
    } else {
        set_message('error', 'Terjadi kesalahan saat mengirim pesan.');
    }
    redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard_buyer.php'); // Kembali ke halaman sebelumnya
} else {
    set_message('error', 'Metode request tidak valid.');
    redirect('dashboard_buyer.php');
}
?>
