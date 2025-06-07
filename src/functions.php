<?php
// functions.php

// Fungsi untuk sanitasi input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk redirect
define('BASE_URL', '/PDT'); // Base URL
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Fungsi untuk mengecek apakah user sudah login
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

// Fungsi untuk mendapatkan peran user
function get_user_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Fungsi untuk mendapatkan ID user
function get_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Fungsi untuk mendapatkan username
function get_username() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

// Fungsi untuk menampilkan pesan (flash message)
function set_message($type, $message) {
    $_SESSION['message'] = ['type' => $type, 'text' => $message];
}

function get_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return "<div class='p-3 rounded-md text-sm " . ($message['type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') . "'>" . $message['text'] . "</div>";
    }
    return '';
}
?>
