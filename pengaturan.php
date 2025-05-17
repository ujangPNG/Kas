<?php
include 'init.php';

// Ambil data pengaturan
$query = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data = mysqli_fetch_assoc($query);

// Update Pengaturan
if (isset($_POST['update'])) {
    $nama_toko = $_POST['nama_toko'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $logo = $data['logo']; // Default logo lama

    // Cek jika ada upload logo baru
    if ($_FILES['logo']['name'] != '') {
        $target_dir = "uploads/";
        $logo = $target_dir . basename($_FILES["logo"]["name"]);
        move_uploaded_file($_FILES["logo"]["tmp_name"], $logo);
    }

    // Update database
    $update_query = "UPDATE pengaturan SET 
                     nama_toko='$nama_toko', 
                     logo='$logo', 
                     alamat='$alamat', 
                     kontak='$kontak' 
                     WHERE id=1";
    mysqli_query($koneksi, $update_query);
    header("Location: pengaturan.php");
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
<body class="with-navbar-padding">

<?php include 'navbar.php'; ?>


<div class="container mt-4 mb-4">
    <h3>Pengaturan Toko</h3>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($data['nama_toko']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Logo Toko</label>
            <input type="file" name="logo" class="form-control">
            <?php if ($data['logo']) { ?>
                <img src="<?= htmlspecialchars($data['logo']) ?>" class="mt-2" style="max-width: 150px;">
            <?php } ?>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required><?= htmlspecialchars($data['alamat']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Kontak</label>
            <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($data['kontak']) ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Simpan Pengaturan Toko</button>
        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </form>

    <hr>
    <h5>Kostumisasi Tampilan Website (Personal, hanya di browser ini)</h5>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label>Warna Background</label>
            <input type="color" id="bgColor" class="form-control form-control-color" value="#f4f8ff">
        </div>
        <div class="col-md-3 mb-3">
            <label>Warna Font</label>
            <input type="color" id="fontColor" class="form-control form-control-color" value="#212529">
        </div>
        <div class="col-md-3 mb-3">
            <label>Warna Tombol</label>
            <input type="color" id="btnColor" class="form-control form-control-color" value="#2563eb">
        </div>
        <div class="col-md-3 mb-3">
            <label>Font Website</label>
            <select id="fontFamily" class="form-control">
                <option value="'Segoe UI', Arial, sans-serif">Segoe UI (Default)</option>
                <option value="Arial, Helvetica, sans-serif">Arial</option>
                <option value="'Roboto', Arial, sans-serif">Roboto</option>
                <option value="'Poppins', Arial, sans-serif">Poppins</option>
                <option value="'Open Sans', Arial, sans-serif">Open Sans</option>
                <option value="'Courier New', Courier, monospace">Courier New</option>
            </select>
        </div>
    </div>
    <button type="button" class="btn btn-success" onclick="saveThemeToCookie()">Simpan Tampilan</button>
    <button type="button" class="btn btn-secondary mt-2" onclick="resetThemeCookie()">Reset Default</button>
</div>
<script>
function setThemeCookie(name, value) {
    document.cookie = name + '=' + encodeURIComponent(value) + ';path=/;max-age=31536000';
}
function saveThemeToCookie() {
    setThemeCookie('theme_bg', document.getElementById('bgColor').value);
    setThemeCookie('theme_font', document.getElementById('fontFamily').value);
    setThemeCookie('theme_fontcolor', document.getElementById('fontColor').value);
    setThemeCookie('theme_btncolor', document.getElementById('btnColor').value);
    alert('Tampilan berhasil disimpan! Refresh halaman lain untuk melihat perubahan.');
}
function resetThemeCookie() {
    setThemeCookie('theme_bg', '#f4f8ff');
    setThemeCookie('theme_font', "'Segoe UI', Arial, sans-serif");
    setThemeCookie('theme_fontcolor', '#212529');
    setThemeCookie('theme_btncolor', '#2563eb');
    document.getElementById('bgColor').value = '#f4f8ff';
    document.getElementById('fontFamily').value = "'Segoe UI', Arial, sans-serif";
    document.getElementById('fontColor').value = '#212529';
    document.getElementById('btnColor').value = '#2563eb';
    alert('Tampilan direset ke default!');
}
// Load preferensi dari cookie saat halaman dibuka
window.addEventListener('DOMContentLoaded', function() {
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }
    var bg = getCookie('theme_bg');
    var font = getCookie('theme_font');
    var fontcolor = getCookie('theme_fontcolor');
    var btncolor = getCookie('theme_btncolor');
    if(bg) document.getElementById('bgColor').value = bg;
    if(font) document.getElementById('fontFamily').value = font;
    if(fontcolor) document.getElementById('fontColor').value = fontcolor;
    if(btncolor) document.getElementById('btnColor').value = btncolor;
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
