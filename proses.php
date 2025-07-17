<?php

//KATEGORI
// Fungsi Tambah
function tambahKategori(&$data, $file_path, $nama_kategori) {
    $data[] = [$nama_kategori];
    saveToCSV($data, $file_path);
    header("Location: data_kategori.php");
    exit();
}

// Fungsi Edit
function editKategori(&$data, $file_path, $index, $nama_kategori) {
    $data[$index] = [$nama_kategori];
    saveToCSV($data, $file_path);
    header("Location: data_kategori.php");
    exit();
}

// Fungsi Hapus
function hapusKategori(&$data, $file_path, $index) {
    unset($data[$index]);
    $data = array_values($data); // Reset index array
    saveToCSV($data, $file_path);
    header("Location: data_kategori.php");
    exit();
}

//PRODUK
function tambahProduk(&$data, $file, $input) {
    $data[] = [
        $input['nama_produk'],
        $input['nama_kategori'],
        $input['harga']
    ];
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

function editProduk(&$data, $file, $input) {
    $index = $input['index'];
    $data[$index] = [
        $input['nama_produk'],
        $input['nama_kategori'],
        $input['harga']
    ];
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

function hapusProduk(&$data, $file, $index) {
    unset($data[$index]);
    $data = array_values($data); // reset index
    saveToCSV($data, $file);
    header("Location: data_produk.php");
    exit();
}

//PENJUALAN
function tambahPenjualan(&$data, $file, $input) {
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

function editPenjualan(&$data, $file, $input) {
    $index = $input['index'];
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

function hapusPenjualan(&$data, $file, $index) {
    unset($data[$index]);
    $data = array_values($data); // reset index
    saveToCSV($data, $file);
    header("Location: data_penjualan.php");
    exit();
}

