-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Bulan Mei 2025 pada 04.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir_db`
--
CREATE DATABASE IF NOT EXISTS `kasir_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kasir_db`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

DROP TABLE IF EXISTS `detail_transaksi`;
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_id` (`transaksi_id`),
  KEY `detail_transaksi_ibfk_2` (`produk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id`, `transaksi_id`, `produk_id`, `jumlah`, `subtotal`) VALUES
(28, 19, 15, 1, 1000),
(29, 19, 13, 2, 20000),
(30, 19, 14, 1, 15000),
(31, 19, 13, 1, 10000),
(32, 19, 14, 1, 15000),
(33, 19, 13, 1, 10000),
(34, 19, 14, 1, 15000),
(35, 19, 15, 1, 1000),
(36, 20, 13, 3, 30000),
(37, 20, 14, 4, 60000),
(38, 20, 20, 4, 48000),
(39, 20, 15, 2, 2000),
(40, 20, 18, 6, 240000),
(41, 21, 13, 4, 40000),
(42, 21, 14, 4, 60000),
(43, 21, 20, 4, 48000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `ikon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `ikon`) VALUES
(1, 'mandi', 'uploads/icons/1747288566_OIP.jpg'),
(2, 'dapur', 'uploads/icons/1745750884_images.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `alamat` text NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `background_color` varchar(20) DEFAULT '#f8f9fa',
  `font_family` varchar(100) DEFAULT 'Arial, sans-serif',
  `font_color` varchar(20) DEFAULT '#212529',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_toko`, `logo`, `alamat`, `kontak`, `background_color`, `font_family`, `font_color`) VALUES
(1, 'toko deel', 'uploads/Screenshot 2025-03-16 103642.png', 'Jl. Contoh No. 123, Kota', '081234567890', '#f8f9fa', 'Arial, sans-serif', '#212529');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

DROP TABLE IF EXISTS `pengguna`;
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `username`, `password`, `nama`) VALUES
(1, 'kasir1', '$2y$10$88LQUysgDk/UP.lQqLqAhuYH9T.YoEQw33T9B./kktuWNoqN4v8lK', 'Kasir Satu'),
(2, 'kasir2', '$2y$10$ZATyK9G99SNIxhDm6NjZjuelHaC6dLcs2swCLvEehZPZ9z6sgafeW', 'Kasir Dua');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

DROP TABLE IF EXISTS `produk`;
CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_produk` (`kode_produk`),
  KEY `fk_kategori` (`kategori_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `kode_produk`, `nama_produk`, `harga`, `stok`, `gambar`, `kategori_id`) VALUES
(13, 'sbn', 'sabun', 10000, 189, 'download.jpeg', 1),
(14, 'spo', 'sampo', 15000, 189, '20250427125059_HeadShouldersShampooLemonFreshSampo1_199ff2a3-8748-42a3-b402-b21dace37a00_900x897.jpg', 1),
(15, 'rco', 'royco', 1000, 196, '20250427125210_Royco Bumbu Pelezat Rasa Ayam 460g.jpg', 2),
(18, 'ikan', 'ikan enak nyam nyam', 40000, 194, 'Ikan-Kerapu.jpg', NULL),
(20, 'sik', 'sikat', 12000, 192, '20250515075802_R.jpg', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_operator` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  UNIQUE KEY `kode_transaksi_2` (`kode_transaksi`),
  KEY `id_operator` (`id_operator`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `kode_transaksi`, `nama_pelanggan`, `total_harga`, `tanggal`, `id_pelanggan`, `id_operator`) VALUES
(19, 'TRX-20250427-0001', 'aa', 87000, '2025-04-27 05:53:36', NULL, NULL),
(20, 'TRX-20250517-0020', 'yamyamg', 380000, '2025-05-17 06:52:04', NULL, NULL),
(21, 'TRX-20250517-0021', 'dani', 148000, '2025-05-17 07:02:24', NULL, NULL);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);


--
-- Metadata
--
USE `phpmyadmin`;

--
-- Metadata untuk tabel detail_transaksi
--

--
-- Metadata untuk tabel kategori
--

--
-- Metadata untuk tabel pengaturan
--

--
-- Metadata untuk tabel pengguna
--

--
-- Metadata untuk tabel produk
--

--
-- Metadata untuk tabel transaksi
--

--
-- Metadata untuk database kasir_db
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
