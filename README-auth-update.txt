PANDUAN IMPLEMENTASI SISTEM LOGIN DAN URL CLEAN

Hal-hal yang telah diimplementasikan:

1. File auth_check.php untuk pengecekan login
2. File init.php untuk inisialisasi koneksi dan pengecekan login
3. Update .htaccess untuk URL tanpa ekstensi .php

Langkah-langkah untuk menyelesaikan implementasi:

1. Pada setiap file PHP (kecuali index.php, login.php, dan logout.php), tambahkan include untuk init.php di awal file.
   Contoh:
   
   ```php
   <?php
   include 'init.php';
   
   // Kode lain di sini
   ?>
   ```

2. Pada file yang sudah ada include untuk koneksi.php dan session_start(), ganti dengan:
   
   ```php
   <?php
   include 'init.php';
   
   // Kode lain di sini
   ?>
   ```

3. Pastikan tidak ada penggunaan session_start() ganda dalam satu file.

4. Link pada navbar dan halaman lain tidak perlu lagi menambahkan .php. Contoh:
   - Dari: <a href="dashboard.php">Dashboard</a>
   - Menjadi: <a href="dashboard">Dashboard</a>

Dengan implementasi ini:
- User harus login untuk mengakses halaman manapun kecuali halaman login
- URL bisa diakses tanpa ekstensi .php
- Sistem akan redirect ke halaman login jika belum login 