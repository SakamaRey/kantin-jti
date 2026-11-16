<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

if (pengguna_saat_ini()) {
    alihkan('pelanggan/profil.php');
}

$errors   = [];
$email    = '';
$username = '';
$pertanyaan = null; //nilai biarkan null 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $baru     = $_POST['password_baru'] ?? '';
    $ulang    = $_POST['ulangi_password'] ?? '';
    $langkah = $_POST['langkah'] ?? '1'; // tambahan untuk pertanyaan dan jawaban
    $jawaban = $_POST['Jawaban'] ?? '';

    if (!csrf_valid()) {
        $errors[] = 'Sesi form tidak valid, muat ulang halaman.';
    }
    if ($langkah == '2' && strlen($baru) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }
    if ($langkah == '2' && $baru !== $ulang) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    // Email DAN username harus cocok pada akun yang sama
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT users_id, security_question, security_answer FROM users WHERE email = ? AND username = ?');
        $stmt->execute([$email, $username]);
        $akun = $stmt->fetch();
        $id = $akun['users_id'] ?? null;

        if (!$id) {
            $errors[] = 'Email dan username tidak cocok dengan akun mana pun.';
        } else if (empty($akun ['security_answers'])) {//kondisi untuk error saat pertanyaan dan jawaban salah
            $errors[] = 'Anda belum membuat pertanyaan verifikasi';
        }else if ($langkah == '1') {
            $pertanyaan[] = $akun['security_question'];
        }else if (!password_verify(normalisasi_jawaban($jawaban), $akun['security_answers'])) {
             $pertanyaan = $akun['security_question'];
             $errors[] = 'Jawaban salah';//
        }else{
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE users_id = ?');
            $stmt->execute([password_hash($baru, PASSWORD_DEFAULT), $id]);

            $_SESSION['pesan_sukses'] = 'Password berhasil direset. Silakan login.';
            alihkan('auth/login.php');
        }
    }

    //fallback jika ada error akan menampilkan form 2
    if ($langkah === '2' && $pertanyaan === null) {
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
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
            
            <input type="hidden" name="langkah" value="<?= $pertanyaan == null ? '1' : '2'?>"> 
            <?php if ($pertanyaan != null): ?> 
                <input type="hidden" name="pertanyaan" value="<? e($pertanyaan) ?>"> 
            <?php endif; ?>

            <div class="field">
                <label for="email">Email <span class="wajib">*</span></label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="Email saat mendaftar" required>
            </div>

            <div class="field">
                <label for="username">Username <span class="wajib">*</span></label>
                <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="Username akunmu" required>
            </div>

            
            <?php if ($pertanyaan != null): ?>
                <div class="field">
                    <label for="jawaban"><?= e($pertanyaan) ?></label>
                    <input type="text" id="jawaban" name="jawaban" placeholder="Masukkan jawaban" require>
                </div>

            <?php input_password('password_baru', 'New Password', 'Minimal 6 karakter', true); ?>
            <?php input_password('ulangi_password', 'Confirm Password', 'Ulangi password baru', true); ?>

            <?php endif; ?>

            <div class="aksi-auth">
                <button type="submit" class="tombol"><?=  $pertanyaan === null ? 'Lanjut' : 'Reset Password' ?></button>
                <a href="<?= e(url('auth/login.php')) ?>" class="tombol tombol-sekunder">Batal</a>
            </div>
        </form>

<?php require __DIR__ . '/../includes/footer.php'; ?>