<?php
// logout.php
require_once 'config.php';
require_once 'functions.php';

// Hapus semua variabel session
$_SESSION = array();

// Hapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

set_message('success', 'Anda telah berhasil logout.');
redirect('index.php');
?>
