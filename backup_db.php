<?php
require_once 'src/config.php';

// tanggal untuk nama file backup
$tanggal_back = date('Y-m-d');

// nama file backup
$backup_file = __DIR__ . '/src/backup/' . $tanggal_back . '.sql';

// Cek apakah file backup sudah ada
if (!file_exists(dirname($backup_file))) {
    mkdir(dirname($backup_file), 0777, true);
}

// command untuk backup database
$command = "\"D:\\Laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe\" -u {$user}";

if (!empty($pass)) {
    $command .= " -p{$pass}";
}
$command .= " {$db} > \"$backup_file\"";
exec($command, $output, $return_var);

// Cek hasil backup
if ($return_var === 0 && file_exists($backup_file)) {
    echo "✅ Backup Berhasil, tersimpan di: <code>$backup_file</code>";
} else {
    echo "❌ Backup Gagal.<br>";
    echo "Perintah: <code>$command</code><br>";
    echo "Output: <pre>" . print_r($output, true) . "</pre>";
}
?>
