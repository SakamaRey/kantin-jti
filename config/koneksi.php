<?php
$host   = 'localhost';
$port   = '5432';
$dbname = 'kantin_jti';
$user   = 'postgres';
$pass   = 'postgres';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}