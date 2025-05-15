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

// Ambil daftar kategori
$query_kategori = "SELECT * FROM kategori";
$result_kategori = mysqli_query($koneksi, $query_kategori);

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

}

// Fungsi untuk menambahkan produk berdasarkan kategori
if (isset($_POST['tambah_kategori'])) {
    $kategori_id = $_POST['kategori_id'];
    
    // Ambil semua produk dalam kategori
    $query_produk_kategori = "SELECT * FROM produk WHERE kategori_id = $kategori_id";
    $result_produk_kategori = mysqli_query($koneksi, $query_produk_kategori);
    
    while ($produk = mysqli_fetch_assoc($result_produk_kategori)) {
        $subtotal = $produk['harga'] * 1; // Default jumlah 1
        
        // Cek apakah produk sudah ada di keranjang
        $found = false;
        foreach ($_SESSION['keranjang'] as &$item) {
            if ($item['id'] == $produk['id']) {
                $item['jumlah'] += 1; // Tambah 1 ke jumlah yang sudah ada
                $item['subtotal'] = $item['harga'] * $item['jumlah'];
                $found = true;
                break;
            }
        }
        unset($item);
        
        // Tambahkan ke keranjang jika belum ada
        if (!$found) {
            $_SESSION['keranjang'][] = [
                'id' => $produk['id'],
                'nama' => $produk['nama_produk'],
                'harga' => $produk['harga'],
                'jumlah' => 1,
                'subtotal' => $subtotal
            ];
        }
    }
}

// Update jumlah produk di keranjang
if (isset($_POST['update_jumlah'])) {
    $index = (int) $_POST['index'];
    $jumlah = (int) $_POST['jumlah'];
    
    // Jika jumlah 0 atau negatif, hapus produk dari keranjang
    if ($jumlah <= 0) {
        array_splice($_SESSION['keranjang'], $index, 1);
    } else {
        // Update jumlah dan subtotal
        $_SESSION['keranjang'][$index]['jumlah'] = $jumlah;
        $_SESSION['keranjang'][$index]['subtotal'] = $_SESSION['keranjang'][$index]['harga'] * $jumlah;
    }
    
    // Redirect kembali ke halaman transaksi
    header("Location: transaksi.php");
    exit();
}

// Hitung total harga
$total_harga = array_sum(array_column($_SESSION['keranjang'], 'subtotal'));

// Mendapatkan produk berdasarkan kategori untuk tabel asosiasi
$kategori_dengan_produk = [];
$query_kategori_list = "SELECT * FROM kategori";
$result_kategori_list = mysqli_query($koneksi, $query_kategori_list);

while ($kategori = mysqli_fetch_assoc($result_kategori_list)) {
    $kategori_id = $kategori['id'];
    $produk_query = "SELECT nama_produk FROM produk WHERE kategori_id = $kategori_id";
    $produk_result = mysqli_query($koneksi, $produk_query);
    
    $produk_names = [];
    while ($produk = mysqli_fetch_assoc($produk_result)) {
        $produk_names[] = $produk['nama_produk'];
    }
    
    $kategori_dengan_produk[] = [
        'id' => $kategori_id,
        'nama' => $kategori['nama_kategori'],
        'produk_list' => implode(', ', $produk_names)
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .scrollable-content {
            height: 400px;
            overflow-y: auto;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 0;
        }
        .quantity-control input {
            width: 50px;
            text-align: center;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5 mb-5">
        <h3>Transaksi</h3>
        
        <div class="row">
            <!-- Daftar Produk (Kiri) -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Daftar Produk</h5>
                    </div>
                    <div class="scrollable-content">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr class="sticky-header">
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset pointer produk
                                mysqli_data_seek($result_produk, 0);
                                while ($produk = mysqli_fetch_assoc($result_produk)): 
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                        <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                                        <td><?= htmlspecialchars($produk['stok']); ?></td>
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
                    </div>
                </div>
            </div>
            
            <!-- Preset Kategori (Kanan) -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Preset Kategori</h5>
                    </div>
                    <div class="scrollable-content">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr class="sticky-header">
                                    <th>Nama Kategori</th>
                                    <th>Produk Terasosiasi</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset pointer kategori
                                mysqli_data_seek($result_kategori, 0);
                                while ($kategori = mysqli_fetch_assoc($result_kategori)): 
                                    // Dapatkan daftar produk untuk kategori ini
                                    $kategori_id = $kategori['id'];
                                    $produk_query = "SELECT nama_produk FROM produk WHERE kategori_id = $kategori_id";
                                    $produk_result = mysqli_query($koneksi, $produk_query);
                                    
                                    $produk_names = [];
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        $produk_names[] = $produk['nama_produk'];
                                    }
                                    
                                    $produk_list = implode(', ', $produk_names);
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($kategori['nama_kategori']); ?></td>
                                        <td>
                                            <?php if (!empty($produk_list)): ?>
                                                <?= htmlspecialchars($produk_list); ?>
                                            <?php else: ?>
                                                <em>Tidak ada produk</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="kategori_id" value="<?= $kategori['id']; ?>">
                                                <button type="submit" name="tambah_kategori" class="btn btn-primary btn-sm">Tambah</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
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
                    <?php foreach ($_SESSION['keranjang'] as $index => $produk): ?>
                        <tr>
                            <td><?= htmlspecialchars($produk['nama']); ?></td>
                            <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST" action="" class="quantity-control">
                                    <input type="hidden" name="index" value="<?= $index; ?>">
                                    <div class="d-flex align-items-center">
                                        <button type="submit" name="update_jumlah" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('qty_<?= $index; ?>').value--;">-</button>
                                        <input type="number" id="qty_<?= $index; ?>" name="jumlah" value="<?= $produk['jumlah']; ?>" min="0" class="form-control mx-2" style="width: 70px;" onChange="this.form.submit()">
                                        <button type="submit" name="update_jumlah" class="btn btn-outline-success btn-sm" onclick="document.getElementById('qty_<?= $index; ?>').value++;">+</button>
                                    </div>
                                </form>
                            </td>
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
