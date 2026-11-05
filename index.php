<?php
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/includes/fungsi.php';

// Ambil semua menu yang tersedia dari database
$daftarMenu = $pdo->query('SELECT * FROM menu WHERE tersedia = TRUE ORDER BY id')->fetchAll();

$judul      = 'Menu';
$tampilCari = true;
$skrip      = ['cari.js'];

require __DIR__ . '/includes/header.php';
?>

        <?php if (!empty($_SESSION['pesan_error'])): ?>
            <div class="pesan-error" role="alert"><?= e($_SESSION['pesan_error']) ?></div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <div class="tab-kategori">
            <span class="tab aktif">Menu</span>
        </div>

        <section class="grid-menu" id="daftar-menu">
            <?php foreach ($daftarMenu as $menu): ?>
                <article class="kartu-menu" data-cari>
                    <img src="<?= e(aset('images/' . $menu['gambar'])) ?>" alt="<?= e($menu['nama']) ?>" width="96" height="96">
                    <h3><?= e($menu['nama']) ?></h3>
                    <p class="deskripsi"><?= e($menu['deskripsi']) ?></p>
                    <div class="baris-harga">
                        <p class="harga"><?= e(rupiah($menu['harga'])) ?></p>

                        <!-- Tombol tambah: dikirim ke pelanggan/keranjang-proses.php -->
                        <form method="post" action="<?= e(url('pelanggan/keranjang-proses.php')) ?>">
                            <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                            <input type="hidden" name="aksi" value="tambah:<?= (int) $menu['id'] ?>">
                            <button type="submit" class="tombol-tambah" aria-label="Tambah <?= e($menu['nama']) ?> ke keranjang">+</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <p class="pesan-kosong" id="pesan-kosong" hidden>Menu tidak ditemukan.</p>

<?php require __DIR__ . '/includes/footer.php'; ?>