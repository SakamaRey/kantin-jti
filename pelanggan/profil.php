<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

// Data akun yang sedang login
$stmt = $pdo->prepare('SELECT username, email, create_account FROM users WHERE users_id = ?');
$stmt->execute([pengguna_saat_ini()['users_id']]);
$akun = $stmt->fetch();

// Akun sudah tidak ada di database: keluarkan
if (!$akun) {
    alihkan('auth/logout.php');
}

// Pesan sukses (mis. dari halaman ganti password), tampil sekali saja
$sukses = $_SESSION['pesan_sukses'] ?? '';
unset($_SESSION['pesan_sukses']);

$judul    = 'Profile';
$aktif    = 'profil';
$tanpaBar = true;
require __DIR__ . '/../includes/header.php';
?>

        <?php if ($sukses): ?>
            <div class="pesan-sukses" role="status"><?= e($sukses) ?></div>
        <?php endif; ?>

        <section class="kartu-profil">
            <div class="avatar-profil" aria-hidden="true"><?= e(strtoupper(mb_substr($akun['username'], 0, 1))) ?></div>
            <h1 class="nama-profil"><?= e($akun['username']) ?></h1>
            <p class="email-profil"><?= e($akun['email']) ?></p>
            <p class="gabung-profil">Bergabung sejak <?= e(tanggal_pendek($akun['create_account'])) ?></p>

            <div class="aksi-auth">
                <a href="<?= e(url('pelanggan/ganti-password.php')) ?>" class="tombol">Change Password</a>
                <a href="<?= e(url('auth/logout.php')) ?>" class="tombol tombol-sekunder">Sign Out</a>
            </div>
        </section>

<?php require __DIR__ . '/../includes/footer.php'; ?>