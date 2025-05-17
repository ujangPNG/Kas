<?php
session_start();
include 'init.php';

// Set default tanggal filter (30 hari terakhir)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days', strtotime($end_date)));

// Ambil tanggal filter dari request jika ada
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Hapus transaksi
if (isset($_GET['hapus'])) {
    $transaksi_id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    // Hapus detail transaksi terlebih dahulu
    $query_hapus_detail = "DELETE FROM detail_transaksi WHERE transaksi_id = '$transaksi_id'";
    mysqli_query($koneksi, $query_hapus_detail);

    // Hapus transaksi
    $query_hapus_transaksi = "DELETE FROM transaksi WHERE id = '$transaksi_id'";
    if (mysqli_query($koneksi, $query_hapus_transaksi)) {
        echo "<script>alert('Transaksi berhasil dihapus!'); window.location='laporan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus transaksi.'); window.location='laporan.php';</script>";
    }
}

// Ambil data transaksi + nama pelanggan berdasarkan filter tanggal
$query_transaksi = "SELECT id, kode_transaksi, tanggal, total_harga, nama_pelanggan 
                     FROM transaksi 
                     WHERE DATE(tanggal) BETWEEN '$start_date' AND '$end_date'
                     ORDER BY tanggal DESC";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);

if (!$result_transaksi) {
    die("Error fetching transactions: " . mysqli_error($koneksi));
}

// Ambil data total penjualan per hari dalam rentang tanggal
$query_penjualan_harian = "SELECT DATE(tanggal) AS tanggal_harian, SUM(total_harga) AS total_penjualan 
                           FROM transaksi 
                           WHERE DATE(tanggal) BETWEEN '$start_date' AND '$end_date' 
                           GROUP BY tanggal_harian 
                           ORDER BY tanggal_harian ASC";
$result_penjualan_harian = mysqli_query($koneksi, $query_penjualan_harian);

$data_penjualan = [];
while ($row = mysqli_fetch_assoc($result_penjualan_harian)) {
    $data_penjualan[$row['tanggal_harian']] = (float) $row['total_penjualan'];
}

// Ambil data total transaksi dalam rentang tanggal
$query_total_transaksi = "SELECT COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total_pendapatan 
                           FROM transaksi 
                           WHERE DATE(tanggal) BETWEEN '$start_date' AND '$end_date'";
$result_total_transaksi = mysqli_query($koneksi, $query_total_transaksi);
$data_total = mysqli_fetch_assoc($result_total_transaksi);

$jumlah_transaksi = $data_total['jumlah_transaksi'] ?? 0;
$total_pendapatan = $data_total['total_pendapatan'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="custom_style.php">
    <link rel="stylesheet" href="style.css"> <!-- Include style.css for custom styles -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Media print untuk tampilan cetak */
        @media print {
            .btn, .date-filter-form, .chart-container, .total-summary, .no-print {
                display: none !important; /* Sembunyikan elemen non-laporan saat cetak */
            }
             body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<?php include 'navbar.php'; ?>
<body class="with-navbar-padding">
    <div class="container mt-5">
        <h3>Laporan Transaksi</h3>

        <!-- Form Filter Tanggal -->
        <div class="date-filter-form">
            <form method="GET" action="laporan.php" class="d-flex align-items-center">
                <label for="start_date">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>" class="form-control w-auto me-2">
                <label for="end_date">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>" class="form-control w-auto me-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <!-- Grafik Total Penjualan Harian -->
        <h4>Grafik Penjualan Harian</h4>
        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Ringkasan Total -->
        <div class="total-summary mb-4">
            <h5>Ringkasan Periode <?= date('d-m-Y', strtotime($start_date)) ?> s/d <?= date('d-m-Y', strtotime($end_date)) ?></h5>
            <p>Total Transaksi: <strong><?= $jumlah_transaksi ?></strong></p>
            <p>Total Pendapatan: <strong>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></strong></p>
        </div>

        <!-- Tabel Laporan Transaksi (Scrollable) -->
        <h4>Daftar Transaksi</h4>
        <div class="table-container">
            <table class="table table-bordered m-0">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Total Harga</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                        <?php while ($transaksi = mysqli_fetch_assoc($result_transaksi)): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaksi['kode_transaksi']); ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($transaksi['tanggal'])); ?></td>
                                <td><?= htmlspecialchars($transaksi['nama_pelanggan']); ?></td>
                                <td>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.'); ?></td>
                                <td class="no-print">
                                    <a href="detail_transaksi.php?id=<?= $transaksi['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                                    <a href="?hapus=<?= $transaksi['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data transaksi pada periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Cetak Laporan -->
        <button class="btn btn-success mt-3 no-print" onclick="window.print()">Cetak Laporan</button>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-3 no-print">
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
    // Data penjualan harian dari PHP
    var salesData = <?= json_encode($data_penjualan); ?>;

    var dates = Object.keys(salesData);
    var totals = Object.values(salesData);

    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line', // Bisa diganti bar jika suka
        data: {
            labels: dates,
            datasets: [{
                label: 'Total Penjualan',
                data: totals,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            // Format rupiah
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Update form filter dengan tanggal saat ini (jika belum diset)
    window.onload = function() {
        if (!document.getElementById('start_date').value) {
            document.getElementById('start_date').value = "<?= $start_date ?>";
        }
         if (!document.getElementById('end_date').value) {
            document.getElementById('end_date').value = "<?= $end_date ?>";
        }
    }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
