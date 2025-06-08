<?php
// edit_profile.php
require_once 'config.php';
require_once 'functions.php';

check_auth();

$user_id = get_user_id();
$username = get_username();
$role = get_user_role();
$message = get_message();

// Ambil data user saat ini
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();

if (!$user_data) {
    set_message('error', 'Data pengguna tidak ditemukan.');
    redirect('logout.php'); // Redirect ke logout jika data tidak ditemukan (misal: user dihapus)
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = sanitize_input($_POST['email']);
    $current_password = sanitize_input($_POST['current_password']);
    $new_password = sanitize_input($_POST['new_password']);
    $confirm_new_password = sanitize_input($_POST['confirm_new_password']);

    // Cek apakah email sudah ada yang lain
    $stmt_email_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt_email_check->execute([$new_email, $user_id]);
    if ($stmt_email_check->fetchColumn() > 0) {
        set_message('error', 'Email sudah digunakan oleh akun lain.');
        redirect('edit_profile.php');
    }

    // Update email
    $stmt_update_email = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt_update_email->execute([$new_email, $user_id]);

    // Update password jika diisi
    if (!empty($new_password)) {
        if ($new_password !== $confirm_new_password) {
            set_message('error', 'Konfirmasi password baru tidak cocok.');
            redirect('edit_profile.php');
        }

        // Verifikasi password lama
        $stmt_verify_pass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_verify_pass->execute([$user_id]);
        $stored_password_hash = $stmt_verify_pass->fetchColumn();

        if (!password_verify($current_password, $stored_password_hash)) {
            set_message('error', 'Password lama salah.');
            redirect('edit_profile.php');
        }

        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update_password = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_update_password->execute([$hashed_new_password, $user_id]);
        set_message('success', 'Profil dan password Anda berhasil diperbarui!');
    } else {
        set_message('success', 'Profil Anda berhasil diperbarui!');
    }
    redirect('edit_profile.php');
}
?>