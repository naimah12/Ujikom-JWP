<?php
include 'proses.php'; // Panggil file proses yang berisi fungsi CRUD

$csv_file = 'data/data_produk.csv';       // Lokasi file produk
$kategori_file = 'data/data_kategori.csv';// Lokasi file kategori
$data = [];                               // Inisialisasi array produk
$kategori_list = [];                      // Inisialisasi array kategori

// ======== BACA DATA PRODUK ========
if (file_exists($csv_file)) {
    $file = fopen($csv_file, 'r');      // Buka file produk
    $header = fgetcsv($file);           // Lewati baris header
    while (($row = fgetcsv($file)) !== false) {
        $data[] = $row;                 // Tambah baris ke array
    }
    fclose($file);
}

// DEBUG PRODUK
// echo "<pre>DATA PRODUK:\n";
// var_dump($data);
// echo "</pre>";

// ======== BACA DATA KATEGORI ========
if (file_exists($kategori_file)) {
    $file = fopen($kategori_file, 'r'); // Buka file kategori
    while (($row = fgetcsv($file)) !== false) {
        $kategori_list[] = $row[0];     // Ambil kolom pertama saja (nama_kategori)
    }
    fclose($file);
}

// DEBUG KATEGORI
// echo "<pre>DATA KATEGORI:\n";
// var_dump($kategori_list);
// echo "</pre>";

// ======== FUNGSI SIMPAN ULANG KE CSV ========
function saveToCSV($data, $file_path) {
    $file = fopen($file_path, 'w');                         // Buka untuk tulis ulang
    fputcsv($file, ['nama_produk', 'nama_kategori', 'harga']); // Header
    foreach ($data as $row) {
        fputcsv($file, $row);                               // Tulis baris data
    }
    fclose($file);
}

// ======== TAMBAH DATA PRODUK ========
if (isset($_POST['tambah'])) {
    // echo "<pre>POST TAMBAH:\n";
    // var_dump($_POST);
    // echo "</pre>";
    tambahProduk($data, $csv_file, $_POST);
}

// ======== EDIT DATA PRODUK ========
if (isset($_POST['simpan_edit'])) {
    // echo "<pre>POST EDIT:\n";
    // var_dump($_POST);
    // echo "</pre>";
    editProduk($data, $csv_file, $_POST);
}

// ======== HAPUS DATA PRODUK ========
if (isset($_GET['hapus'])) {
    // echo "<pre>INDEX HAPUS:\n";
    // var_dump($_GET['hapus']);
    // echo "</pre>";
    hapusProduk($data, $csv_file, $_GET['hapus']);
}
?>


<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>Data Produk</title>
    <link rel="stylesheet" href="style.css"><!-- Menghubungkan dengan file CSS -->
    <script>
        // Fungsi untuk menampilkan form (tambah/edit)
        function showForm(mode, index = null) {
            document.getElementById('form-container').style.display = 'block';// Tampilkan form
            const form = document.forms['formProduk']; // Ambil form berdasarkan atribut name
            
            if (mode === 'tambah') {
                form.reset(); // Kosongkan form
                form.index.value = '';// Kosongkan input hidden index
                document.getElementById('form-title').innerText = 'Tambah Data Produk';// Ganti judul form
                document.getElementById('simpan-tambah').style.display = 'inline-block';// Tampilkan tombol Tambah
                document.getElementById('simpan-edit').style.display = 'none';// Sembunyikan tombol Simpan

            } else if (mode === 'edit') {
                const data = JSON.parse(document.getElementById('data-' + index).textContent); // Ambil data JSON dari baris
                // Isi form dengan data kategori yang akan diedit
                form.nama_produk.value = data[0]; //nama produk
                form.nama_kategori.value = data[1]; //nama kategori
                form.harga.value = data[2]; //harga
                form.index.value = index;// Isi input hidden index
                document.getElementById('form-title').innerText = 'Edit Data Produk';// Ganti judul form
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
        <h1>DATA PRODUK</h1>

        <div class="header-tools">
            <div class="left">
                <button onclick="showForm('tambah')">Tambah</button>
            </div>
            <div class="right">
                <input type="text" placeholder="Cari">
            </div>
        </div>

        <!-- Form Tambah/Edit -->
        <div id="form-container" style="display:none; margin-bottom:20px;">
            <h3 id="form-title">Tambah Data Produk</h3>
            <form name="formProduk" method="post">
                <input type="hidden" name="index">
                <input type="text" name="nama_produk" placeholder="Nama Produk" required>
                <select name="nama_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?= htmlspecialchars($kategori) ?>"><?= htmlspecialchars($kategori) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="harga" placeholder="Harga" required>
                <button type="submit" name="tambah" id="simpan-tambah">Tambah</button>
                <button type="submit" name="simpan_edit" id="simpan-edit" style="display:none;">Simpan</button>
                <button type="button" onclick="hideForm()">Batal</button>
            </form>
        </div>

        <!-- Tabel Data -->
        <table class="tabel-data">
            <tr>
                <th>NO</th>
                <th>NAMA PRODUK</th>
                <th>NAMA KATEGORI</th>
                <th>HARGA</th>
                <th>AKSI</th>
            </tr>
            <?php foreach ($data as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td> <!-- No urut -->
                    <td><?= htmlspecialchars($row[0]) ?></td> <!-- Nama produk -->
                    <td><?= htmlspecialchars($row[1]) ?></td> <!-- Kategori -->
                    <td><?= htmlspecialchars($row[2]) ?></td> <!-- Harga -->
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
