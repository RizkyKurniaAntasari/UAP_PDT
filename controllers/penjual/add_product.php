<?php
// add_product.php
require_once '../../src/config.php';
require_once BASE_PATH . func;

check_auth();
if (get_user_role() !== 'seller') {
    set_message('error', 'Akses ditolak. Anda bukan penjual.');
    redirect('/views/pembeli/dashboard_buyer.php');
}

$user_id = get_user_id();
$message = get_message();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);

    if (empty($title) || empty($price)) {
        set_message('error', 'Judul dan Harga harus diisi.');
        redirect_views('/penjual/add_product.php');
    }

    if (!is_numeric($price) || $price < 0) {
        set_message('error', 'Harga harus berupa angka positif.');
        redirect_views('/penjual/add_product.php');
    }

    // Proses Upload Gambar
    $image_path = null;

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = basename($_FILES['image_file']['name']);
        $file_size = $_FILES['image_file']['size'];
        $file_type = mime_content_type($file_tmp);

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file_type, $allowed_types)) {
            set_message('error', 'Format gambar hanya boleh JPG, JPEG, atau PNG.');
            redirect_views('/penjual/add_product.php');
        }

        // Rename file agar unik
        $new_name = uniqid('img_') . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
        $destination_dir = BASE_PATH . '/uploads/';
        $destination_path = $destination_dir . $new_name;

        // Pastikan folder tujuan ada
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0755, true);
        }

        // Pindahkan file
        if (!move_uploaded_file($file_tmp, $destination_path)) {
            set_message('error', 'Gagal mengunggah gambar.');
            redirect_views('/penjual/add_product.php');
        }

        // Simpan path relatif ke database
        $image_path = '/'. basename(dirname(dirname(__DIR__)))  .'/uploads/' . $new_name; # sesuaikan dengan nama project masing2 (PDT)
    } else {
        set_message('error', 'Gambar produk wajib diunggah.');
        redirect_views('/penjual/add_product.php');
    }

    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO products (user_id, title, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $description, $price, $image_path])) {
        set_message('success', 'Produk berhasil ditambahkan!');
        redirect_views('/penjual/dashboard_seller.php');
    } else {
        set_message('error', 'Terjadi kesalahan saat menambahkan produk.');
        redirect_views('/penjual/add_product.php');
    }
}
