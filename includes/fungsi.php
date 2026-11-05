<?php
// Fungsi kecil yang dipakai di banyak halaman.

// Session harus dimulai sebelum ada output apa pun ke browser.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah kode HTML/JS dari data masuk ke halaman (keamanan XSS).
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

// 15000 -> "Rp 15.000"
function rupiah($angka)
{
    return 'Rp ' . number_format((int) $angka, 0, ',', '.');
}

// 70000 -> "Rp. 70.000,-" (format resmi untuk subtotal)
function rupiah_formal($angka)
{
    return 'Rp. ' . number_format((int) $angka, 0, ',', '.') . ',-';
}

// Kolom password dengan tombol mata (tampil/sembunyi). JS-nya: assets/js/lihat-password.js
function input_password($id, $label, $placeholder, $wajib = false)
{
    ?>
    <div class="field">
        <label for="<?= e($id) ?>"><?= e($label) ?><?php if ($wajib): ?> <span class="wajib">*</span><?php endif; ?></label>
        <div class="kolom-password">
            <input type="password" id="<?= e($id) ?>" name="<?= e($id) ?>" placeholder="<?= e($placeholder) ?>" required>
            <button type="button" class="lihat-password" data-target="<?= e($id) ?>" aria-label="Tampilkan password">
                <!-- mata tertutup (password tersembunyi) -->
                <svg class="mata-tutup" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10c4 5 14 5 18 0"/><path d="M6 14l-2 3M12 15.5V19M18 14l2 3"/></svg>
                <!-- mata terbuka (password terlihat) -->
                <svg class="mata-buka" viewBox="0 0 24 24" aria-hidden="true" hidden><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
    </div>
    <?php
}

// ---------- Alamat (URL) ----------
// Halaman ada di folder berbeda (index.php di depan, login.php di auth/),
// jadi alamat file CSS, gambar, dan link harus dihitung dari folder proyek.

// Bagian alamat sebelum folder proyek. Server php -S dari folder proyek: "" (kosong).
// XAMPP/Laragon di htdocs/kantin-jti: "/kantin-jti".
function base_url()
{
    static $base = null;
    if ($base === null) {
        $akarWeb    = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT']));
        $akarProyek = str_replace('\\', '/', dirname(__DIR__));
        $base       = rtrim(substr($akarProyek, strlen($akarWeb)), '/');
    }
    return $base;
}

// url('auth/login.php') -> "/auth/login.php"
function url($path = '')
{
    return base_url() . '/' . ltrim($path, '/');
}

// aset('css/style.css') -> "/assets/css/style.css"
function aset($path)
{
    return url('assets/' . ltrim($path, '/'));
}

// Pindah halaman lalu hentikan script.
function alihkan($path)
{
    header('Location: ' . url($path));
    exit;
}

// ---------- Login ----------

// Data pengguna yang sedang login (id, username, peran), atau null.
function pengguna_saat_ini()
{
    return $_SESSION['pengguna'] ?? null;
}

// Pasang di halaman yang hanya boleh dibuka setelah login.
function wajib_login()
{
    if (pengguna_saat_ini() === null) {
        alihkan('auth/login.php');
    }
}

// Token rahasia di setiap form agar form tidak bisa dikirim dari situs lain (CSRF).
function token_csrf()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid()
{
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', (string) $_POST['csrf']);
}

// Simpan pengguna ke session (dipakai setelah login / daftar).
function masukkan_session($pengguna)
{
    session_regenerate_id(true); // ganti ID session agar tidak bisa dibajak
    $_SESSION['pengguna'] = [
        'id'       => (int) $pengguna['id'],
        'username' => $pengguna['username'],
        'peran'    => $pengguna['peran'],
    ];
}

// Fungsi keranjang belanja
require_once __DIR__ . '/keranjang.php';

// 10000 -> "Rp. 10.000" (dipakai di daftar riwayat)
function rupiah_titik($angka)
{
    return 'Rp. ' . number_format((int) $angka, 0, ',', '.');
}

// "2026-08-12 11:00:00" -> "12 Aug 2026"
function tanggal_pendek($waktu)
{
    return date('d M Y', strtotime($waktu));
}