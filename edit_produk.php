<?php
include 'koneksi.php';
include 'navbar.php';

// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori";
$result_kategori = mysqli_query($koneksi, $query_kategori);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id");
    $row = mysqli_fetch_assoc($query);
}

if (isset($_POST['update'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori_id = !empty($_POST['kategori']) ? $_POST['kategori'] : null;
    
    // Cek apakah ada file gambar yang diupload
    if ($_FILES['gambar']['name']) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Buat nama file unik
            $newname = date('YmdHis') . '_' . $filename;
            $upload_path = "assets/" . $newname;
            
            if(move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $newname;
                
                // Update database dengan gambar baru
                mysqli_query($koneksi, "UPDATE produk SET 
                    nama_produk='$nama_produk', 
                    harga='$harga', 
                    stok='$stok', 
                    gambar='$gambar',
                    kategori_id=" . ($kategori_id ? "'$kategori_id'" : "NULL") . " 
                    WHERE id=$id");
            } else {
                echo "Gagal mengupload file!";
                exit;
            }
        } else {
            echo "Tipe file tidak diizinkan!";
            exit;
        }
    } else {
        // Update database tanpa mengubah gambar
        mysqli_query($koneksi, "UPDATE produk SET 
            nama_produk='$nama_produk', 
            harga='$harga', 
            stok='$stok',
            kategori_id=" . ($kategori_id ? "'$kategori_id'" : "NULL") . " 
            WHERE id=$id");
    }

    header("Location: data_produk.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="with-navbar-padding">
    <div class="container mt-5">
        <h2>Edit Produk</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="<?php echo $row['nama_produk']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" value="<?php echo $row['harga']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stok" class="form-control" value="<?php echo $row['stok']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Kategori (Opsional)</label>
                <select name="kategori" class="form-control">
                    <option value="">Pilih Kategori</option>
                    <?php 
                    // Reset pointer kategori
                    mysqli_data_seek($result_kategori, 0);
                    while ($kategori = mysqli_fetch_assoc($result_kategori)): 
                        $selected = ($kategori['id'] == $row['kategori_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $kategori['id'] ?>" <?= $selected ?>><?= $kategori['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" class="form-control">
                <?php if(!empty($row['gambar'])): ?>
                    <p class="mt-2">Gambar Saat Ini:</p>
                    <?php if(file_exists("assets/" . $row['gambar'])): ?>
                        <img src="assets/<?php echo $row['gambar']; ?>" width="100" class="mt-2">
                    <?php else: ?>
                        <p class="text-danger">Gambar tidak ditemukan</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <button type="submit" name="update" class="btn btn-primary mt-3">Simpan Perubahan</button>
            <a href="data_produk.php" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</body>
</html>
