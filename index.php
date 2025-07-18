<?php
// Tampilkan semua error saat pengembangan
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// File CSV yang digunakan
$data_penjualan_file = 'data/data_penjualan.csv'; // File transaksi penjualan
$data_produk_file = 'data/data_produk.csv';       // File produk

// Inisialisasi array
$penjualan = [];            // Menampung semua transaksi penjualan
$produk_kategori = [];      // Mapping nama produk ke kategori produk

// ==================== BACA DATA PENJUALAN ====================
if (file_exists($data_penjualan_file)) {              // Cek apakah file penjualan ada
    $file = fopen($data_penjualan_file, 'r');         // Buka file
    fgetcsv($file);                                   // Lewati baris header
    while (($row = fgetcsv($file)) !== false) {       // Loop setiap baris
        $penjualan[] = $row;                          // Tambahkan ke array
    }
    fclose($file);                                     // Tutup file
}

// DEBUG: tampilkan data penjualan
// echo "<pre>DATA PENJUALAN:\n";
// var_dump($penjualan);
// echo "</pre>";

// ==================== BACA DATA PRODUK & KATEGORI ====================
if (file_exists($data_produk_file)) {                // Cek file produk ada
    $file = fopen($data_produk_file, 'r');           // Buka file
    fgetcsv($file);                                   // Lewati header
    while (($row = fgetcsv($file)) !== false) {
        $produk_kategori[$row[0]] = $row[1];          // Simpan: nama_produk => nama_kategori
    }
    fclose($file);
}

// DEBUG: tampilkan mapping produk -> kategori
// echo "<pre>DATA PRODUK -> KATEGORI:\n";
// var_dump($produk_kategori);
// echo "</pre>";

// ==================== INISIALISASI ====================
$bulan_terakhir = [];             // Menyimpan jumlah item per bulan
$produk_terjual = [];            // Menyimpan total item per produk
$terlaris_kategori = [];         // Menyimpan item terbanyak per kategori
$total_penghasilan = 0;         // Total penjualan semua produk
$penghasilan_per_produk = [];   // Total uang per produk

// ==================== PROSES TIAP TRANSAKSI ====================
foreach ($penjualan as $p) {
    $tanggal = $p[0];                // Tanggal penjualan
    $produk = $p[1];                 // Nama produk
    $item = (int)$p[2];              // Jumlah item (integer)
    $penjualan_rp = (int)$p[3];      // Total uang transaksi (integer)

    // Ambil bulan dari tanggal (format YYYY-MM)
    $bulan = date('Y-m', strtotime($tanggal));
    if (!isset($bulan_terakhir[$bulan])) $bulan_terakhir[$bulan] = 0;
    $bulan_terakhir[$bulan] += $item; // Tambah item ke bulan tersebut

    // Hitung total item per produk
    if (!isset($produk_terjual[$produk])) $produk_terjual[$produk] = 0;
    $produk_terjual[$produk] += $item;

    // Ambil kategori dari nama produk
    $kategori = $produk_kategori[$produk] ?? 'Tidak Diketahui';

    // Simpan produk terlaris per kategori
    if (!isset($terlaris_kategori[$kategori][$produk])) {
        $terlaris_kategori[$kategori][$produk] = $item;
    } else {
        $terlaris_kategori[$kategori][$produk] += $item;
    }

    // Total semua penghasilan
    $total_penghasilan += $penjualan_rp;

    // Total penghasilan per produk
    if (!isset($penghasilan_per_produk[$produk])) $penghasilan_per_produk[$produk] = 0;
    $penghasilan_per_produk[$produk] += $penjualan_rp;
}

// DEBUG: total per bulan
// echo "<pre>PENJUALAN PER BULAN:\n";
// var_dump($bulan_terakhir);
// echo "</pre>";

// DEBUG: total item terjual per produk
// echo "<pre>ITEM TERJUAL PER PRODUK:\n";
// var_dump($produk_terjual);
// echo "</pre>";

// DEBUG: penghasilan per produk
// echo "<pre>PENGHASILAN PER PRODUK:\n";
// var_dump($penghasilan_per_produk);
// echo "</pre>";

// ==================== SORTIR DATA ====================

// Urutkan bulan terbaru → terlama, ambil 6 saja
krsort($bulan_terakhir); 
$bulan_terakhir = array_slice($bulan_terakhir, 0, 6, true);

// Urutkan produk dari yang paling banyak terjual
arsort($produk_terjual);

// Urutkan penghasilan per produk
arsort($penghasilan_per_produk);

// Ambil 1 produk terlaris tiap kategori
$hasil_terlaris_kategori = [];
foreach ($terlaris_kategori as $kategori => $produk_list) {
    arsort($produk_list); // Urutkan dari item terbanyak
    $hasil_terlaris_kategori[$kategori] = array_slice($produk_list, 0, 1, true);
}

// DEBUG: Produk terlaris per kategori (hasil akhir)
// echo "<pre>PRODUK TERLARIS PER KATEGORI:\n";
// var_dump($hasil_terlaris_kategori);
// echo "</pre>";
?>


<!DOCTYPE html>
<html>
<head>
    <title>PT. NIAGA MANDIRI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>PT. NIAGA MANDIRI</h3>
        <a href="index.php">DASHBOARD</a>
        <a href="data_penjualan.php">DATA PENJUALAN</a>
        <a href="data_produk.php">DATA PRODUK</a>
        <a href="data_kategori.php">DATA KATEGORI</a>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <h1>DASHBOARD</h1>

        <!-- Tabel penjualan 6 bulan terakhir -->
        <h3>Jumlah Penjualan per Bulan (6 Bulan Terakhir)</h3>
        <table>
            <tr><th>Bulan</th><th>Item Terjual</th></tr>
            <?php foreach ($bulan_terakhir as $bulan => $jumlah): ?>
                <tr>
                    <td><?= date('F Y', strtotime($bulan . '-01')) ?></td> <!-- Format jadi nama bulan -->
                    <td><?= $jumlah ?></td> <!-- Total item -->
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Tabel 10 produk terlaris -->
        <h3>10 Produk Terbanyak Terjual</h3>
        <table>
            <tr><th>Produk</th><th>Item Terjual</th></tr>
            <?php $i = 0; foreach ($produk_terjual as $produk => $jumlah): if (++$i > 10) break; ?>
                <tr><td><?= $produk ?></td><td><?= $jumlah ?></td></tr>
            <?php endforeach; ?>
        </table>

        <!-- Tabel produk terlaris per kategori -->
        <h3>Produk Terlaris per Kategori</h3>
        <table>
            <tr><th>Kategori</th><th>Produk Terlaris</th><th>Jumlah Terjual</th></tr>
            <?php foreach ($hasil_terlaris_kategori as $kategori => $produk): foreach ($produk as $nama => $jumlah): ?>
                <tr><td><?= $kategori ?></td><td><?= $nama ?></td><td><?= $jumlah ?></td></tr>
            <?php endforeach; endforeach; ?>
        </table>
    </div>
</body>
</html>
