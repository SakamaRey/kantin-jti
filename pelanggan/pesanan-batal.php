<?php
// Pelanggan membatalkan pesanan yang masih menunggu konfirmasi.
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    alihkan('index.php');
}

$id = (int) ($_POST['id'] ?? 0);

// Hanya bisa dibatalkan jika milik sendiri DAN masih "menunggu".
// (Jika admin sempat menerima lebih dulu, tidak ada baris yang berubah.)
$stmt = $pdo->prepare(
    "UPDATE pesanan SET status = 'dibatalkan' WHERE id_pesanan = ? AND users_id = ? AND status = 'menunggu'"
);
$stmt->execute([$id, pengguna_saat_ini()['users_id']]);

// Halaman status menentukan tujuan: dibatalkan -> katalog, sudah diterima -> layar diterima
alihkan('pelanggan/pesanan-status.php?id=' . $id);