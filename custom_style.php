<?php
include 'init.php';
$query = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
$pengaturan = mysqli_fetch_assoc($query);
header('Content-Type: text/css');
$bg = !empty($pengaturan['background_color']) ? $pengaturan['background_color'] : '#f8f9fa';
$font = !empty($pengaturan['font_family']) ? $pengaturan['font_family'] : 'Arial, sans-serif';
$color = !empty($pengaturan['font_color']) ? $pengaturan['font_color'] : '#212529';
?>
body {
    background: <?= json_encode($bg) ?> !important;
    font-family: <?= json_encode($font) ?> !important;
    color: <?= json_encode($color) ?> !important;
} 