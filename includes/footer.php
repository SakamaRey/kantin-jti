    </main>

    <?php if (!($polos ?? false)): ?>
    <!-- Bar keranjang (mobile). Angkanya masih contoh, diisi di commit 10. -->
    <div class="bar-keranjang">
        <span>0 item</span>
        <strong>Rp 0</strong>
        <a href="#" class="tombol-lanjut">Lihat Keranjang</a>
    </div>

    <!-- Navigasi bawah (mobile) -->
    <nav class="nav-bawah" aria-label="Navigasi bawah">
        <a href="<?= e(url('index.php')) ?>" class="aktif">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11l9-8 9 8v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg>
            <span>Home</span>
        </a>
        <a href="#">
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