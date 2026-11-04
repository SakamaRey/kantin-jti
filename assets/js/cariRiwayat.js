const kolomCari   = document.getElementById('cari');
const semuaKartu  = document.querySelectorAll('.kartu-transaksi');
const pesanKosong = document.getElementById('pesan-kosong');
 
if (kolomCari) {
    kolomCari.addEventListener('input', () => {
        const kataKunci = kolomCari.value.trim().toLowerCase();
        let jumlahCocok = 0;
 
        semuaKartu.forEach((kartu) => {
            const cocok = kartu.dataset.nama.includes(kataKunci);
            kartu.hidden = !cocok;
            if (cocok) jumlahCocok++;
        });
 
        // Tampilkan pesan hanya jika ada transaksi tetapi tidak ada yang cocok
        pesanKosong.hidden = jumlahCocok > 0 || semuaKartu.length === 0;
    });
}