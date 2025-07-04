-- Nama Database: uap_pdt_db
CREATE DATABASE IF NOT EXISTS uap_pdt_db;

USE uap_pdt_db;
-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('seller', 'buyer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- ID Penjual
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255), -- URL gambar produk (untuk demo, bisa diganti dengan path file)
    status ENUM('available', 'sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Tabel messages
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    product_id INT, -- Opsional: jika pesan terkait produk tertentu
    message_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
);

-- Tabel wishlists
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- ID Pembeli
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, product_id), -- Mencegah duplikasi produk di wishlist
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS log_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    product_id INT,
    message_text TEXT,
    created_at DATETIME,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
);


DELIMITER $$

CREATE TRIGGER validate_message_trigger
BEFORE INSERT ON messages
FOR EACH ROW
BEGIN
    -- Validasi sender atau receiver kosong
    IF NEW.sender_id IS NULL OR NEW.receiver_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sender or receiver ID is missing';
    END IF;

    -- Validasi sender dan receiver tidak boleh sama
    IF NEW.sender_id = NEW.receiver_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sender and receiver cannot be the same';
    END IF;

    -- Validasi product_id jika diisi, harus ada di tabel products
    DECLARE _exists INT DEFAULT 0;
    IF NEW.product_id IS NOT NULL THEN
        SELECT COUNT(*) INTO _exists FROM products WHERE id = NEW.product_id;
        IF _exists = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product ID does not exist';
        END IF;
    END IF;
END$$

DELIMITER ;


DELIMITER $$

DROP PROCEDURE IF EXISTS get_user_conversations$$

CREATE PROCEDURE get_user_conversations(IN uid INT)
BEGIN
    -- Validasi: user ID harus valid
    IF NOT EXISTS (SELECT 1 FROM users WHERE id = uid) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid user ID';
    END IF;

    -- Ambil semua pesan yang dikirim/dibalas user
    SELECT 
        m.*, 
        sender.username AS sender_username, 
        receiver.username AS receiver_username,
        p.title AS product_title
    FROM messages m
    JOIN users sender ON m.sender_id = sender.id
    JOIN users receiver ON m.receiver_id = receiver.id
    LEFT JOIN products p ON m.product_id = p.id
    WHERE m.sender_id = uid OR m.receiver_id = uid
    ORDER BY m.created_at DESC;
END$$

DELIMITER;

DELIMITER $$

CREATE FUNCTION get_user_role_by_id(uid INT)
RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE role_value VARCHAR(20);
    SELECT role INTO role_value FROM users WHERE id = uid;
    RETURN role_value;
END $$

DELIMITER ;

DELIMITER $$

CREATE FUNCTION get_username_by_id(uid INT)
RETURNS VARCHAR(50)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE uname VARCHAR(50);
    SELECT username INTO uname FROM users WHERE id = uid;
    RETURN uname;
END $$

DELIMITER ;





