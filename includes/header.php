<?php
// Bagian atas halaman. Sebelum include, halaman bisa mengisi:
//   $judul      judul tab browser
//   $tampilCari true jika kolom pencarian ingin ditampilkan
//   $polos      true untuk halaman tanpa navigasi (login, daftar)
$judul      = $judul      ?? 'Kantin JTI';
$tampilCari = $tampilCari ?? false;
$polos      = $polos      ?? false;
$aktif      = $aktif      ?? 'home';
$users   = pengguna_saat_ini();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> | Kantin JTI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(aset('css/style.css')) ?>">
</head>
<body>

    <?php if (!$polos): ?>
    <header class="header">
        <div class="wadah header-isi">
            <a href="<?= e(url('index.php')) ?>" class="logo">Kantin JTI</a>

            <nav class="nav-atas" aria-label="Navigasi utama">
                <a href="<?= e(url('index.php')) ?>" class="<?= $aktif === 'home' ? 'aktif' : '' ?>">Home</a>
                <a href="<?= e(url('pelanggan/riwayat.php')) ?>" class="<?= $aktif === 'transaksi' ? 'aktif' : '' ?>">Transaksi</a>
                <a href="#" class="<?= $aktif === 'profil' ? 'aktif' : '' ?>">Profile</a>
            </nav>

            <?php if ($tampilCari): ?>
                <label class="pencarian">
                    <span aria-hidden="true">&#128269;</span>
                    <input type="search" id="cari" placeholder="Cari makanan" aria-label="Cari makanan">
                </label>
            <?php endif; ?>

            <?php if ($users): ?>
                <span class="nama-pengguna"><?= e($users['username']) ?></span>
                <a href="<?= e(url('auth/logout.php')) ?>" class="tombol-pil">Logout</a>
            <?php else: ?>
                <a href="<?= e(url('auth/login.php')) ?>" class="tombol-pil">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <?php endif; ?>

    <main class="wadah <?= $polos ? 'tengah' : '' ?>">