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




