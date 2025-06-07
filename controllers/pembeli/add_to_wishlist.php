<?php
// add_to_wishlist.php
require_once '../config.php';
require_once '../functions.php';

check_auth();
if (get_user_role() !== 'buyer') {
    set_message('error', 'Akses ditolak. Anda bukan pembeli.');
    redirect('dashboard_seller.php');
}

$user_id = get_user_id();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = sanitize_input($_POST['product_id']);

    if (empty($product_id)) {
        set_message('error', 'ID Produk tidak diberikan.');
        redirect('dashboard_buyer.php');
    }

    // Cek apakah produk sudah ada di wishlist
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt_check->execute([$user_id, $product_id]);
    if ($stmt_check->fetchColumn() > 0) {
        set_message('error', 'Produk ini sudah ada di wishlist Anda.');
        redirect('dashboard_buyer.php');
    }

    $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $product_id])) {
        set_message('success', 'Produk berhasil ditambahkan ke wishlist!');
    } else {
        set_message('error', 'Terjadi kesalahan saat menambahkan produk ke wishlist.');
    }
} else {
    set_message('error', 'Metode request tidak valid.');
}

redirect('dashboard_buyer.php');
?>
