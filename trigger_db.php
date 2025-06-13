<?php
require_once 'src/config.php';

try {
    // Hapus trigger jika sudah ada
    $pdo->exec("DROP TRIGGER IF EXISTS log_message_trigger");

    // Trigger untuk mencatat pesan baru ke log_messages
    $trigger_sql = "
        CREATE TRIGGER log_message_trigger
        AFTER INSERT ON messages
        FOR EACH ROW
        BEGIN
            INSERT INTO log_messages (message_id,sender_id,receiver_id,product_id,message_text,created_at
            ) VALUES (NEW.id,NEW.sender_id,NEW.receiver_id,NEW.product_id,NEW.message_text,NOW()
            );
        END
    ";

    $pdo->exec($trigger_sql);
    echo "Trigger berhasil dibuat.";
} catch (PDOException $e) {
    echo "Gagal membuat trigger: " . $e->getMessage();
}
?>
