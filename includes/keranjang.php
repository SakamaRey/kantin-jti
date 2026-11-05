<?php
// Fungsi keranjang belanja dan pembuatan pesanan.
// Keranjang disimpan di session (hilang jika logout)
// Pilihan jam ambil: 10:00, 10:30, ... 14:00
function slot_jam()
{
    $slot = [];
    for ($menit = 10 * 60; $menit <= 14 * 60; $menit += 30) {
        $slot[] = sprintf('%02d:%02d', intdiv($menit, 60), $menit % 60);
    }
    return $slot;
}

// Tambah (+1) atau kurangi (-1) jumlah satu menu. Jumlah 0 berarti dihapus dari keranjang.
function keranjang_ubah($menuId, $selisih)
{
    $jumlah = ($_SESSION['keranjang'][$menuId]['jumlah'] ?? 0) + $selisih;

    if ($jumlah <= 0) {
        unset($_SESSION['keranjang'][$menuId]);
        return;
    }

    $_SESSION['keranjang'][$menuId]['jumlah']  = min($jumlah, 20); // maksimal 20 per menu
    $_SESSION['keranjang'][$menuId]['catatan'] = $_SESSION['keranjang'][$menuId]['catatan'] ?? '';
}

function keranjang_hapus($menuId)
{
    unset($_SESSION['keranjang'][$menuId]);
}

function keranjang_kosongkan()
{
    unset($_SESSION['keranjang'], $_SESSION['jam_ambil']);
}

// Isi keranjang lengkap dengan nama dan harga dari database.
function keranjang_detail(PDO $pdo)
{
    $hasil = ['item' => [], 'jumlah_item' => 0, 'total' => 0];
    $isi   = $_SESSION['keranjang'] ?? [];

    if (!$isi) {
        return $hasil;
    }

    $id    = array_keys($isi);
    $tanda = implode(',', array_fill(0, count($id), '?'));
    $stmt  = $pdo->prepare("SELECT * FROM menu WHERE tersedia = TRUE AND id IN ($tanda) ORDER BY id");
    $stmt->execute($id);

    foreach ($stmt->fetchAll() as $menu) {
        $jumlah = $isi[$menu['id']]['jumlah'];

        $hasil['item'][] = [
            'id'       => (int) $menu['id'],
            'nama'     => $menu['nama'],
            'gambar'   => $menu['gambar'],
            'harga'    => (int) $menu['harga'],
            'jumlah'   => $jumlah,
            'catatan'  => $isi[$menu['id']]['catatan'] ?? '',
            'subtotal' => $menu['harga'] * $jumlah,
        ];
        $hasil['jumlah_item'] += $jumlah;
        $hasil['total']       += $menu['harga'] * $jumlah;
    }

    return $hasil;
}

// Simpan isi keranjang sebagai pesanan baru (status awal: menunggu).
function buat_pesanan(PDO $pdo, $penggunaId)
{
    $keranjang = keranjang_detail($pdo);

    if (!$keranjang['item']) {
        throw new RuntimeException('Keranjang masih kosong.');
    }

    $jamAmbil = $_SESSION['jam_ambil'] ?? '12:00';

    // Transaksi: pesanan dan semua itemnya tersimpan semua, atau tidak sama sekali.
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO pesanan (kode, pengguna_id, jam_ambil, total) VALUES (?, ?, ?, ?)
             ON CONFLICT (kode) DO NOTHING RETURNING id'
        );

        for ($percobaan = 0;; $percobaan++) {
            $kode = 'P' . random_int(100000, 999999);
            $stmt->execute([$kode, $penggunaId, $jamAmbil, $keranjang['total']]);
            $pesananId = $stmt->fetchColumn();

            if ($pesananId !== false) {
                break;
            }
            if ($percobaan >= 5) {
                throw new RuntimeException('Gagal membuat kode pesanan.');
            }
        }

        $baris = $pdo->prepare(
            'INSERT INTO item_pesanan (pesanan_id, menu_id, jumlah, harga, catatan) VALUES (?, ?, ?, ?, ?)'
        );
        foreach ($keranjang['item'] as $item) {
            $catatan = trim($item['catatan']);
            $baris->execute([$pesananId, $item['id'], $item['jumlah'], $item['harga'], $catatan === '' ? null : $catatan]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    keranjang_kosongkan();

    return ['id' => (int) $pesananId, 'kode' => $kode];
}