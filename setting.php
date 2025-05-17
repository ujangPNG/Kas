<?php
include 'init.php';

// Ambil data pengaturan dari database
$query = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
$pengaturan = mysqli_fetch_assoc($query);

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $nama_toko = $_POST['nama_toko'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $logo = $pengaturan['logo'];
    $background_color = isset($_POST['background_color']) ? $_POST['background_color'] : $pengaturan['background_color'];
    $font_family = isset($_POST['font_family']) ? $_POST['font_family'] : $pengaturan['font_family'];
    $font_color = isset($_POST['font_color']) ? $_POST['font_color'] : $pengaturan['font_color'];

    // Jika ada upload logo baru
    if (!empty($_FILES['logo']['name'])) {
        $file_name = "logo_" . time() . ".png";
        move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/" . $file_name);
        $logo = $file_name;
    }

    // Update database
    mysqli_query($koneksi, "UPDATE pengaturan SET nama_toko='$nama_toko', logo='$logo', alamat='$alamat', kontak='$kontak', background_color='$background_color', font_family='$font_family', font_color='$font_color' WHERE id=1");

    echo "<script>alert('Pengaturan berhasil diperbarui!'); window.location='setting.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pengaturan Toko</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Toko</label>
                <input type="text" name="nama_toko" class="form-control" value="<?= $pengaturan['nama_toko']; ?>">
            </div>
            <div class="mb-3">
                <label>Logo Toko</label><br>
                <img src="uploads/<?= $pengaturan['logo']; ?>" width="100"><br>
                <input type="file" name="logo" class="form-control">
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control"><?= $pengaturan['alamat']; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?= $pengaturan['kontak']; ?>">
            </div>
            <div class="mb-3">
                <label>Warna Background</label>
                <input type="color" name="background_color" class="form-control form-control-color" value="<?= htmlspecialchars($pengaturan['background_color']); ?>">
            </div>
            <div class="mb-3">
                <label>Font Family</label>
                <input type="text" name="font_family" class="form-control" value="<?= htmlspecialchars($pengaturan['font_family']); ?>" placeholder="Contoh: Arial, sans-serif">
            </div>
            <div class="mb-3">
                <label>Warna Font</label>
                <input type="color" name="font_color" class="form-control form-control-color" value="<?= htmlspecialchars($pengaturan['font_color']); ?>">
            </div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>
</html>
