<?php
    require_once __DIR__ . '/../controllers/edit_profile.php';
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

<?php include_once '../views/components/navbar.php';
// var_dump($role);
?>

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
                    <a href="<?php echo ($role == 'pembeli' ? $dashboard_routes['pembeli'] : $dashboard_routes['penjual']); ?>" class="w-full text-center bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
