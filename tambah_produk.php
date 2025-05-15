<?php
include 'koneksi.php';
include 'navbar.php';


// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori";
$result_kategori = mysqli_query($koneksi, $query_kategori);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori_id = !empty($_POST['kategori']) ? $_POST['kategori'] : null;
    
    // Upload gambar dengan penanganan lebih baik
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Buat nama file unik
            $newname = date('YmdHis') . '_' . $filename;
            $upload_path = "assets/" . $newname;
            
            if(move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $newname;
            } else {
                echo "Gagal mengupload file!";
                exit;
            }
        } else {
            echo "Tipe file tidak diizinkan!";
            exit;
        }
    } else {
        $gambar = ''; // Jika tidak ada file yang diupload
    }

    $query = "INSERT INTO produk (kode_produk, nama_produk, harga, stok, gambar, kategori_id) 
              VALUES ('$kode_produk', '$nama_produk', '$harga', '$stok', '$gambar', " . ($kategori_id ? "'$kategori_id'" : "NULL") . ")";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: data_produk.php");
    } else {
        echo "Gagal menambahkan produk!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Tambah Produk</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Kode Produk</label>
                <input type="text" name="kode_produk" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stok" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Kategori (Opsional)</label>
                <select name="kategori" class="form-control">
                    <option value="">Pilih Kategori</option>
                    <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                        <option value="<?= $kategori['id'] ?>"><?= $kategori['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="data_produk.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
