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

        <div class="tab-kategori">
            <span class="tab aktif">Menu</span>
        </div>

        <section class="grid-menu" id="daftar-menu">
            <?php foreach ($daftarMenu as $menu): ?>
                <article class="kartu-menu">
                    <img src="<?= e(aset('images/' . $menu['gambar'])) ?>" alt="<?= e($menu['nama']) ?>" width="96" height="96">
                    <h3><?= e($menu['nama']) ?></h3>
                    <p class="deskripsi"><?= e($menu['deskripsi']) ?></p>
                    <p class="harga"><?= e(rupiah($menu['harga'])) ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <p class="pesan-kosong" id="pesan-kosong" hidden>Menu tidak ditemukan.</p>

<?php require __DIR__ . '/includes/footer.php'; ?>