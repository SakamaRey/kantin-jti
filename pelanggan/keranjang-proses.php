<?php
// Menerima semua tombol dari katalog dan halaman keranjang, lalu kembali ke halaman asal.
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

// Hanya menerima kiriman form yang sah
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    alihkan('index.php');
}

// Setelah selesai, kembali ke katalog atau ke halaman keranjang
$kembali = ($_POST['kembali'] ?? '') === 'keranjang' ? 'pelanggan/keranjang.php' : 'index.php';

// 1. Simpan catatan dan jam ambil dari form keranjang
if (isset($_POST['catatan']) && is_array($_POST['catatan'])) {
    foreach ($_POST['catatan'] as $menuId => $teks) {
        if (isset($_SESSION['keranjang'][(int) $menuId])) {
            $_SESSION['keranjang'][(int) $menuId]['catatan'] = mb_substr(trim((string) $teks), 0, 200);
        }
    }
}
if (isset($_POST['jam_ambil']) && in_array($_POST['jam_ambil'], slot_jam(), true)) {
    $_SESSION['jam_ambil'] = $_POST['jam_ambil'];
}

// 2. Jalankan tombol yang ditekan. Nilainya berbentuk "perintah:id", contoh "tambah:3"
[$perintah, $menuId] = array_pad(explode(':', (string) ($_POST['aksi'] ?? '')), 2, '0');
$menuId = (int) $menuId;

switch ($perintah) {
    case 'tambah':
        // Hanya menu yang masih tersedia yang boleh masuk keranjang
        $stmt = $pdo->prepare('SELECT 1 FROM menu WHERE id = ? AND tersedia = TRUE');
        $stmt->execute([$menuId]);
        if ($stmt->fetchColumn()) {
            keranjang_ubah($menuId, +1);
        } else {
            $_SESSION['pesan_error'] = 'Menu tersebut sedang tidak tersedia.';
        }
        break;

    case 'kurang':
        keranjang_ubah($menuId, -1);
        break;

    case 'hapus':
        keranjang_hapus($menuId);
        break;

    case 'pesan':
        try {
            $pesanan = buat_pesanan($pdo, pengguna_saat_ini()['id']);
            alihkan('pelanggan/pesanan-status.php?id=' . $pesanan['id']);
        } catch (RuntimeException $e) {
            $_SESSION['pesan_error'] = $e->getMessage();
        }
        break;
}

alihkan($kembali);