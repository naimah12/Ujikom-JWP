<?php

include 'proses.php';

// Lokasi file CSV 
$csv_file = 'data/data_kategori.csv';
$data = []; // Inisialisasi array untuk menampung data dari CSV

    // Membaca file CSV jika tersedia
    if (file_exists($csv_file)) {
        $file = fopen($csv_file, 'r');           // Buka file dalam mode baca
        $header = fgetcsv($file);                // Lewati baris header
        while (($row = fgetcsv($file)) !== false) {
            $data[] = $row;                      // Tambahkan setiap baris ke array data
        }
        fclose($file);                           // Tutup file setelah selesai
    }

// Fungsi untuk menyimpan array data kembali ke file CSV
function saveToCSV($data, $file_path) {
    $file = fopen($file_path, 'w');          // Buka file dalam mode tulis (overwrite)
    fputcsv($file, ['nama_kategori']);       // Tulis baris header
    foreach ($data as $row) {
        fputcsv($file, $row);                // Tulis tiap baris data
    }
    fclose($file);                           // Tutup file setelah selesai menulis
}

    // Menangani aksi tambah data
if (isset($_POST['tambah'])) {
    tambahKategori($data, $csv_file, $_POST['nama_kategori']);
}

// Menangani aksi edit data
if (isset($_POST['simpan_edit'])) {
    editKategori($data, $csv_file, $_POST['index'], $_POST['nama_kategori']);
}

// Menangani aksi hapus data
if (isset($_GET['hapus'])) {
    hapusKategori($data, $csv_file, $_GET['hapus']);
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
    <link rel="stylesheet" href="style.css"> <!-- Menghubungkan dengan file CSS -->
    <script>
        // Fungsi untuk menampilkan form (tambah/edit)
        function showForm(mode, index = null) {
            document.getElementById('form-container').style.display = 'block'; // Tampilkan form
            const form = document.forms['formKategori']; // Ambil referensi form berdasarkan name
            if (mode === 'tambah') {
                form.reset(); // Reset seluruh input form
                form.index.value = ''; // Kosongkan input hidden index
                document.getElementById('form-title').innerText = 'Tambah Data Kategori'; // Ganti judul form
                document.getElementById('simpan-tambah').style.display = 'inline-block'; // Tampilkan tombol Tambah
                document.getElementById('simpan-edit').style.display = 'none'; // Sembunyikan tombol Simpan
            } else if (mode === 'edit') {
                // Ambil data JSON dari elemen script yang sesuai index
                const data = JSON.parse(document.getElementById('data-' + index).textContent);
                form.nama_kategori.value = data[0]; // Isi form dengan data kategori yang akan diedit
                form.index.value = index; // Isi input hidden index
                document.getElementById('form-title').innerText = 'Edit Data Kategori'; // Ganti judul form
                document.getElementById('simpan-tambah').style.display = 'none'; // Sembunyikan tombol Tambah
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
    <h1>DATA KATEGORI</h1> 

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
            <h3 id="form-title">Tambah Data Kategori</h3>
            <form name="formKategori" method="post">
                <input type="hidden" name="index">
                <input type="text" name="nama_kategori" placeholder="Nama Kategori" required>
                <button type="submit" name="tambah" id="simpan-tambah">Tambah</button>
                <button type="submit" name="simpan_edit" id="simpan-edit" style="display:none;">Simpan</button>
                <button type="button" onclick="hideForm()">Batal</button>
            </form>
        </div>

    <!-- Tabel -->
    <table class="tabel-data">
        <tr>
            <th>NO</th>
            <th>NAMA KATEGORI</th>
            <th>AKSI</th>
        </tr>
        <?php foreach ($data as $i => $row): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row[0]) ?></td>
                <td>
                    <div class="btn-group" role="group" aria-label="Aksi">
                        <button type="button" class="btn btn-sm btn-warning" onclick="showForm('edit', <?= $i ?>)">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </button>
                        <a href="?hapus=<?= $i ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </a>
                    </div>
                </td>
            </tr>
            <script>
                document.write(`<script id="data-<?= $i ?>" type="application/json"><?= json_encode($row) ?><\/script>`);
            </script>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
