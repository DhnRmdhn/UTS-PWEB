<?php

include "koneksi.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?pesan=' . urlencode('Akses tidak valid.') . '&tipe=error');
    exit;
}


$tanggal     = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';
$jenis       = isset($_POST['jenis']) ? trim($_POST['jenis']) : '';
$kategori    = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
$keterangan  = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$nominal_raw = isset($_POST['nominal']) ? trim($_POST['nominal']) : '';

// Daftar kategori disesuaikan dengan jenis transaksi
$daftarKategori = array(
    'pemasukan' => array('Gaji', 'Uang Saku', 'Bonus', 'Lainnya'),
    'pengeluaran' => array('Makan', 'Transportasi', 'Top Up', 'Belanja', 'Tagihan', 'Lainnya')
);


if ($tanggal === '' || $jenis === '' || $kategori === '' || $nominal_raw === '') {
    header('Location: index.php?pesan=' . urlencode('Tanggal, jenis, kategori, dan nominal wajib diisi.') . '&tipe=error');
    exit;
}

// Jenis dan kategori harus cocok agar kategori tidak dikirim sembarangan
if (!isset($daftarKategori[$jenis]) || !in_array($kategori, $daftarKategori[$jenis])) {
    header('Location: index.php?pesan=' . urlencode('Kategori tidak sesuai dengan jenis transaksi.') . '&tipe=error');
    exit;
}

if (!is_numeric($nominal_raw)) {
    header('Location: index.php?pesan=' . urlencode('Nominal harus berupa angka.') . '&tipe=error');
    exit;
}

$nominal = (int) $nominal_raw;

if ($nominal <= 0) {
    header('Location: index.php?pesan=' . urlencode('Nominal harus lebih dari 0.') . '&tipe=error');
    exit;
}

$parts = explode('-', $tanggal);
if (count($parts) !== 3 || !checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
    header('Location: index.php?pesan=' . urlencode('Format tanggal tidak valid.') . '&tipe=error');
    exit;
}


if (strlen($keterangan) > 100) {
    header('Location: index.php?pesan=' . urlencode('Keterangan maksimal 100 karakter.') . '&tipe=error');
    exit;
}

$tanggal = $conn->real_escape_string($tanggal);
$jenis = $conn->real_escape_string($jenis);
$kategori = $conn->real_escape_string($kategori);
$keterangan = $conn->real_escape_string($keterangan);

$sql = "INSERT INTO keuangan (tanggal, jenis, kategori, keterangan, nominal) VALUES ('$tanggal', '$jenis', '$kategori', '$keterangan', $nominal)";

if ($conn->query($sql)) {
    header('Location: index.php?pesan=' . urlencode('Transaksi berhasil disimpan.') . '&tipe=sukses&halaman=overview');
    exit;
}

header('Location: index.php?pesan=' . urlencode('Gagal menyimpan transaksi. Coba lagi.') . '&tipe=error');
exit;
