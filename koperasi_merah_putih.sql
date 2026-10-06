-- =========================================================
-- Database: Sistem Informasi Manajemen Koperasi Desa Merah Putih
-- Unit usaha: penjualan sembako
-- =========================================================

CREATE DATABASE IF NOT EXISTS koperasi_merah_putih;
USE koperasi_merah_putih;

-- ---------------------------------------------------------
-- 1. kategori_barang
-- ---------------------------------------------------------
CREATE TABLE kategori_barang (
    id_kategori    INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori  VARCHAR(50) NOT NULL
);

-- ---------------------------------------------------------
-- 2. barang
-- ---------------------------------------------------------
CREATE TABLE barang (
    id_barang      INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori    INT,
    nama_barang    VARCHAR(100) NOT NULL,
    satuan         VARCHAR(20) NOT NULL,
    harga          DECIMAL(12,2) NOT NULL,
    stok           INT NOT NULL DEFAULT 0,
    stok_minimum   INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_kategori) REFERENCES kategori_barang(id_kategori)
);

-- ---------------------------------------------------------
-- 3. pemasok
-- ---------------------------------------------------------
CREATE TABLE pemasok (
    id_pemasok     INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemasok   VARCHAR(100) NOT NULL,
    kontak         VARCHAR(50),
    alamat         VARCHAR(150)
);

-- ---------------------------------------------------------
-- 4. pengguna
-- ---------------------------------------------------------
CREATE TABLE pengguna (
    id_pengguna    INT AUTO_INCREMENT PRIMARY KEY,
    nama           VARCHAR(100) NOT NULL,
    username       VARCHAR(50) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,
    role           ENUM('kasir','petugas_persediaan','pengurus') NOT NULL,
    status         ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
);

-- ---------------------------------------------------------
-- 5. penerimaan_barang (header)
-- ---------------------------------------------------------
CREATE TABLE penerimaan_barang (
    id_penerimaan  INT AUTO_INCREMENT PRIMARY KEY,
    tanggal        DATE NOT NULL,
    id_pemasok     INT,
    id_petugas     INT,
    FOREIGN KEY (id_pemasok) REFERENCES pemasok(id_pemasok),
    FOREIGN KEY (id_petugas) REFERENCES pengguna(id_pengguna)
);

-- ---------------------------------------------------------
-- 6. detail_penerimaan
-- ---------------------------------------------------------
CREATE TABLE detail_penerimaan (
    id_detail      INT AUTO_INCREMENT PRIMARY KEY,
    id_penerimaan  INT NOT NULL,
    id_barang      INT NOT NULL,
    jumlah         INT NOT NULL,
    harga_beli     DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_penerimaan) REFERENCES penerimaan_barang(id_penerimaan),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- ---------------------------------------------------------
-- 7. transaksi_penjualan (header)
-- ---------------------------------------------------------
CREATE TABLE transaksi_penjualan (
    id_transaksi     INT AUTO_INCREMENT PRIMARY KEY,
    tanggal          DATE NOT NULL,
    id_kasir         INT,
    total_transaksi  DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_kasir) REFERENCES pengguna(id_pengguna)
);

-- ---------------------------------------------------------
-- 8. detail_transaksi
-- ---------------------------------------------------------
CREATE TABLE detail_transaksi (
    id_detail      INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi   INT NOT NULL,
    id_barang      INT NOT NULL,
    jumlah         INT NOT NULL,
    harga_satuan   DECIMAL(12,2) NOT NULL,
    subtotal       DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi_penjualan(id_transaksi),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- ---------------------------------------------------------
-- 9. pembayaran (1:1 dengan transaksi_penjualan)
-- ---------------------------------------------------------
CREATE TABLE pembayaran (
    id_pembayaran      INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi       INT NOT NULL UNIQUE,
    total_bayar        DECIMAL(12,2) NOT NULL,
    metode_pembayaran  ENUM('tunai','non-tunai') NOT NULL,
    status_pembayaran  ENUM('lunas','belum') NOT NULL DEFAULT 'lunas',
    tanggal_bayar      DATE NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi_penjualan(id_transaksi)
);

-- ---------------------------------------------------------
-- 10. mutasi_persediaan (kartu stok / ledger)
-- ---------------------------------------------------------
CREATE TABLE mutasi_persediaan (
    id_mutasi      INT AUTO_INCREMENT PRIMARY KEY,
    id_barang      INT NOT NULL,
    tanggal        DATE NOT NULL,
    jenis_mutasi   ENUM('masuk','keluar') NOT NULL,
    jumlah         INT NOT NULL,
    stok_akhir     INT NOT NULL,
    referensi      VARCHAR(50) COMMENT 'id_penerimaan atau id_transaksi terkait',
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- =========================================================
-- Data contoh (opsional, untuk pengujian)
-- =========================================================

INSERT INTO kategori_barang (nama_kategori) VALUES
('Beras & Gandum'), ('Minyak & Gula'), ('Bahan Pokok Lainnya');

INSERT INTO barang (id_kategori, nama_barang, satuan, harga, stok, stok_minimum) VALUES
(1, 'Beras Premium 5kg', 'karung', 65000, 40, 10),
(2, 'Minyak Goreng 1L', 'botol', 18000, 25, 15),
(2, 'Gula Pasir 1kg', 'bungkus', 15000, 8, 10),
(3, 'Telur Ayam', 'kg', 27000, 20, 5);

INSERT INTO pemasok (nama_pemasok, kontak, alamat) VALUES
('CV Sumber Sembako', '0812xxxxxxx', 'Malang'),
('UD Tani Makmur', '0813xxxxxxx', 'Malang');

INSERT INTO pengguna (nama, username, password, role) VALUES
('Budi Santoso', 'budi.kasir', 'hashed_password', 'kasir'),
('Sari Wulandari', 'sari.gudang', 'hashed_password', 'petugas_persediaan'),
('Ahmad Fauzi', 'ahmad.pengurus', 'hashed_password', 'pengurus');
