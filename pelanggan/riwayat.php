<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

// Riwayat = menu yang sudah dibeli pada pesanan yang diterima admin, terbaru di atas.
$stmt = $pdo->prepare(
    "SELECT m.nama_menu AS nama, p.create_pesanan AS dibuat_pada, i.jumlah * i.harga AS nominal
     FROM item_pesanan i
     JOIN pesanan p ON p.id_pesanan = i.id_pesanan
     JOIN menu m    ON m.id_menu = i.id_menu
     WHERE p.users_id = ? AND p.status = 'diterima'
     ORDER BY p.create_pesanan DESC, i.id_item"
);
$stmt->execute([pengguna_saat_ini()['users_id']]);
$daftarRiwayat = $stmt->fetchAll();

$judul      = 'Riwayat Transaksi';
$aktif      = 'transaksi';
$tampilCari = true;
$tanpaBar   = true; // desain riwayat tidak memakai bar keranjang
$skrip      = ['cari.js'];
require __DIR__ . '/../includes/header.php';
?>

        <a href="<?= e(url('index.php')) ?>" class="tombol-kembali" aria-label="Kembali ke menu">&larr;</a>

        <?php if (!$daftarRiwayat): ?>

            <p class="riwayat-kosong">Belum ada transaksi yang diterima.</p>

        <?php else: ?>

            <ul class="daftar-riwayat">
                <?php foreach ($daftarRiwayat as $baris): ?>
                    <li class="baris-riwayat" data-cari>
                        <div>
                            <h3><?= e($baris['nama']) ?></h3>
                            <p class="tanggal"><?= e(tanggal_pendek($baris['dibuat_pada'])) ?></p>
                        </div>
                        <p class="nominal"><?= e(rupiah_titik($baris['nominal'])) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="pesan-kosong" id="pesan-kosong" hidden>Transaksi tidak ditemukan.</p>

        <?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>