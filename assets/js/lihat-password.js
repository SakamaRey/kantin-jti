document.querySelectorAll(".lihat-password").forEach(function (tombol) {
  tombol.addEventListener("click", function () {
    const kolom = document.getElementById(tombol.dataset.target);
    const jadiTerlihat = kolom.type === "password";

    kolom.type = jadiTerlihat ? "text" : "password";
    tombol.querySelector(".mata-tutup").hidden = jadiTerlihat;
    tombol.querySelector(".mata-buka").hidden = !jadiTerlihat;
    tombol.setAttribute(
      "aria-label",
      jadiTerlihat ? "Sembunyikan password" : "Tampilkan password",
    );
  });
});
