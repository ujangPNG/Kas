<?php
session_start();
include 'init.php';

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang masih kosong!'); window.location='transaksi.php';</script>";
    exit();
}

if (!isset($_POST['nama_pelanggan']) || empty(trim($_POST['nama_pelanggan']))) {
    echo "<script>alert('Nama pelanggan wajib diisi!'); window.location='transaksi.php';</script>";
    exit();
}

$nama_pelanggan = mysqli_real_escape_string($koneksi, trim($_POST['nama_pelanggan']));
$total_harga = 0;

foreach ($_SESSION['keranjang'] as $produk) {
    $total_harga += $produk['subtotal'];
}

$tanggal = date('Y-m-d H:i:s');

$result = mysqli_query($koneksi, "SELECT MAX(id) AS last_id FROM transaksi");
$row = mysqli_fetch_assoc($result);
$last_id = $row['last_id'] ? $row['last_id'] + 1 : 1;

$kode_transaksi = "TRX-" . date("Ymd") . "-" . str_pad($last_id, 4, "0", STR_PAD_LEFT);

$query_transaksi = "INSERT INTO transaksi (kode_transaksi, nama_pelanggan, total_harga, tanggal) 
                    VALUES ('$kode_transaksi', '$nama_pelanggan', '$total_harga', '$tanggal')";
if (mysqli_query($koneksi, $query_transaksi)) {
    $transaksi_id = mysqli_insert_id($koneksi);

    foreach ($_SESSION['keranjang'] as $produk) {
        $produk_id = mysqli_real_escape_string($koneksi, $produk['id']);
        $jumlah = mysqli_real_escape_string($koneksi, $produk['jumlah']);
        $subtotal = mysqli_real_escape_string($koneksi, $produk['subtotal']);

        $query_detail = "INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, subtotal) 
                         VALUES ('$transaksi_id', '$produk_id', '$jumlah', '$subtotal')";
        mysqli_query($koneksi, $query_detail);

        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE id = '$produk_id'");
    }

    unset($_SESSION['keranjang']);

    echo "<script>alert('Transaksi Berhasil!'); window.location='transaksi.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan transaksi.'); window.location='transaksi.php';</script>";
}
?>
