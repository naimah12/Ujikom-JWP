<?php
include 'proses.php'; // Include fungsi CRUD dari file proses.php

$csv_file = 'data/data_penjualan.csv';        // File data penjualan
$data_produk_file = 'data/data_produk.csv';   // File data produk
$data = []; // Inisialisasi array data penjualan

// Ambil parameter pencarian (jika ada) dari URL
$search_nama = $_GET['search_nama'] ?? '';
$search_tanggal = $_GET['search_tanggal'] ?? '';

// Inisialisasi array produk
$produk_list = [];

// DEBUG: tampilkan parameter pencarian
// echo "<pre>DEBUG GET:\n";
// var_dump($_GET);
// echo "</pre>";

// ==================== BACA DATA PENJUALAN ====================
if (file_exists($csv_file)) {
    $file = fopen($csv_file, 'r');
    $header = fgetcsv($file); // Lewati baris header
    while (($row = fgetcsv($file)) !== false) {
        $data[] = $row;
    }
    fclose($file);
}

// DEBUG: tampilkan data awal sebelum filter
// echo "<pre>DATA AWAL PENJUALAN:\n";
// var_dump($data);
// echo "</pre>";

// ==================== FILTER DATA ====================
if ($search_nama || $search_tanggal) {
    $data = array_filter($data, function ($row) use ($search_nama, $search_tanggal) {
        $match_nama = $search_nama ? stripos($row[1], $search_nama) !== false : true; // cocok nama
        $match_tanggal = $search_tanggal ? $row[0] === $search_tanggal : true;        // cocok tanggal
        return $match_nama && $match_tanggal;
    });
}

// DEBUG: tampilkan data setelah filter
// echo "<pre>DATA SETELAH FILTER:\n";
// var_dump($data);
// echo "</pre>";

// ==================== BACA LIST PRODUK ====================
if (file_exists($data_produk_file)) {
    $file = fopen($data_produk_file, 'r');
    fgetcsv($file); // Lewati header
    while (($row = fgetcsv($file)) !== false) {
        $produk_list[] = $row[0]; // Ambil hanya nama_produk
    }
    fclose($file);
}

// DEBUG: tampilkan daftar produk
// echo "<pre>LIST PRODUK:\n";
// var_dump($produk_list);
// echo "</pre>";

// Fungsi untuk menyimpan array data kembali ke file CSV
function saveToCSV($data, $file_path) {
    $file = fopen($file_path, 'w');// Buka file dalam mode tulis (overwrite)
    fputcsv($file, ['tanggal', 'nama_produk', 'item_terjual', 'total_penjualan']); // Tulis baris header
    foreach ($data as $row) {
        fputcsv($file, $row);  // Tulis tiap baris data
    }
    fclose($file);// Tutup file setelah selesai menulis
}

// ==================== HANDLE ACTION ====================
if (isset($_POST['tambah'])) {
    // echo "<pre>POST TAMBAH:\n";
    // var_dump($_POST);
    // echo "</pre>";
    tambahPenjualan($data, $csv_file, $_POST);
}

if (isset($_POST['simpan_edit'])) {
    // echo "<pre>POST EDIT:\n";
    // var_dump($_POST);
    // echo "</pre>";
    editPenjualan($data, $csv_file, $_POST);
}

if (isset($_GET['hapus'])) {
    // echo "<pre>HAPUS INDEX:\n";
    // var_dump($_GET['hapus']);
    // echo "</pre>";
    hapusPenjualan($data, $csv_file, $_GET['hapus']);
}

