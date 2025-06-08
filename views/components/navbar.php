<?php
require_once __DIR__ . '/../../src/config.php';
check_auth();
$username = get_username();
$role = get_user_role() == 'buyer' ? 'pembeli' : 'penjual';

// Mapping dashboard berdasarkan role
$dashboard_routes = [
    'pembeli' => '../views/pembeli/dashboard_buyer.php',
    'penjual' => '../views/penjual/dashboard_seller.php',
];

$dashboard_url = $dashboard_routes[$role] ?? '/'; // fallback ke home jika role tidak dikenali
?>

<nav class="bg-blue-600 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <a href="<?php echo $dashboard_url; ?>" class="text-white text-2xl font-bold">
            Dashboard <?php echo ucfirst($role); ?>
        </a>
        <div class="flex items-center space-x-4">
            <span class="text-white">
                Halo, <?php echo htmlspecialchars($username); ?> (<?php echo ucfirst($role); ?>)
            </span>
            <a href="/views/common/edit_profile.php" class="text-white hover:text-blue-200">Edit Profil</a>
            <?php if ($role === 'pembeli' || $role === 'penjual'): ?>
                <a href="../../controllers/messages.php" class="text-white hover:text-blue-200">Pesan</a>
            <?php endif; ?>
            <a href="../../controllers/auth/logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
        </div>
    </div>
</nav>
