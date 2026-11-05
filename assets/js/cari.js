// Pencarian menu: sembunyikan kartu yang tidak cocok dengan kata kunci.

const kolomCari = document.getElementById("cari");
const semuaItem = document.querySelectorAll("[data-cari]");
const pesanKosong = document.getElementById("pesan-kosong");

kolomCari.addEventListener("input", function () {
  // Huruf kecil semua agar "Ayam" dan "ayam" dianggap sama
  const kataKunci = kolomCari.value.trim().toLowerCase();
  let jumlahTampil = 0;

  semuaItem.forEach(function (item) {
    const cocok = item.textContent.toLowerCase().includes(kataKunci);

    item.hidden = !cocok; // hidden = true -> item disembunyikan
    if (cocok) {
      jumlahTampil++;
    }
  });

  // Tampilkan pesan jika tidak ada satu pun yang cocok
  if (pesanKosong) {
    pesanKosong.hidden = jumlahTampil > 0;
  }
});
