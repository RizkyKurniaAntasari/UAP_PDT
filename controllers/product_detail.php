<?php
// product_detail.php
require_once __DIR__ . '/../src/config.php';
require_once BASE_PATH . func;

check_auth();

$user_id = get_user_id();
$username = get_username();
$role = get_user_role();
$message = get_message();
$peran = $role == 'buyer' ? 'pembeli' : 'penjual';

$product = null;
$seller_info = null;

if (isset($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);

    $stmt = $pdo->prepare("SELECT p.*, u.username AS seller_username, u.email AS seller_email FROM products p JOIN users u ON p.user_id = u.id WHERE p.id = ? AND p.status = 'available'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        set_message('error', 'Produk tidak ditemukan atau tidak tersedia.');
        redirect_views('/' . $peran . '/dashboard_buyer.php');
    }

    // Get seller's ID for messaging
    $seller_info = ['id' => $product['user_id'], 'username' => $product['seller_username']];

} else {
    set_message('error', 'ID Produk tidak diberikan.');
    redirect('/views/pembeli/dashboard_buyer.php');
}
?>