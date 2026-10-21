// Pencarian menu: sembunyikan kartu yang tidak cocok dengan kata kunci.

const kolomCari = document.getElementById("cari");
const semuaKartu = document.querySelectorAll(".kartu-menu");
const pesanKosong = document.getElementById("pesan-kosong");

kolomCari.addEventListener("input", function () {
  // Ambil kata kunci, huruf kecil semua agar "Ayam" dan "ayam" dianggap sama
  const kataKunci = kolomCari.value.trim().toLowerCase();
  let jumlahTampil = 0;

  semuaKartu.forEach(function (kartu) {
    const nama = kartu.querySelector("h3").textContent.toLowerCase();
    const deskripsi = kartu
      .querySelector(".deskripsi")
      .textContent.toLowerCase();

    const cocok = nama.includes(kataKunci) || deskripsi.includes(kataKunci);

    kartu.hidden = !cocok; // hidden = true -> kartu disembunyikan
    if (cocok) {
      jumlahTampil++;
    }
  });

  // Tampilkan pesan jika tidak ada satu pun kartu yang cocok
  pesanKosong.hidden = jumlahTampil > 0;
});
