<?php
// Kredensial Database
$host = "localhost";
$user = "root";       // Ganti dengan username Anda
$password = "";       // Ganti dengan password Anda
$database = "warung_banjar"; // Ganti dengan nama database Anda

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Atur charset
$koneksi->set_charset("utf8");

// Fungsi untuk membuat format harga (Rp. 15.000)
function formatRupiah($angka){
    $hasil_rupiah = "Rp. " . number_format($angka,0,',','.');
    return $hasil_rupiah;
}
?>