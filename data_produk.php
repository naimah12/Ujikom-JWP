<?php
include 'proses.php';

$csv_file = 'data/data_produk.csv';
$kategori_file = 'data/data_kategori.csv';
$data = [];
$kategori_list = [];

// Baca data produk
if (file_exists($csv_file)) {
    $file = fopen($csv_file, 'r');
    $header = fgetcsv($file); // Skip header
    while (($row = fgetcsv($file)) !== false) {
        $data[] = $row;
    }
    fclose($file);
}

// Baca data kategori
if (file_exists($kategori_file)) {
    $file = fopen($kategori_file, 'r');
    while (($row = fgetcsv($file)) !== false) {
        $kategori_list[] = $row[0]; // Ambil nama_kategori saja
    }
    fclose($file);
}

// Simpan ke CSV
function saveToCSV($data, $file_path) {
    $file = fopen($file_path, 'w');
    fputcsv($file, ['nama_produk', 'nama_kategori', 'harga']);
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
}

// Tambah
if (isset($_POST['tambah'])) {
    tambahProduk($data, $csv_file, $_POST);
}

// Edit
if (isset($_POST['simpan_edit'])) {
    editProduk($data, $csv_file, $_POST);
}

// Hapus
if (isset($_GET['hapus'])) {
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
    <link rel="stylesheet" href="style.css">
    <script>
        function showForm(mode, index = null) {
            document.getElementById('form-container').style.display = 'block';
            const form = document.forms['formProduk'];
            if (mode === 'tambah') {
                form.reset();
                form.index.value = '';
                document.getElementById('form-title').innerText = 'Tambah Data Produk';
                document.getElementById('simpan-tambah').style.display = 'inline-block';
                document.getElementById('simpan-edit').style.display = 'none';
            } else if (mode === 'edit') {
                const data = JSON.parse(document.getElementById('data-' + index).textContent);
                form.nama_produk.value = data[0];
                form.nama_kategori.value = data[1];
                form.harga.value = data[2];
                form.index.value = index;
                document.getElementById('form-title').innerText = 'Edit Data Produk';
                document.getElementById('simpan-tambah').style.display = 'none';
                document.getElementById('simpan-edit').style.display = 'inline-block';
            }
        }

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
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row[0]) ?></td>
                    <td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= htmlspecialchars($row[2]) ?></td>
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
