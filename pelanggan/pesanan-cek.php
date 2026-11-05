<?php
// Dipanggil JavaScript (status-pesanan.js) untuk mengetahui status pesanan terbaru.
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$users = pengguna_saat_ini();

if (!$users) {
    http_response_code(401);
    echo json_encode(['status' => null]);
    exit;
}

// Hanya pesanan milik pengguna yang sedang login
$stmt = $pdo->prepare('SELECT status FROM pesanan WHERE id_pesanan = ? AND pengguna_id = ?');
$stmt->execute([(int) ($_GET['users_id'] ?? 0), $users['users_id']]);

echo json_encode(['status' => $stmt->fetchColumn() ?: null]);