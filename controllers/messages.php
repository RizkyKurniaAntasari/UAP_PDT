<?php
// messages.php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

check_auth();

$user_id = get_user_id();
$username = get_username();
$role = get_user_role();
$message = get_message();

// Ambil semua pesan yang dikirim dan diterima oleh user ini
$stmt_messages = $pdo->prepare("
    SELECT m.*, 
           sender.username AS sender_username, 
           receiver.username AS receiver_username,
           p.title AS product_title
    FROM messages m
    JOIN users sender ON m.sender_id = sender.id
    JOIN users receiver ON m.receiver_id = receiver.id
    LEFT JOIN products p ON m.product_id = p.id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmt_messages->execute([$user_id, $user_id]);
$all_messages = $stmt_messages->fetchAll();

// Group messages by conversation partner or product
$conversations = [];
foreach ($all_messages as $msg) {
    $partner_id = ($msg['sender_id'] == $user_id) ? $msg['receiver_id'] : $msg['sender_id'];
    $partner_username = ($msg['sender_id'] == $user_id) ? $msg['receiver_username'] : $msg['sender_username'];

    $key = '';
    if ($msg['product_id']) {
        $key = 'product_' . $msg['product_id'] . '_' . $partner_id;
    } else {
        $key = 'user_' . $partner_id;
    }
    
    if (!isset($conversations[$key])) {
        $conversations[$key] = [
            'product_id' => $msg['product_id'],
            'product_title' => $msg['product_title'],
            'partner_id' => $partner_id,
            'partner_username' => $partner_username,
            'messages' => []
        ];
    }
    $conversations[$key]['messages'][] = $msg;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Anda - Sistem Barang Bekas</title>
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
                <a href="edit_profile.php" class="text-white hover:text-blue-200">Edit Profil</a>
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Semua Pesan Anda</h2>
            <?php if (empty($conversations)): ?>
                <p class="text-gray-600">Anda belum memiliki riwayat pesan.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($conversations as $key => $conversation): ?>
                        <div class="border border-gray-200 p-4 rounded-md bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                Percakapan dengan: <span class="text-blue-600"><?php echo htmlspecialchars($conversation['partner_username']); ?></span>
                                <?php if ($conversation['product_title']): ?>
                                    <span class="text-sm text-gray-600 ml-2">(Tentang: <a href="product_detail.php?id=<?php echo $conversation['product_id']; ?>" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($conversation['product_title']); ?></a>)</span>
                                <?php endif; ?>
                            </h3>
                            <div class="mt-2 space-y-2 max-h-64 overflow-y-auto border-t border-gray-200 pt-2 pr-2">
                                <?php foreach ($conversation['messages'] as $msg): ?>
                                    <div class="flex <?php echo ($msg['sender_id'] == $user_id ? 'justify-end' : 'justify-start'); ?>">
                                        <div class="<?php echo ($msg['sender_id'] == $user_id ? 'bg-blue-100 text-gray-800' : 'bg-gray-200 text-gray-800'); ?> px-4 py-2 rounded-lg max-w-sm">
                                            <p class="font-semibold text-sm">
                                                <?php echo ($msg['sender_id'] == $user_id ? 'Anda' : htmlspecialchars($msg['sender_username'])); ?>
                                            </p>
                                            <p class="mt-1"><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></p>
                                            <p class="text-xs text-gray-500 mt-1 text-right"><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($role == 'buyer' && $conversation['partner_id'] != $user_id): // Buyer bisa membalas pesan ke seller ?>
                                <div class="mt-4">
                                    <h4 class="text-md font-semibold text-gray-700 mb-2">Balas Pesan:</h4>
                                    <form action="send_message.php" method="POST" class="space-y-2">
                                        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($conversation['partner_id']); ?>">
                                        <?php if ($conversation['product_id']): ?>
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($conversation['product_id']); ?>">
                                        <?php endif; ?>
                                        <textarea name="message_text" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik balasan Anda..." required></textarea>
                                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Kirim Balasan</button>
                                    </form>
                                </div>
                            <?php elseif ($role == 'seller' && $conversation['partner_id'] != $user_id): // Seller bisa membalas pesan ke buyer ?>
                                <div class="mt-4">
                                    <h4 class="text-md font-semibold text-gray-700 mb-2">Balas Pesan:</h4>
                                    <form action="send_message.php" method="POST" class="space-y-2">
                                        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($conversation['partner_id']); ?>">
                                        <?php if ($conversation['product_id']): ?>
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($conversation['product_id']); ?>">
                                        <?php endif; ?>
                                        <textarea name="message_text" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik balasan Anda..." required></textarea>
                                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">Kirim Balasan</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
