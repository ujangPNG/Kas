<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar - Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="custom_style.php">
    <script>
    (function() {
        function getCookie(name) {
            let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? decodeURIComponent(match[2]) : null;
        }
        var bg = getCookie('theme_bg');
        var font = getCookie('theme_font');
        var fontcolor = getCookie('theme_fontcolor');
        var btncolor = getCookie('theme_btncolor');
        if(bg) document.documentElement.style.setProperty('--bg-color', bg);
        if(font) document.documentElement.style.setProperty('--main-font', font);
        if(fontcolor) document.documentElement.style.setProperty('--font-color', fontcolor);
        if(btncolor) document.documentElement.style.setProperty('--btn-color', btncolor);
    })();
    </script>
    <style>
        /* Remove most inline navbar styles, now handled in style.css */
        .navbar {
            /* background: linear-gradient(90deg, #2563eb 70%, #ff9800 100%) !important; */
            /* box-shadow: 0 2px 12px rgba(37,99,235,0.07); */
            border-bottom: 2px solid #ff9800;
            min-height: 64px;
        }
        .navbar-brand {
            /* color: #fff !important; */
            font-size: 1.5rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }
        .nav-link {
            /* color: #fff !important; */
            font-size: 1.08rem;
            padding: 8px 18px !important;
            border-radius: 8px;
            margin-right: 8px;
        }
        .nav-link.active, .nav-link:focus {
            background: #e3f0ff;
            color: #2563eb !important;
        }
        .navbar-toggler {
            border: none;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        body {
            padding-top: 70px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="dashboard.php">TOKO BERKAH MAJU</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="transaksi.php">
                        <i class="bi bi-display"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="data_produk.php">
                        <i class="bi bi-bag"></i> Data Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laporan.php">
                        <i class="bi bi-bar-chart"></i> Data Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengaturan.php">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_kategori.php">
                        <i class="bi bi-basket"></i> Kelola Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
