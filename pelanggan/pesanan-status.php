<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

// Ambil ID pesanan dari URL secara aman (?id= ATAU ?id_pesanan=)
$idPesanan = (int) ($_GET['id'] ?? $_GET['id_pesanan'] ?? 0);

// PERBAIKAN: Mengubah id & pengguna_id menjadi id_pesanan & users_id
$stmt = $pdo->prepare('SELECT * FROM pesanan WHERE id_pesanan = ? AND users_id = ?');
$stmt->execute([$idPesanan, pengguna_saat_ini()['users_id']]);
$pesanan = $stmt->fetch();

// Pesanan tidak ada atau sudah dibatalkan: kembali ke katalog
if (!$pesanan || $pesanan['status'] === 'dibatalkan') {
    alihkan('index.php');
}

$status = $pesanan['status']; // menunggu | diterima | ditolak | dibatalkan | selesai

$judul = 'Status Pesanan';
$polos = true;

// JavaScript pemeriksa status hanya perlu saat masih menunggu
if ($status === 'menunggu') {
    $skrip = ['status-pesanan.js'];
}

require __DIR__ . '/../includes/header.php';
?>

        <div class="layar-status" id="status-pesanan"
             data-status="<?= e($status) ?>"
             data-cek="<?= e(url('pelanggan/pesanan-cek.php?id=' . (int) $pesanan['id_pesanan'])) ?>">

            <?php if ($status === 'menunggu'): ?>

                <div class="putaran" role="status" aria-label="Menunggu"></div>

                <div class="kartu-status">
                    <h1>Pesanan Sedang Diproses</h1>
                    <p class="redup">Menunggu Konfirmasi Admin</p>
                </div>

                <form method="post" action="<?= e(url('pelanggan/pesanan-batal.php')) ?>" class="tombol-status-bungkus">
                    <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $pesanan['id_pesanan'] ?>">
                    <button type="submit" class="tombol-status">Batal</button>
                </form>

            <?php elseif ($status === 'diterima'): ?>

                <div class="ikon-status">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"/></svg>
                </div>

                <div class="kartu-status">
                    <p class="kode"><?= e($pesanan['kode']) ?></p>
                    <p>Lakukan Pembayaran Di Kantin</p>
                </div>

                <div class="tombol-status-bungkus">
                    <a href="<?= e(url('index.php')) ?>" class="tombol-status">Pembayaran Selesai</a>
                </div>

            <?php else: ?>

                <div class="ikon-status">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
                </div>

                <div class="kartu-status">
                    <h1 class="kode">Booking di Tolak</h1>
                    <p>Maaf Menunya Baru Saja Habis</p>
                </div>

                <div class="tombol-status-bungkus">
                    <a href="<?= e(url('index.php')) ?>" class="tombol-status">Kembali ke Dashboard</a>
                </div>

            <?php endif; ?>

        </div>

<?php require __DIR__ . '/../includes/footer.php'; ?>