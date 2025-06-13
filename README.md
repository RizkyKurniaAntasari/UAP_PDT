# 📰 PosterTalk (Platform jual beli poster digital)
PosterTalk merupakan platform jual beli poster digital yang memungkinkan untuk mengunggah dan menjual karyamu sendiri kepada khalayak ramai. PosterTalk juga memungkinkan penjual dan pembeli untuk saling terhubung melalui fitur chat yang tersedia.

### 📌 Tujuan Pembuatan PosterTalk
Tujuan dari pembangunan sistem ini adalah untuk menyediakan platform jual beli poster yang efisien dan aman, dengan menerapkan fitur-fitur penting dari sistem database seperti stored procedure, function, serta perancangan yang dapat dikembangkan untuk mendukung transaction dan trigger.

### 💻 Arsitektur Platform
Dibangun menggunakan bahasa PHP dan database MySQL yang mendukung fitur-fitur seperti **stored procedure, function**, serta perancangan yang dapat dikembangkan untuk mendukung **transaction dan trigger**.

![Image](https://github.com/user-attachments/assets/4dc54041-c6e5-401f-9c10-90f62d16503b)
![Image](https://github.com/user-attachments/assets/abfe046b-e29a-42c6-b15d-457c9cfefc25)
![Image](https://github.com/user-attachments/assets/abc6743d-1f96-4af7-99fa-e92d16b6e3fd)
![Image](https://github.com/user-attachments/assets/adc709c5-73de-47e5-83b9-392442d3d947)

# Stored Procedure
Sistem menggunakan stored procedure untuk menangani logika kompleks secara langsung di sisi database, seperti:
1. Menampilkan semua pesan yang terlibat dengan pengguna tertentu (baik sebagai pengirim maupun penerima), lengkap dengan nama pengguna dan judul produk terkait.
2. Stored procedure: get_user_conversations(uid INT)
3. Cuplikan kode:
   
![Image](https://github.com/user-attachments/assets/08bb882f-8b41-4a2f-bdf4-1507ad2cd048)

# Function
Sistem memanfaatkan function untuk mengambil informasi tertentu secara efisien, contohnya:
1. Mendapatkan peran pengguna (buyer atau seller) berdasarkan ID-nya.
2. Function: get_user_role_by_id(uid INT)
3. Cuplikan kode:
   
![Image](https://github.com/user-attachments/assets/7824953d-3f27-454a-adaa-a5ed3a565d3e)

# Trigger
trigger `log_message_trigger` berfungsi sebagai sistem pencatat otomatis yang aktif setelah data pesan masuk ke sistem. Trigger ini merekam setiap pesan yang dikirim, memastikan tidak ada aktivitas komunikasi yang terlewatkan dari log historis.

`schema.sql`

![Image](https://github.com/user-attachments/assets/c458001e-7245-4f00-a15a-6d48c47d4e3f)

`trigger_db.php`

![Image](https://github.com/user-attachments/assets/081cc899-f594-435f-a801-c6e811ddc845)

### ⚙️ Aktif di Proses Berikut
Trigger ini aktif saat akan menyisipkan (INSERT) data ke tabel messages, baik melalui:
1. Fitur chat antar pengguna
2. Kirim pesan terkait produk
3. Sistem notifikasi atau auto-reply

### 🛡️ Peran Penting Trigger Ini:
1. Menolak pesan tanpa pengirim atau penerima
2. Mencegah pesan dikirim ke diri sendiri
3. Memverifikasi bahwa produk yang disebut memang benar-benar ada

# 🔄 Transaction
Dalam sistem, proses pengiriman pesan antar pengguna — seperti pembeli dan penjual — tidak cukup hanya menyisipkan data ke dalam tabel. Dibutuhkan jaminan bahwa semua tahapan validasi dan penyimpanan dilakukan dengan benar agar tidak terjadi inkonsistensi, misalnya pesan masuk ke database meskipun penerima tidak valid.
Untuk menjamin integritas proses ini, digunakan mekanisme transaksi database melalui beginTransaction(), commit(), dan rollBack() pada PDO.

### Alur Proses:
### 1. Validasi Data
a. Memastikan receiver_id dan message_text tidak kosong.
b. Mencegah pengguna mengirim pesan ke dirinya sendiri.
### 2. Transaksi Database
a. Proses dimulai dengan beginTransaction().
b. Sistem memeriksa apakah penerima pesan (receiver_id) benar-benar terdaftar di database.
c. Jika penerima valid, data pesan disimpan ke dalam tabel messages.
d. Jika semua langkah berhasil, maka transaksi diselesaikan dengan commit().
e. Namun jika terjadi kesalahan — seperti penerima tidak ditemukan — maka seluruh proses dibatalkan dengan rollBack().
Dengan pendekatan ini, sistem memastikan bahwa tidak ada pesan tersimpan secara parsial atau menuju pengguna yang tidak sah.

`send_message.php`

![Image](https://github.com/user-attachments/assets/bc44bd20-627c-4a25-98fb-15a07ff0ad9b)

# 🔄 Backup Otomatis
Untuk menjaga integritas dan ketersediaan data, sistem ini dilengkapi fitur backup otomatis menggunakan mysqldump yang dijalankan melalui task scheduler. Proses backup dilakukan secara berkala dan disimpan dalam direktori khusus dengan format nama file yang mencantumkan tanggal sehingga memudahkan pelacakan. Semua file backup disimpan di `folder src/backup/` dalam format `.sql`.

`backup_db.php`

![Image](https://github.com/user-attachments/assets/78df4248-be55-4c79-b12b-0d063e722710)

Backup ini dapat dijadwalkan secara otomatis menggunakan fitur Task Scheduler (Windows) agar berjalan rutin sesuai kebutuhan, misalnya harian atau mingguan.





