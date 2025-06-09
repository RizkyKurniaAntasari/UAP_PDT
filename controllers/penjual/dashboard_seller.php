<?php
// dashboard_seller.php

require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . func;

// Cek autentikasi dan peran
check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('/view/pembeli/dashboard_buyer.php');
    exit;
}

$user_id = get_user_id();
$username = get_username();
$message = get_message();

// Ambil produk milik penjual saat ini
$stmt_products = $pdo->prepare("
    SELECT * 
    FROM products 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt_products->execute([$user_id]);
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

// Ambil pesan yang diterima oleh penjual
$stmt_messages = $pdo->prepare("
    SELECT m.*, s.username AS sender_username, p.title AS product_title
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    LEFT JOIN products p ON m.product_id = p.id
    WHERE m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmt_messages->execute([$user_id]);
$received_messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);
$stmt_messages->closeCursor();

// Ambil semua produk dari seluruh seller untuk referensi (jika dibutuhkan)
$stmt_all_products = $pdo->prepare("
    SELECT p.*, u.username AS seller_username 
    FROM products p
    JOIN users u ON p.user_id = u.id
");
$stmt_all_products->execute();
$all_products = $stmt_all_products->fetchAll(PDO::FETCH_ASSOC);
$stmt_all_products->closeCursor();
