    </main>

    <?php if (!($polos ?? false)): ?>
    <?php if ($tampilKeranjang ?? true): ?>
    <!-- Bar keranjang (mobile). Angkanya masih contoh, diisi di commit 10. -->
    <div class="bar-keranjang" id="bar-keranjang" hidden>
        <button type="button" class="tombol-panah" id="panah-keranjang" aria-label="Buka keranjang">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 15l6-6 6 6"/></svg>
        </button>
        <div class="info-bar">
            <span id="bar-jumlah">0 Item</span>
            <strong id="bar-total">RP 0</strong>
        </div>
        <button type="button" class="tombol-lanjut" id="buka-keranjang">NEXT</button>
    </div>
    <div class="lapisan-keranjang" id="lapisan-keranjang"></div>
    <section class="keranjang" id="keranjang" role="dialog" aria-modal="true" aria-label="Keranjang" aria-hidden="true">
        <button type="button" class="tombol-tutup" id="tutup-keranjang" aria-label="Tutup keranjang">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </button>
 
        <ul class="daftar-keranjang" id="daftar-keranjang"></ul>
 
        <div class="bawah-keranjang">
            <label class="baris-jam">
                <span>Akan Diambil Pada Jam:</span>
                <select id="pilih-jam"></select>
            </label>
 
            <div class="baris-subtotal">
                <span>Subtotal :</span>
                <strong id="subtotal">Rp. 0,-</strong>
            </div>
            <button type="button" class="tombol" disabled>Lanjut Pembayaran</button>
        </div>
    </section>
    <?php endif; ?>
    <!-- Navigasi bawah (mobile) -->
    <nav class="nav-bawah" aria-label="Navigasi bawah">
        <a href="<?= e(url('index.php')) ?>" class="<?= ($menuAktif ?? 'home') === 'home' ? 'aktif' : '' ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11l9-8 9 8v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg>
            <span>Home</span>
        </a>
        <a href="<?= e(url('transaksi/riwayat.php')) ?>" class="<?= ($menuAktif ?? 'home') === 'transaksi' ? 'aktif' : '' ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/></svg>
            <span>Transaksi</span>
        </a>
        <a href="#">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-3.5 3.5-5.5 8-5.5s8 2 8 5.5z"/></svg>
            <span>Profile</span>
        </a>
    </nav>
    <?php endif; ?>

    <?php foreach (($skrip ?? []) as $file): ?>
        <script src="<?= e(aset('js/' . $file)) ?>"></script>
    <?php endforeach; ?>
</body>
</html>