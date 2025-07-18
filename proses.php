<?php

// ==================== KATEGORI ====================

// Fungsi Tambah Kategori
function tambahKategori(&$data, $file_path, $nama_kategori) {
    // echo "<pre>TAMBAH KATEGORI:\n";
    // var_dump($nama_kategori);
    // echo "</pre>";

    $data[] = [$nama_kategori]; // Tambahkan array 1 kolom (hanya nama kategori)
    saveToCSV($data, $file_path); // Simpan ulang ke file CSV
    header("Location: data_kategori.php"); // Redirect ke halaman utama
    exit();
}

// Fungsi Edit Kategori
function editKategori(&$data, $file_path, $index, $nama_kategori) {
    // echo "<pre>EDIT KATEGORI (index $index):\n";
    // var_dump($nama_kategori);
    // echo "</pre>";

    $data[$index] = [$nama_kategori]; // Ganti nama kategori di indeks tertentu
    saveToCSV($data, $file_path);
    header("Location: data_kategori.php");
    exit();
}

// Fungsi Hapus Kategori
function hapusKategori(&$data, $file_path, $index) {
    // echo "<pre>HAPUS KATEGORI (index $index):\n";
    // var_dump($data[$index]);
    // echo "</pre>";

    unset($data[$index]); // Hapus berdasarkan index
    $data = array_values($data); // Reset ulang index agar rapih
    saveToCSV($data, $file_path);
    header("Location: data_kategori.php");
    exit();
}

// ==================== PRODUK ====================

// Fungsi Tambah Produk
function tambahProduk(&$data, $file, $input) {
    // echo "<pre>TAMBAH PRODUK:\n";
    // var_dump($input);
    // echo "</pre>";

    $data[] = [
        $input['nama_produk'],
        $input['nama_kategori'],
        $input['harga']
    ];
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

// Fungsi Edit Produk
function editProduk(&$data, $file, $input) {
    $index = $input['index'];

    // echo "<pre>EDIT PRODUK (index $index):\n";
    // var_dump($input);
    // echo "</pre>";

    $data[$index] = [
        $input['nama_produk'],
        $input['nama_kategori'],
        $input['harga']
    ];
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

// Fungsi Hapus Produk
function hapusProduk(&$data, $file, $index) {
    // echo "<pre>HAPUS PRODUK (index $index):\n";
    // var_dump($data[$index]);
    // echo "</pre>";

    unset($data[$index]);
    $data = array_values($data); // Reset ulang index
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

// ==================== PENJUALAN ====================

// Fungsi Tambah Penjualan
function tambahPenjualan(&$data, $file, $input) {
    // echo "<pre>TAMBAH PENJUALAN:\n";
    // var_dump($input);
    // echo "</pre>";

    $data[] = [
        $input['tanggal'],
        $input['nama_produk'],
        $input['item_terjual'],
        $input['total_penjualan']
    ];
    saveToCSV($data, $file);
    header("Location: data_penjualan.php");
    exit();
}

// Fungsi Edit Penjualan
function editPenjualan(&$data, $file, $input) {
    $index = $input['index'];

    // echo "<pre>EDIT PENJUALAN (index $index):\n";
    // var_dump($input);
    // echo "</pre>";

    $data[$index] = [
        $input['tanggal'],
        $input['nama_produk'],
        $input['item_terjual'],
        $input['total_penjualan']
    ];
    saveToCSV($data, $file);
    header("Location: data_penjualan.php");
    exit();
}

// Fungsi Hapus Penjualan
function hapusPenjualan(&$data, $file, $index) {
    // echo "<pre>HAPUS PENJUALAN (index $index):\n";
    // var_dump($data[$index]);
    // echo "</pre>";

    unset($data[$index]);
    $data = array_values($data); // Reset ulang index
    saveToCSV($data, $file);
    header("Location: data_penjualan.php");
    exit();
}
