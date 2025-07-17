<?php
// Inisialisasi file data CSV
$data_penjualan_file = 'data/data_penjualan.csv';
$data_produk_file = 'data/data_produk.csv';

//Inisialisasi array untuk menyimpan data dari CSV
$penjualan = [];           // Data transaksi penjualan
$produk_kategori = [];     // Mapping: nama produk → kategori

//Baca data penjualan
if (file_exists($data_penjualan_file)) {
    $file = fopen($data_penjualan_file, 'r');
    fgetcsv($file); // Lewati baris header
    while (($row = fgetcsv($file)) !== false) {
        $penjualan[] = $row; // Simpan setiap baris ke array
    }
    fclose($file);
}

//Baca data produk dan kategorinya
if (file_exists($data_produk_file)) {
    $file = fopen($data_produk_file, 'r');
    fgetcsv($file); // Lewati header
    while (($row = fgetcsv($file)) !== false) {
        $produk_kategori[$row[0]] = $row[1]; // nama_produk => kategori
    }
    fclose($file);
}

//Data ringkasan
$bulan_terakhir = [];            // Total item terjual per bulan
$produk_terjual = [];           // Total item terjual per produk
$terlaris_kategori = [];        // Produk terlaris di tiap kategori
$total_penghasilan = 0;         // Jumlah total semua transaksi
$penghasilan_per_produk = [];   // Total penghasilan per produk

// Loop seluruh data penjualan
foreach ($penjualan as $p) {
    $tanggal = $p[0];                    // Kolom 1: tanggal transaksi
    $produk = $p[1];                     // Kolom 2: nama produk
    $item = (int)$p[2];                  // Kolom 3: item terjual
    $penjualan_rp = (int)$p[3];         // Kolom 4: total rupiah penjualan

    //klasifikasi berdasarkan bulan (format: YYYY-MM)
    $bulan = date('Y-m', strtotime($tanggal));
    if (!isset($bulan_terakhir[$bulan])) $bulan_terakhir[$bulan] = 0;
    $bulan_terakhir[$bulan] += $item;

    // Akumulasi total item per produk
    if (!isset($produk_terjual[$produk])) $produk_terjual[$produk] = 0;
    $produk_terjual[$produk] += $item;

    // Klasifikasi berdasarkan kategori produk
    $kategori = $produk_kategori[$produk] ?? 'Tidak Diketahui';
    if (!isset($terlaris_kategori[$kategori][$produk])) {
        $terlaris_kategori[$kategori][$produk] = $item;
    } else {
        $terlaris_kategori[$kategori][$produk] += $item;
    }

    //Total keseluruhan penghasilan
    $total_penghasilan += $penjualan_rp;

    //Total penghasilan per produk
    if (!isset($penghasilan_per_produk[$produk])) $penghasilan_per_produk[$produk] = 0;
    $penghasilan_per_produk[$produk] += $penjualan_rp;
}

// Urutkan bulan dari terbaru ke terlama dan ambil 6 bulan terakhir
krsort($bulan_terakhir);
$bulan_terakhir = array_slice($bulan_terakhir, 0, 6, true);

// Urutkan produk terjual terbanyak
arsort($produk_terjual);

// Urutkan penghasilan produk dari yang tertinggi
arsort($penghasilan_per_produk);

// Ambil hanya 1 produk terlaris per kategori
$hasil_terlaris_kategori = [];
foreach ($terlaris_kategori as $kategori => $produk_list) {
    arsort($produk_list);
    $hasil_terlaris_kategori[$kategori] = array_slice($produk_list, 0, 1, true);
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>PT. NIAGA MANDIRI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h3>PT. NIAGA MANDIRI</h3>
        <a href="index.php">DASHBOARD</a>
        <a href="data_penjualan.php">DATA PENJUALAN</a>
        <a href="data_produk.php">DATA PRODUK</a>
        <a href="data_kategori.php">DATA KATEGORI</a>
    </div>
     <div class="content">
        <h1>DASHBOARD</h1>

        <h3>Jumlah Penjualan per Bulan (6 Bulan Terakhir)</h3>
        <table>
            <tr><th>Bulan</th><th>Item Terjual</th></tr>
            <?php foreach ($bulan_terakhir as $bulan => $jumlah): ?>
                <tr><td><?= date('F Y', strtotime($bulan . '-01')) ?></td><td><?= $jumlah ?></td></tr>
            <?php endforeach; ?>
        </table>

        <h3>10 Produk Terbanyak Terjual</h3>
        <table>
            <tr><th>Produk</th><th>Item Terjual</th></tr>
            <?php $i = 0; foreach ($produk_terjual as $produk => $jumlah): if (++$i > 10) break; ?>
                <tr><td><?= $produk ?></td><td><?= $jumlah ?></td></tr>
            <?php endforeach; ?>
        </table>

        <h3>Produk Terlaris per Kategori</h3>
        <table>
            <tr><th>Kategori</th><th>Produk Terlaris</th><th>Jumlah Terjual</th></tr>
            <?php foreach ($terlaris_kategori as $kategori => $produk): foreach ($produk as $nama => $jumlah): ?>
                <tr><td><?= $kategori ?></td><td><?= $nama ?></td><td><?= $jumlah ?></td></tr>
            <?php endforeach; endforeach; ?>
        </table>
    </div>
</body>
</html>