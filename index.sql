
--membuat databes list music, buat jika jika tidak ada di awal
CREATE DATABASE IF NOT EXISTS music_db;
USE music_db;

--membuat tabel music llist dan atribut atributnya
CREATE TABLE IF NOT EXISTS music (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    album VARCHAR(255) NOT NULL,
    browser VARCHAR(255),
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);