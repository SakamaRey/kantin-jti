<?php
// Salin file ini menjadi koneksi.php, lalu isi password PostgreSQL kamu.
// koneksi.php TIDAK ikut ke GitHub (lihat .gitignore).

$host   = 'localhost';
$port   = '5432';
$dbname = 'kantin_jti';
$user   = 'postgres';
$pass   = 'ISI_PASSWORD_KAMU';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}