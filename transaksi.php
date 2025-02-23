<?php
session_start();
include 'koneksi.php';

// Pastikan session keranjang sudah ada dan berbentuk array
if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Menampilkan daftar produk dari database
$query_produk = "SELECT * FROM produk";
$result_produk = mysqli_query($koneksi, $query_produk);

if (!$result_produk) {
    die("Error fetching products: " . mysqli_error($koneksi));
}

// Menambahkan produk ke keranjang
if (isset($_POST['tambah_ke_keranjang'])) {
    $produk_id = (int) ($_POST['produk_id'] ?? 0);
    $jumlah = (int) ($_POST['jumlah'] ?? 1);

    if ($produk_id <= 0 || $jumlah <= 0) {
        echo "<script>alert('Produk atau jumlah tidak valid!'); window.location='transaksi.php';</script>";
        exit();
    }

    // Ambil data produk berdasarkan ID
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $produk_id);
    mysqli_stmt_execute($stmt);
    $result_detail = mysqli_stmt_get_result($stmt);
    $produk = mysqli_fetch_assoc($result_detail);

    if (!$produk) {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='transaksi.php';</script>";
        exit();
    }

    $subtotal = $produk['harga'] * $jumlah;

    // Cek apakah produk sudah ada di keranjang
    $found = false;
    foreach ($_SESSION['keranjang'] as &$item) {
        if ($item['id'] == $produk_id) {
            $item['jumlah'] += $jumlah;
            $item['subtotal'] = $item['harga'] * $item['jumlah'];
            $found = true;
            break;
        }
    }
    unset($item);

    // Tambah produk baru ke keranjang
    if (!$found) {
        $_SESSION['keranjang'][] = [
            'id' => $produk['id'],
            'nama' => $produk['nama_produk'],
            'harga' => $produk['harga'],
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }

    echo "<script>alert('Produk berhasil ditambahkan ke keranjang!'); window.location='transaksi.php';</script>";
}

// Hitung total harga
$total_harga = array_sum(array_column($_SESSION['keranjang'], 'subtotal'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Transaksi</h3>

        <!-- Daftar Produk -->
        <h4>Daftar Produk</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($produk = mysqli_fetch_assoc($result_produk)): ?>
                    <tr>
                        <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                        <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <form method="POST" action="transaksi.php" class="d-flex">
                                <input type="number" name="jumlah" value="1" min="1" class="form-control me-2" required>
                                <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">
                                <button type="submit" name="tambah_ke_keranjang" class="btn btn-success">Tambah</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php include 'navbar.php'; ?>
        <!-- Keranjang Belanja -->
        <h4>Keranjang Belanja</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['keranjang'])): ?>
                    <?php foreach ($_SESSION['keranjang'] as $produk): ?>
                        <tr>
                            <td><?= htmlspecialchars($produk['nama']); ?></td>
                            <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td><?= $produk['jumlah']; ?></td>
                            <td>Rp <?= number_format($produk['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">Keranjang kosong</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Harga -->
        <div class="mt-4">
            <strong>Total Harga:</strong>
            <span class="text-white bg-dark p-2 rounded">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
        </div>

        <!-- Form Simpan Transaksi -->
        <form method="POST" action="simpan_transaksi.php" class="mt-3">
            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" required>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Transaksi</button>
        </form>

        <!-- Tombol Cetak & Dashboard -->
        <button class="btn btn-success mt-3" onclick="window.print()">Cetak Transaksi</button>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
    </div>
</body>
</html>
