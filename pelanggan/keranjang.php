<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/fungsi.php';

wajib_login();

$keranjang   = keranjang_detail($pdo);
$jamTerpilih = $_SESSION['jam_ambil'] ?? '12:00';

$error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);

$judul    = 'Keranjang';
$tanpaBar = true;
require __DIR__ . '/../includes/header.php';
?>

        <h1 class="judul-halaman">Keranjang</h1>

        <?php if ($error): ?>
            <div class="pesan-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (!$keranjang['item']): ?>

            <div class="keranjang-kosong">
                <p>Keranjang masih kosong.</p>
                <a href="<?= e(url('index.php')) ?>" class="tombol">Pilih Menu</a>
            </div>

        <?php else: ?>

            <form method="post" action="<?= e(url('pelanggan/keranjang-proses.php')) ?>" class="form-keranjang">
                <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                <input type="hidden" name="kembali" value="keranjang">

                <!-- Tombol bawaan saat menekan Enter di kolom catatan: hanya menyimpan -->
                <button type="submit" name="aksi" value="simpan" class="tombol-bawaan" tabindex="-1" aria-hidden="true">Simpan</button>

                <ul class="daftar-keranjang">
                    <?php foreach ($keranjang['item'] as $item): ?>
                        <li class="item-keranjang">
                            <img src="<?= e(aset('images/' . $item['gambar'])) ?>" alt="" width="64" height="64">

                            <div class="item-info">
                                <h3><?= e($item['nama']) ?></h3>
                                <p class="item-harga">
                                    <?= e(rupiah($item['harga'])) ?> &times; <?= (int) $item['jumlah'] ?>
                                    = <strong><?= e(rupiah($item['subtotal'])) ?></strong>
                                </p>
                                <input type="text" name="catatan[<?= (int) $item['id'] ?>]"
                                       value="<?= e($item['catatan']) ?>" maxlength="200"
                                       placeholder="Catatan, mis. tanpa timun" aria-label="Catatan untuk <?= e($item['nama']) ?>">
                            </div>

                            <div class="item-aksi">
                                <div class="jumlah">
                                    <button type="submit" name="aksi" value="kurang:<?= (int) $item['id'] ?>" aria-label="Kurangi">&minus;</button>
                                    <span><?= (int) $item['jumlah'] ?></span>
                                    <button type="submit" name="aksi" value="tambah:<?= (int) $item['id'] ?>" aria-label="Tambah">+</button>
                                </div>
                                <button type="submit" name="aksi" value="hapus:<?= (int) $item['id'] ?>" class="tombol-hapus">Hapus</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="baris-jam">
                    <label for="jam_ambil">Akan Diambil Pada Jam:</label>
                    <select id="jam_ambil" name="jam_ambil">
                        <?php foreach (slot_jam() as $jam): ?>
                            <option value="<?= e($jam) ?>" <?= $jam === $jamTerpilih ? 'selected' : '' ?>><?= e(str_replace(':', '.', $jam)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ringkasan-total">
                    <span>Subtotal :</span>
                    <strong><?= e(rupiah_formal($keranjang['total'])) ?></strong>
                </div>

                <button type="submit" name="aksi" value="pesan" class="tombol">Lanjut Pembayaran</button>

        <?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>