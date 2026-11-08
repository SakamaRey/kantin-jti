<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

if (pengguna_saat_ini()) {
    alihkan('pelanggan/profil.php');
}

$errors   = [];
$email    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $baru     = $_POST['password_baru'] ?? '';
    $ulang    = $_POST['ulangi_password'] ?? '';

    if (!csrf_valid()) {
        $errors[] = 'Sesi form tidak valid, muat ulang halaman.';
    }
    if (strlen($baru) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }
    if ($baru !== $ulang) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    // Email DAN username harus cocok pada akun yang sama
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT users_id FROM users WHERE email = ? AND username = ?');
        $stmt->execute([$email, $username]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            $errors[] = 'Email dan username tidak cocok dengan akun mana pun.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE users_id = ?');
            $stmt->execute([password_hash($baru, PASSWORD_DEFAULT), $id]);

            $_SESSION['pesan_sukses'] = 'Password berhasil direset. Silakan login.';
            alihkan('auth/login.php');
        }
    }
}

$judul = 'Lupa Password';
$polos = true;
$skrip = ['lihat-password.js'];
require __DIR__ . '/../includes/header.php';
?>

        <a href="<?= e(url('auth/login.php')) ?>" class="tombol-kembali" aria-label="Kembali ke login">&larr;</a>

        <h1 class="judul-auth">Lupa Password</h1>

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

            <div class="field">
                <label for="email">Email <span class="wajib">*</span></label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="Email saat mendaftar" required>
            </div>

            <div class="field">
                <label for="username">Username <span class="wajib">*</span></label>
                <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="Username akunmu" required>
            </div>

            <?php input_password('password_baru', 'New Password', 'Minimal 6 karakter', true); ?>
            <?php input_password('ulangi_password', 'Confirm Password', 'Ulangi password baru', true); ?>

            <div class="aksi-auth">
                <button type="submit" class="tombol">Reset Password</button>
                <a href="<?= e(url('auth/login.php')) ?>" class="tombol tombol-sekunder">Batal</a>
            </div>
        </form>

<?php require __DIR__ . '/../includes/footer.php'; ?>