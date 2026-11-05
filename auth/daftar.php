<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

if (pengguna_saat_ini()) {
    alihkan('index.php');
}

$errors   = [];
$email    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ulang    = $_POST['ulangi_password'] ?? '';

    // 1. Validasi isian
    if (!csrf_valid()) {
        $errors[] = 'Sesi form tidak valid, muat ulang halaman.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username harus 3 sampai 30 karakter.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $ulang) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    // 2. Cek email / username sudah dipakai atau belum
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = ? OR username = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'Email atau username sudah terdaftar.';
        }
    }

    // 3. Simpan, lalu langsung login
    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (email, username, password) VALUES (?, ?, ?) RETURNING users_id, username, role'
        );
        $stmt->execute([$email, $username, password_hash($password, PASSWORD_DEFAULT)]);

        masukkan_session($stmt->fetch());
        alihkan('index.php');
    }
}

$judul = 'Sign Up';
$polos = true;
$skrip = ['lihat-password.js'];
require __DIR__ . '/../includes/header.php';
?>

        <h1 class="judul-auth">Selamat Datang di<br>Kantin JTI</h1>

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
                <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="contoh: Carmen@gmail.com" required>
            </div>

            <div class="field">
                <label for="username">Username <span class="wajib">*</span></label>
                <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="contoh: Nyoman Ayu Carmenita" required>
            </div>

            <?php input_password('password', 'New Password', 'contoh: Carmen****', true); ?>
            <?php input_password('ulangi_password', 'Confirm Password', 'contoh: Carmen****', true); ?>

            <div class="aksi-auth">
                <button type="submit" class="tombol">Continue</button>
                <a href="<?= e(url('auth/login.php')) ?>" class="tombol tombol-sekunder">Sudah punya akun? Login</a>
            </div>
        </form>

<?php require __DIR__ . '/../includes/footer.php'; ?>