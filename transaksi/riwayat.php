<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login(); // Hanya Pengguna yang sudah login yang bisa akses

//Ambil semua pesanan milik pengguna ini, terbaru di atas
$stmt = $pdo->prepare(
    'SELECT p.id, p.total, p.dibuat_pada,
            string_agg(m.nama, \', \' ORDER BY ip.id) AS nama_paket
       FROM pesanan p
       JOIN item_pesanan ip ON ip.pesanan_id = p.id
       JOIN menu m          ON m.id = ip.menu_id
      WHERE p.pengguna_id = ?
      GROUP BY p.id
      ORDER BY p.dibuat_pada DESC, p.id DESC'
);
$stmt->execute([pengguna_saat_ini()['id']]);
$daftarTransaksi = $stmt->fetchAll();

$judul           = 'Transaksi';
$tampilCari      = true;
$menuAktif       = 'transaksi';
$tampilKeranjang = false;
$skrip           = ['cariRiwayat.js'];
 
require __DIR__ . '/../includes/header.php';
?>
 
        <?php if (!$daftarTransaksi): ?>
            <p class="pesan-kosong">Belum ada transaksi.</p>
        <?php endif; ?>
 
        <section class="daftar-transaksi" id="daftar-transaksi">
            <?php foreach ($daftarTransaksi as $transaksi): ?>
                <article class="kartu-transaksi" data-nama="<?= e(strtolower($transaksi['nama_paket'])) ?>">
                    <div>
                        <h3><?= e($transaksi['nama_paket']) ?></h3>
                        <p class="tanggal"><?= e(date('j M Y', strtotime($transaksi['dibuat_pada']))) ?></p>
                    </div>
                    <p class="total">Rp. <?= e(number_format((int) $transaksi['total'], 0, ',', '.')) ?></p>
                </article>
            <?php endforeach; ?>
        </section>
 
        <p class="pesan-kosong" id="pesan-kosong" hidden>Transaksi tidak ditemukan.</p>
 
<?php require __DIR__ . '/../includes/footer.php'; ?>
