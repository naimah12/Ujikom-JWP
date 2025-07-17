<?php
include 'proses.php';

$csv_file = 'data/data_penjualan.csv';
$data_produk_file = 'data/data_produk.csv';
$data = [];

$search_nama = $_GET['search_nama'] ?? '';
$search_tanggal = $_GET['search_tanggal'] ?? '';

$produk_list = [];

$search_nama = $_GET['search_nama'] ?? '';
$search_tanggal = $_GET['search_tanggal'] ?? '';

$data = []; // inisialisasi

// Baca data penjualan dari CSV
if (file_exists($csv_file)) {
    $file = fopen($csv_file, 'r');
    $header = fgetcsv($file); // skip header
    while (($row = fgetcsv($file)) !== false) {
        $data[] = $row;
    }
    fclose($file);
}

// FILTER dilakukan data terisi
if ($search_nama || $search_tanggal) {
    $data = array_filter($data, function ($row) use ($search_nama, $search_tanggal) {
        $match_nama = $search_nama ? stripos($row[1], $search_nama) !== false : true;
        $match_tanggal = $search_tanggal ? $row[0] === $search_tanggal : true;
        return $match_nama && $match_tanggal;
    });
}

// Baca nama produk dari data_produk.csv
if (file_exists($data_produk_file)) {
    $file = fopen($data_produk_file, 'r');
    fgetcsv($file); // skip header
    while (($row = fgetcsv($file)) !== false) {
        $produk_list[] = $row[0]; // Ambil nama_produk
    }
    fclose($file);
}

// Simpan data ke CSV
function saveToCSV($data, $file_path) {
    $file = fopen($file_path, 'w');
    fputcsv($file, ['tanggal', 'nama_produk', 'item_terjual', 'total_penjualan']);
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
}

// Tambah
if (isset($_POST['tambah'])) {
    tambahPenjualan($data, $csv_file, $_POST);
}

// Edit
if (isset($_POST['simpan_edit'])) {
    editPenjualan($data, $csv_file, $_POST);
}

// Hapus
if (isset($_GET['hapus'])) {
    hapusPenjualan($data, $csv_file, $_GET['hapus']);
}

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
    <link rel="stylesheet" href="style.css">
    <script>
        function showForm(mode, index = null) {
            document.getElementById('form-container').style.display = 'block';
            const form = document.forms['formPenjualan'];
            if (mode === 'tambah') {
                form.reset();
                form.index.value = '';
                document.getElementById('form-title').innerText = 'Tambah Data Penjualan';
                document.getElementById('simpan-tambah').style.display = 'inline-block';
                document.getElementById('simpan-edit').style.display = 'none';
            } else if (mode === 'edit') {
                const data = JSON.parse(document.getElementById('data-' + index).textContent);
                form.tanggal.value = data[0];
                form.nama_produk.value = data[1];
                form.item_terjual.value = data[2];
                form.total_penjualan.value = data[3];
                form.index.value = index;
                document.getElementById('form-title').innerText = 'Edit Data Penjualan';
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
        <h1>DATA PENJUALAN</h1>

        <div style="margin-bottom: 15px;">
            <button onclick="showForm('tambah')">Tambah</button>
        </div>

        <form method="get" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <input type="text" name="search_nama" placeholder="Nama Produk" value="<?= htmlspecialchars($search_nama) ?>">
            <input type="date" name="search_tanggal" value="<?= htmlspecialchars($search_tanggal) ?>">
            <button type="submit">Cari</button>
            <a href="data_penjualan.php" style="margin-left: 5px;">Reset</a>
        </form>

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
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row[0]) ?></td>
                    <td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= htmlspecialchars($row[2]) ?></td>
                    <td><?= htmlspecialchars($row[3]) ?></td>
                    <td><?= klasifikasiPenjualan((int)$row[3]) ?></td>
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
