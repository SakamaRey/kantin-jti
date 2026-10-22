<?php
require __DIR__ . '/../includes/fungsi.php';

// Hapus semua data session lalu kembali ke katalog
$_SESSION = [];
session_destroy();

alihkan('index.php');