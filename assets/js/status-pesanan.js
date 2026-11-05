const layar = document.getElementById("status-pesanan");

if (layar) {
  setInterval(async function () {
    try {
      const respons = await fetch(layar.dataset.cek, { cache: "no-store" });
      const data = await respons.json();

      if (data.status !== layar.dataset.status) {
        location.reload();
      }
    } catch (error) {
      // Koneksi putus sebentar: abaikan, coba lagi 3 detik kemudian
    }
  }, 3000);
}
