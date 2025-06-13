<?php
require_once __DIR__ . '/../src/config.php';

// Mencoba memasukkan data ke trigger tabel 'log messages'
try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 2, 5, "Sudah malam atau sudah tau?"]);

    // Jika berhasil, akan otomatis menambahkan ke tabel 'log_messages' melalui trigger
    echo "Pesan berhasil ditambahkan ke 'messages'.<br>";
    echo "Trigger otomatis menambahkan ke 'log_messages'.";
} catch (PDOException $e) {
    echo "Gagal menambahkan pesan: " . $e->getMessage();
}
