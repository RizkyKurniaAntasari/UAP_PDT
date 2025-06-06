<?php
// delete_product.php
require_once 'config.php';
require_once 'functions.php';

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('dashboard_buyer.php');
}

$user_id = get_user_id();

if (isset($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);

    // Pastikan produk yang dihapus adalah milik user yang sedang login
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$product_id, $user_id])) {
        if ($stmt->rowCount() > 0) {
            set_message('success', 'Produk berhasil dihapus!');
        } else {
            set_message('error', 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.');
        }
    } else {
        set_message('error', 'Terjadi kesalahan saat menghapus produk.');
    }
} else {
    set_message('error', 'ID Produk tidak diberikan.');
}

redirect('dashboard_seller.php');
?>
