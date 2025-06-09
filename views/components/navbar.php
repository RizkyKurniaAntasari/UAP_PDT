<?php
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
check_auth();

$user_id = get_user_id();

// Ambil data dari stored function MySQL
$stmt1 = $pdo->prepare("SELECT get_user_role_by_id(:uid) AS role");
$stmt1->execute(['uid' => $user_id]);
$role = $stmt1->fetchColumn();
$stmt1->closeCursor(); // sangat penting!

$stmt2 = $pdo->prepare("SELECT get_username_by_id(:uid) AS username");
$stmt2->execute(['uid' => $user_id]);
$username = $stmt2->fetchColumn();
$stmt2->closeCursor(); // sangat penting!


// Mapping label role
$role_label = ($role === 'buyer') ? 'pembeli' : 'penjual';

// Routing berdasarkan role
$dashboard_routes = [
    'pembeli' => BASE_URL . '/views/pembeli/dashboard_buyer.php',
    'penjual' => BASE_URL . '/views/penjual/dashboard_seller.php',
];

$pesan_routes = BASE_URL . '/views/messages_page.php';
$edit = BASE_URL . '/views/edit_profile.php';
$logout = BASE_URL . '/controllers/auth/logout.php';

$dashboard_url = $dashboard_routes[$role_label] ?? BASE_URL;
?>

<nav class="bg-blue-600 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <a href="<?= $dashboard_url; ?>" class="text-white text-2xl font-bold whitespace-nowrap">
            Dashboard <?= ucfirst($role_label); ?>
        </a>
        <div class="flex items-center space-x-4">
            <span class="text-white whitespace-nowrap">
                Halo, <?= htmlspecialchars($username); ?> (<?= ucfirst($role_label); ?>)
            </span>
            <a href="<?= $edit; ?>" class="text-white hover:text-blue-200 whitespace-nowrap">Edit Profil</a>
            <a href="<?= $pesan_routes; ?>" class="text-white hover:text-blue-200 whitespace-nowrap">Pesan</a>
            <form action="<?= $logout; ?>" method="post" class="flex items-center">
                <button type="submit"
                    class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300 whitespace-nowrap"
                    onclick="return confirm('Yakin ingin keluar ?');">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
