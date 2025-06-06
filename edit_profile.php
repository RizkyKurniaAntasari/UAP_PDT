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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Sistem Barang Bekas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="<?php echo ($role == 'seller' ? 'dashboard_seller.php' : 'dashboard_buyer.php'); ?>" class="text-white text-2xl font-bold">Kembali ke Dashboard</a>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars(ucfirst($role)); ?>)</span>
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Profil Anda</h2>
            <?php echo $message; ?>
            <form action="edit_profile.php" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly disabled>
                    <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah.</p>
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <hr class="my-6 border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Ubah Password (Opsional)</h3>
                <p class="text-sm text-gray-600 mb-4">Isi kolom di bawah ini hanya jika Anda ingin mengubah password Anda.</p>
                <div>
                    <label for="current_password" class="block text-gray-700 text-sm font-semibold mb-2">Password Saat Ini:</label>
                    <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="new_password" class="block text-gray-700 text-sm font-semibold mb-2">Password Baru:</label>
                    <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="confirm_new_password" class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password Baru:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-between space-x-4">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Perbarui Profil</button>
                    <a href="<?php echo ($role == 'seller' ? 'dashboard_seller.php' : 'dashboard_buyer.php'); ?>" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
