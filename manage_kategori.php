<?php
include 'koneksi.php';

// Tambah kategori baru
if (isset($_POST['tambah'])) {
    $nama_kategori = $_POST['nama_kategori'];
    $ikon = $_POST['ikon'];
    
    $query = "INSERT INTO kategori (nama_kategori, ikon) VALUES ('$nama_kategori', '$ikon')";
    mysqli_query($koneksi, $query);
}

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM kategori WHERE id = $id");
}
$query = "SELECT * FROM kategori";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Kelola Kategori</h2>

        <!-- Form Tambah Kategori -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Tambah Kategori Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Ikon (opsional)</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Kategori</button>
                </form>
            </div>
        </div>

        <!-- Daftar Kategori -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Ikon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_kategori'] ?></td>
                    <td><?= $row['ikon'] ?></td>
                    <td>
                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
