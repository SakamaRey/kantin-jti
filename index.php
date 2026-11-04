<?php
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/includes/fungsi.php';

// Ambil semua menu yang tersedia dari database
$daftarMenu = $pdo->query('SELECT * FROM menu WHERE tersedia = TRUE ORDER BY id')->fetchAll();

$judul      = 'Menu';
$tampilCari = true;
$skrip      = ['cari.js', 'keranjang.js'];

require __DIR__ . '/includes/header.php';
?>

        <div class="tab-kategori">
            <span class="tab aktif">Menu</span>
        </div>

        <section class="grid-menu" id="daftar-menu">
            <?php foreach ($daftarMenu as $menu): ?>
                <article class="kartu-menu"
                         data-id="<?= (int) $menu['id'] ?>"
                         data-nama="<?= e($menu['nama']) ?>"
                         data-harga="<?= (int) $menu['harga'] ?>"
                         data-gambar="<?= e(aset('images/' . $menu['gambar'])) ?>">
                    <img src="<?= e(aset('images/' . $menu['gambar'])) ?>" alt="<?= e($menu['nama']) ?>" width="96" height="96">
                    <h3><?= e($menu['nama']) ?></h3>
                    <p class="deskripsi"><?= e($menu['deskripsi']) ?></p>
                    <?php ?>
                    <div class="kartu-bawah">
                        <button type="button" class="tombol-tambah" aria-label="Tambah <?= e($menu['nama']) ?> ke keranjang">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <p class="harga"><?= e(rupiah($menu['harga'])) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <p class="pesan-kosong" id="pesan-kosong" hidden>Menu tidak ditemukan.</p>

<?php require __DIR__ . '/includes/footer.php'; ?>