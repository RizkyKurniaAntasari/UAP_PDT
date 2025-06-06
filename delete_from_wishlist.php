<?php
// delete_from_wishlist.php
require_once 'config.php';
require_once 'functions.php';

check_auth();
if (get_user_role() !== 'buyer') {
    set_message('error', 'Akses ditolak. Anda bukan pembeli.');
    redirect('dashboard_seller.php');
}

$user_id = get_user_id();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wishlist_id = sanitize_input($_POST['wishlist_id']);

    if (empty($wishlist_id)) {
        set_message('error', 'ID Wishlist tidak diberikan.');
        redirect('dashboard_buyer.php');
    }

    // Pastikan item wishlist yang dihapus adalah milik user yang sedang login
    $stmt = $pdo->prepare("DELETE FROM wishlists WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$wishlist_id, $user_id])) {
        if ($stmt->rowCount() > 0) {
            set_message('success', 'Produk berhasil dihapus dari wishlist!');
        } else {
            set_message('error', 'Item wishlist tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.');
        }
    } else {
        set_message('error', 'Terjadi kesalahan saat menghapus dari wishlist.');
    }
} else {
    set_message('error', 'Metode request tidak valid.');
}

redirect('dashboard_buyer.php');
?>
