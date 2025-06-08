<?php
require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . func;

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('/views/pembeli/dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message();
$product = null;

if (isset($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
    $product = $stmt->fetch();

    if (!$product) {
        set_message('error', 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.');
        redirect('/views/penjual/dashboard_seller.php');
    }
} else {
    set_message('error', 'ID Produk tidak diberikan.');
    redirect('/views/penjual/dashboard_seller.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = sanitize_input($_POST['product_id']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);
    $image_url = sanitize_input($_POST['image_url']);
    $status = sanitize_input($_POST['status']);

    if (empty($title) || empty($price) || empty($status)) {
        set_message('error', 'Judul, Harga, dan Status harus diisi.');
        redirect('/views/penjual/edit_product.php?id=' . $product_id);
    }

    if (!is_numeric($price) || $price < 0) {
        set_message('error', 'Harga harus berupa angka positif.');
        redirect('/views/penjual/edit_product.php?id=' . $product_id);
    }

    // Default image if empty
    if (empty($image_url)) {
        $image_url = "https://placehold.co/400x300/e0e0e0/555555?text=No+Image";
    }

    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, image_url = ?, status = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$title, $description, $price, $image_url, $status, $product_id, $user_id])) {
        set_message('success', 'Produk berhasil diperbarui!');
        redirect('/views/penjual/dashboard_seller.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat memperbarui produk.');
        redirect('/views/penjual/edit_product.php?id=' . $product_id);
    }
}