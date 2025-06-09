<?php
require_once __DIR__ . '/../../src/config.php';
check_auth();
$username = get_username();
$role = get_user_role() == 'buyer' ? 'pembeli' : 'penjual';

// Mapping dashboard berdasarkan role
$dashboard_routes = [
    'pembeli' => BASE_URL . '/views/pembeli/dashboard_buyer.php',
    'penjual' => BASE_URL . '/views/penjual/dashboard_seller.php',
];

$pesan_routes = BASE_URL . '/views/messages_page.php';
$edit = BASE_URL . '/views/edit_profile.php';
$logout = BASE_URL . '/controllers/auth/logout.php';

$dashboard_url = $dashboard_routes[$role] ?? '/'; // fallback ke home jika role tidak dikenali
?>
<nav class="bg-blue-600 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <a href="<?= $dashboard_url; ?>" class="text-white text-2xl font-bold whitespace-nowrap">
            Dashboard <?= ucfirst($role); ?>
        </a>
        <div class="flex items-center space-x-4">
            <span class="text-white whitespace-nowrap">
                Halo, <?= htmlspecialchars($username); ?> (<?= ucfirst($role); ?>)
            </span>
            <a href="<?= $edit; ?>" class="text-white hover:text-blue-200 whitespace-nowrap">Edit Profil</a>
            <?php if ($role === 'pembeli' || $role === 'penjual'): ?>
                <a href="<?= $pesan_routes; ?>" class="text-white hover:text-blue-200 whitespace-nowrap">Pesan</a>
            <?php endif; ?>
            <form action="<?= $logout; ?>" class="flex items-center">
                <button type="submit"
                    class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300 whitespace-nowrap"
                    onclick="return confirm('Yakin ingin keluar ?');">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
