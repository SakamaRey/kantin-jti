<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lama  = $_POST['password_lama'] ?? '';
    $baru  = $_POST['password_baru'] ?? '';
    $ulang = $_POST['ulangi_password'] ?? '';
    $id    = pengguna_saat_ini()['users_id'];

    if (!csrf_valid()) {
        $errors[] = 'Sesi form tidak valid, muat ulang halaman.';
    }

    // Password lama harus cocok dengan yang tersimpan
    $stmt = $pdo->prepare('SELECT password FROM users WHERE users_id = ?');
    $stmt->execute([$id]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($lama, $hash)) {
        $errors[] = 'Password lama salah.';
    }
    if (strlen($baru) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }
    if ($baru !== $ulang) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }
    if ($baru === $lama && $lama !== '') {
        $errors[] = 'Password baru harus berbeda dari password lama.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE users_id = ?');
        $stmt->execute([password_hash($baru, PASSWORD_DEFAULT), $id]);

        session_regenerate_id(true);
        $_SESSION['pesan_sukses'] = 'Password berhasil diganti.';
        alihkan('pelanggan/profil.php');
    }
}

$judul = 'Change Password';
$polos = true;
$skrip = ['lihat-password.js'];
require __DIR__ . '/../includes/header.php';
?>

        <a href="<?= e(url('pelanggan/profil.php')) ?>" class="tombol-kembali" aria-label="Kembali ke profil">&larr;</a>

        <h1 class="judul-auth">Ganti Password</h1>

        <?php if ($errors): ?>
            <div class="pesan-error" role="alert">
                <ul>
                    <?php foreach ($errors as $pesan): ?>
                        <li><?= e($pesan) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

            <?php input_password('password_lama', 'Old Password', 'Password saat ini', true); ?>
            <?php input_password('password_baru', 'New Password', 'Minimal 6 karakter', true); ?>
            <?php input_password('ulangi_password', 'Confirm Password', 'Ulangi password baru', true); ?>

            <div class="aksi-auth">
                <button type="submit" class="tombol">Simpan</button>
                <a href="<?= e(url('pelanggan/profil.php')) ?>" class="tombol tombol-sekunder">Batal</a>
            </div>
        </form>

<?php require __DIR__ . '/../includes/footer.php'; ?>