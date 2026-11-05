<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

// Sudah login? Tidak perlu melihat halaman ini.
if (pengguna_saat_ini()) {
    alihkan('index.php');
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Boleh login memakai username ATAU email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $username]);
    $users = $stmt->fetch();

    if (csrf_valid() && $users && password_verify($password, $users['password'])) {
        masukkan_session($users);
        alihkan('index.php');
    }

    $error = 'Username atau password salah.';
}

$judul = 'Login';
$polos = true;
$skrip = ['lihat-password.js'];
require __DIR__ . '/../includes/header.php';
?>

        <h1 class="judul-auth">Selamat Datang di<br>Kantin JTI</h1>

        <?php if ($error): ?>
            <div class="pesan-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="contoh: Carmenita" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="contoh: Carmen****" required>
            </div>

            <div class="aksi-auth">
                <button type="submit" class="tombol">Login</button>
                <a href="<?= e(url('auth/daftar.php')) ?>" class="tombol tombol-sekunder">Sign Up</a>
            </div>
        </form>

<?php require __DIR__ . '/../includes/footer.php'; ?>