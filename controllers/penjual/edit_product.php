<?php
// controllers/penjual/edit_product.php

require_once __DIR__ . '/../../src/config.php';
require_once BASE_PATH . '/src/functions.php'; // Corrected path to functions.php

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('/views/pembeli/dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message(); // Get any flash messages

$product = null; // Initialize product variable

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Handle GET request - fetch product data for display
    $id = $_GET['id'] ?? null; // Use null coalescing operator for cleaner code

    if (!$id) {
        set_message('error', 'ID produk tidak ditemukan.');
        redirect('/views/penjual/dashboard_seller.php');
    }

    $product_id = sanitize_input($id);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
    $product = $stmt->fetch();

    if (!$product) {
        set_message('error', 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.');
        redirect('/views/penjual/dashboard_seller.php');
    }

    // At this point, $product contains the data to be displayed in the form.
    // The view file will now be included to render the form.

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle POST request - process form submission
    $product_id = sanitize_input($_POST['product_id'] ?? null); // Ensure product_id exists
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $price = sanitize_input($_POST['price'] ?? 0);
    $image_file = $_FILES['image_url'] ?? null; // Handle file upload
    $status = sanitize_input($_POST['status'] ?? '');

    // --- Input Validation ---
    $errors = [];
    if (empty($product_id)) {
        $errors[] = 'ID Produk tidak ditemukan dalam formulir.';
    }
    if (empty($title) || empty($price) || empty($status)) {
        $errors[] = 'Judul, Harga, dan Status harus diisi.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = 'Harga harus berupa angka positif.';
    }

    // Fetch the existing product to get its current image_url if no new file is uploaded
    $existing_product = null;
    if ($product_id) {
        $stmt_check = $pdo->prepare("SELECT image_url FROM products WHERE id = ? AND user_id = ?");
        $stmt_check->execute([$product_id, $user_id]);
        $existing_product = $stmt_check->fetch();
        if (!$existing_product) {
            $errors[] = 'Produk tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.';
        }
    }


    $image_url_to_save = $existing_product['image_url'] ?? "https://placehold.co/400x300/e0e0e0/555555?text=No+Image"; // Default or existing

    // Handle file upload (if a new file is provided)
    if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/public/uploads/'; // Define your upload directory
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($image_file['name'], PATHINFO_EXTENSION);
        $new_file_name = uniqid('product_') . '.' . $file_extension;
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($image_file['tmp_name'], $upload_path)) {
            $image_url_to_save = '/public/uploads/' . $new_file_name; // Relative path for database
        } else {
            $errors[] = 'Gagal mengunggah gambar.';
        }
    }


    if (!empty($errors)) {
        set_message('error', implode('<br>', $errors));
        // Redirect back to the form, passing the product_id
        redirect('/views/penjual/edit_product.php?id=' . $product_id);
    }

    // --- Database Update ---
    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, image_url = ?, status = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$title, $description, $price, $image_url_to_save, $status, $product_id, $user_id])) {
        set_message('success', 'Produk berhasil diperbarui!');
        redirect('/views/penjual/dashboard_seller.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat memperbarui produk.');
        redirect('/views/penjual/edit_product.php?id=' . $product_id);
    }
}
// Note: No HTML output in this file. The view file will handle displaying the form.
?>