// ==================== FUNSI KLASIFIKASI ====================
function klasifikasiPenjualan($nilai) {
    if ($nilai > 100000000) return 'Sangat Tinggi';
    elseif ($nilai > 50000000) return 'Sedang';
    elseif ($nilai > 20000000) return 'Cukup';
    elseif ($nilai > 10000000) return 'Rendah';
    else return 'Sangat Rendah';
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
<title>Data Penjualan</title>
    <link rel="stylesheet" href="style.css"><!-- Menghubungkan dengan file CSS -->
    <script>
        // Fungsi untuk menampilkan form (tambah/edit)
        function showForm(mode, index = null) {
            document.getElementById('form-container').style.display = 'block';// Tampilkan form
            const form = document.forms['formPenjualan']; // Ambil form berdasarkan atribut name

            if (mode === 'tambah') {
                form.reset(); // Kosongkan form
                form.index.value = '';// Kosongkan input hidden index
                document.getElementById('form-title').innerText = 'Tambah Data Penjualan';// Ganti judul form
                document.getElementById('simpan-tambah').style.display = 'inline-block';// Tampilkan tombol Tambah
                document.getElementById('simpan-edit').style.display = 'none';// Sembunyikan tombol Simpan

            } else if (mode === 'edit') {
                const data = JSON.parse(document.getElementById('data-' + index).textContent); // Ambil data JSON dari baris
                // Isi form dengan data kategori yang akan diedit
                form.tanggal.value = data[0]; //tanggal
                form.nama_produk.value = data[1]; //nama produk
                form.item_terjual.value = data[2]; //item terjual
                form.total_penjualan.value = data[3]; //total penjualan
                form.index.value = index; // Isi input hidden index
                document.getElementById('form-title').innerText = 'Edit Data Penjualan';// Ganti judul form
                document.getElementById('simpan-tambah').style.display = 'none';// Sembunyikan tombol Tambah
                document.getElementById('simpan-edit').style.display = 'inline-block'; // Tampilkan tombol Simpan
            }
        }
        // Fungsi untuk menyembunyikan form
        function hideForm() {
            document.getElementById('form-container').style.display = 'none';
        }
    </script>
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
        <h1>DATA PENJUALAN</h1>

        <div style="margin-bottom: 15px;">
            <button onclick="showForm('tambah')">Tambah</button>
        </div>

        <!-- Form Filter -->
        <form method="get" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <input type="text" name="search_nama" placeholder="Nama Produk" value="<?= htmlspecialchars($search_nama) ?>">
            <input type="date" name="search_tanggal" value="<?= htmlspecialchars($search_tanggal) ?>">
            <button type="submit">Cari</button>
            <a href="data_penjualan.php" style="margin-left: 5px;">Reset</a>
        </form>

        <!-- Form Tambah/Edit -->
        <div id="form-container" style="display:none; margin-bottom:20px;">
            <h3 id="form-title">Tambah Data Penjualan</h3>
            <form name="formPenjualan" method="post">
                <input type="hidden" name="index">
                <input type="date" name="tanggal" required>
                <select name="nama_produk" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($produk_list as $produk): ?>
                        <option value="<?= htmlspecialchars($produk) ?>"><?= htmlspecialchars($produk) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="item_terjual" placeholder="Item Terjual" required>
                <input type="number" name="total_penjualan" placeholder="Total Penjualan" required>
                <button type="submit" name="tambah" id="simpan-tambah">Tambah</button>
                <button type="submit" name="simpan_edit" id="simpan-edit" style="display:none;">Simpan</button>
                <button type="button" onclick="hideForm()">Batal</button>
            </form>
        </div>

        <!-- Tabel -->
        <table class="tabel-data">
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>NAMA PRODUK</th>
                <th>ITEM</th>
                <th>TOTAL</th>
                <th>KETERANGAN</th>
                <th>AKSI</th>
            </tr>
            <?php foreach ($data as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td> <!-- Nomor urut -->
                    <td><?= htmlspecialchars($row[0]) ?></td> <!-- Tanggal -->
                    <td><?= htmlspecialchars($row[1]) ?></td> <!-- Nama produk -->
                    <td><?= htmlspecialchars($row[2]) ?></td> <!-- Item terjual -->
                    <td><?= htmlspecialchars($row[3]) ?></td> <!-- Total penjualan -->
                    <td><?= klasifikasiPenjualan((int)$row[3]) ?></td> <!-- Keterangan -->
                <td>
                    <div class="btn-group" role="group" aria-label="Aksi">
                        <!-- Button Edit-->
                        <button type="button" class="btn btn-sm btn-warning" onclick="showForm('edit', <?= $i ?>)">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </button>
                         <!-- Button Hapus-->
                        <a href="?hapus=<?= $i ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </a>
                    </div>
                </td>

                </tr>
                <!-- Simpan data per baris sebagai script JSON agar bisa dipanggil JS -->
                <script>
                    document.write(`<script id="data-<?= $i ?>" type="application/json"><?= json_encode($row) ?><\/script>`);
                </script>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
