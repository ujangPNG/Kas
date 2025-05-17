<?php
include 'init.php';

// Tambah kategori baru
if (isset($_POST['tambah'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    
    // Upload ikon
    $ikon = null;
    if(isset($_FILES['ikon']) && $_FILES['ikon']['error'] == 0) {
        $target_dir = "uploads/icons/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = basename($_FILES["ikon"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["ikon"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (move_uploaded_file($_FILES["ikon"]["tmp_name"], $target_file)) {
                    $ikon = $target_file;
                }
            }
        }
    }
    
    $query = "INSERT INTO kategori (nama_kategori, ikon) VALUES ('$nama_kategori', " . ($ikon ? "'$ikon'" : "NULL") . ")";
    mysqli_query($koneksi, $query);
}

// Edit kategori
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    
    // Upload ikon
    $ikon_update = "";
    if(isset($_FILES['ikon']) && $_FILES['ikon']['error'] == 0) {
        $target_dir = "uploads/icons/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = basename($_FILES["ikon"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["ikon"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (move_uploaded_file($_FILES["ikon"]["tmp_name"], $target_file)) {
                    // Delete old icon if exists
                    $old_icon_query = "SELECT ikon FROM kategori WHERE id = $id";
                    $old_icon_result = mysqli_query($koneksi, $old_icon_query);
                    $old_icon = mysqli_fetch_assoc($old_icon_result);
                    if($old_icon['ikon'] && file_exists($old_icon['ikon'])) {
                        unlink($old_icon['ikon']);
                    }
                    
                    $ikon_update = ", ikon = '$target_file'";
                }
            }
        }
    }
    
    $query = "UPDATE kategori SET nama_kategori = '$nama_kategori' $ikon_update WHERE id = $id";
    mysqli_query($koneksi, $query);
}

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Delete icon file if exists
    $icon_query = "SELECT ikon FROM kategori WHERE id = $id";
    $icon_result = mysqli_query($koneksi, $icon_query);
    $icon = mysqli_fetch_assoc($icon_result);
    if($icon['ikon'] && file_exists($icon['ikon'])) {
        unlink($icon['ikon']);
    }
    
    mysqli_query($koneksi, "DELETE FROM kategori WHERE id = $id");
}

// Get kategori for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_query = "SELECT * FROM kategori WHERE id = $id";
    $edit_result = mysqli_query($koneksi, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
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
    <link rel="stylesheet" href="custom_style.php">
</head>
<body class="with-navbar-padding">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Kelola Kategori</h2>

        <!-- Form Tambah/Edit Kategori -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" value="<?= $edit_data ? $edit_data['nama_kategori'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Ikon (opsional)</label>
                        <input type="file" name="ikon" class="form-control">
                        <?php if($edit_data && $edit_data['ikon']): ?>
                            <div class="mt-2">
                                <small>Ikon saat ini: <?= $edit_data['ikon'] ?></small>
                                <?php if(file_exists($edit_data['ikon'])): ?>
                                    <img src="<?= $edit_data['ikon'] ?>" alt="Icon" style="max-height: 50px; max-width: 50px;" class="d-block mt-1">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-success">Simpan Perubahan</button>
                        <a href="manage_kategori.php" class="btn btn-secondary">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-primary">Tambah Kategori</button>
                    <?php endif; ?>
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
                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                    <td>
                        <?php if($row['ikon'] && file_exists($row['ikon'])): ?>
                            <img src="<?= $row['ikon'] ?>" alt="Icon" style="max-height: 128px; max-width: 128px;">
                        <?php else: ?>
                            <span class="text-muted">Tidak ada ikon</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>