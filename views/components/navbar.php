
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="../views/pembeli/dashboard_buyer.php" class="text-white text-2xl font-bold">Dashboard Pembeli</a>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, <?php echo htmlspecialchars($username); ?> (Pembeli)</span>
                <a href="edit_profile.php" class="text-white hover:text-blue-200">Edit Profil</a>
                <a href="messages.php" class="text-white hover:text-blue-200">Pesan</a>
                <a href="../../controllers/auth/logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>