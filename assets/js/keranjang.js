// Keranjang belanja Kantin JTI
//
// - Tombol + di kartu menu memasukkan menu ke keranjang.
// - Bar keranjang (bawah layar) menampilkan jumlah item dan total.
// - Lembar keranjang: ubah jumlah, tambah catatan, geser KIRI untuk
//   menampilkan tombol catatan/hapus, dan pilih jam pengambilan.
//
// Data disimpan di localStorage browser, jadi tidak hilang saat halaman dimuat ulang.
// PENTING: harga di sini hanya untuk tampilan. Saat pembayaran, server harus
// menghitung ulang harga dari database (jangan percaya harga dari browser).

(function () {
  const KUNCI = "kantin_keranjang";
  const JAM_BUKA = 8 * 60; 
  const JAM_TUTUP = 16 * 60; 
  const LOMPAT = 30; 
  const MAKS_JUMLAH = 99;
  const MAKS_CATATAN = 200; 

  const barKeranjang = document.getElementById("bar-keranjang");
  const lembar = document.getElementById("keranjang");
  // Halaman tanpa keranjang (mis. riwayat) tidak perlu menjalankan apa pun
  if (!barKeranjang || !lembar) {
    return;
  }

  const barJumlah = document.getElementById("bar-jumlah");
  const barTotal = document.getElementById("bar-total");
  const tombolBuka = document.getElementById("buka-keranjang");
  const tombolPanah = document.getElementById("panah-keranjang");
  const tombolTutup = document.getElementById("tutup-keranjang");
  const lapisan = document.getElementById("lapisan-keranjang");
  const daftar = document.getElementById("daftar-keranjang");
  const pilihJam = document.getElementById("pilih-jam");
  const teksSubtotal = document.getElementById("subtotal");

  const IKON_CATATAN =
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13.5 6.5l4 4"/></svg>';
  const IKON_HAPUS =
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 13h10l1-13"/><path d="M10 11v6M14 11v6"/></svg>';

  // ---------- Data ----------
  // data.item = [{ id, nama, harga, gambar, jumlah, catatan }], data.jam = "08.30"
  let data = muatData();

  // Keadaan tampilan (tidak disimpan)
  let digeserId = null; 
  const catatanTerbuka = {}; 

  function muatData() {
    const kosong = { item: [], jam: "" };
    try {
      const mentah = JSON.parse(localStorage.getItem(KUNCI));
      if (!mentah || !Array.isArray(mentah.item)) {
        return kosong;
      }
      const item = [];
      mentah.item.forEach(function (i) {
        const id = Number(i.id);
        const harga = Number(i.harga);
        const jumlah = Math.floor(Number(i.jumlah));
        // Lewati data yang rusak
        if (!id || !(harga >= 0) || !(jumlah >= 1)) {
          return;
        }
        item.push({
          id: id,
          nama: String(i.nama),
          harga: harga,
          gambar: String(i.gambar),
          jumlah: Math.min(jumlah, MAKS_JUMLAH),
          catatan: String(i.catatan || "").slice(0, MAKS_CATATAN),
        });
      });
      return { item: item, jam: String(mentah.jam || "") };
    } catch (e) {
      return kosong;
    }
  }

  function simpan() {
    try {
      localStorage.setItem(KUNCI, JSON.stringify(data));
    } catch (e) {
      // Penyimpanan diblokir (mis. mode privat): keranjang tetap jalan sampai halaman ditutup
    }
  }

  function rupiah(angka) {
    return String(angka).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function dua(angka) {
    return angka < 10 ? "0" + angka : String(angka);
  }

  function totalJumlah() {
    return data.item.reduce(function (jumlah, i) {
      return jumlah + i.jumlah;
    }, 0);
  }

  function totalHarga() {
    return data.item.reduce(function (total, i) {
      return total + i.harga * i.jumlah;
    }, 0);
  }

  function buat(tag, kelas, teks) {
    const el = document.createElement(tag);
    if (kelas) {
      el.className = kelas;
    }
    if (teks !== undefined) {
      el.textContent = teks;
    }
    return el;
  }

  function buatTombol(kelas, label, isi, pakaiIkon) {
    const tombol = buat("button", kelas);
    tombol.type = "button";
    tombol.setAttribute("aria-label", label);
    if (pakaiIkon) {
      tombol.innerHTML = isi;
    } else {
      tombol.textContent = isi;
    }
    return tombol;
  }

  function cariItem(id) {
    return data.item.find(function (i) {
      return i.id === id;
    });
  }

  // ---------- Aksi keranjang ----------
  function tambahKeKeranjang(id, nama, harga, gambar) {
    const ada = cariItem(id);
    if (ada) {
      ada.jumlah = Math.min(ada.jumlah + 1, MAKS_JUMLAH);
    } else {
      data.item.push({
        id: id,
        nama: nama,
        harga: harga,
        gambar: gambar,
        jumlah: 1,
        catatan: "",
      });
    }
    simpan();
    render();
  }

  function ubahJumlah(id, selisih) {
    const item = cariItem(id);
    if (!item) {
      return;
    }
    // Jumlah minimal 1. Untuk membuang menu, geser baris lalu tekan tombol hapus.
    item.jumlah = Math.max(1, Math.min(item.jumlah + selisih, MAKS_JUMLAH));
    simpan();
    render();
  }

  function hapusItem(id) {
    data.item = data.item.filter(function (i) {
      return i.id !== id;
    });
    delete catatanTerbuka[id];
    digeserId = null;
    simpan();
    render();
  }

  function catatanTampil(item) {
    if (item.id in catatanTerbuka) {
      return catatanTerbuka[item.id];
    }
    return item.catatan !== ""; // catatan yang sudah ada langsung ditampilkan
  }

  function alihkanCatatan(id) {
    const item = cariItem(id);
    catatanTerbuka[id] = !catatanTampil(item);
    render();
    if (catatanTerbuka[id]) {
      const kolom = daftar.querySelector('[data-id="' + id + '"] textarea');
      if (kolom) {
        kolom.focus();
      }
    }
  }

  // Geser baris ke kiri (id) atau tutup semua (null)
  function aturGeser(id) {
    digeserId = id;
    daftar.querySelectorAll(".item-keranjang").forEach(function (li) {
      li.classList.toggle("digeser", Number(li.dataset.id) === id);
    });
  }

  // ---------- Tampilan ----------
  function buatBaris(item) {
    const li = buat("li", "item-keranjang");
    li.dataset.id = item.id;
    if (digeserId === item.id) {
      li.classList.add("digeser");
    }

    const baris = buat("div", "baris");

    // Tombol yang muncul saat baris digeser ke kiri
    const aksi = buat("div", "aksi-geser");
    const tombolCatatan = buatTombol("tombol-aksi", "Catatan untuk " + item.nama, IKON_CATATAN, true);
    const tombolHapus = buatTombol("tombol-aksi", "Hapus " + item.nama, IKON_HAPUS, true);
    tombolCatatan.addEventListener("click", function () {
      alihkanCatatan(item.id);
    });
    tombolHapus.addEventListener("click", function () {
      hapusItem(item.id);
    });
    aksi.append(tombolCatatan, tombolHapus);

    // Isi baris (bagian yang ikut bergeser)
    const isi = buat("div", "isi-item");

    const foto = buat("div", "foto-item");
    const gambar = document.createElement("img");
    gambar.src = item.gambar;
    gambar.alt = "";
    gambar.draggable = false;
    foto.appendChild(gambar);

    const info = buat("div", "info-item");
    info.appendChild(buat("h4", "", item.nama));
    info.appendChild(buat("p", "rincian", item.jumlah + " X Rp " + rupiah(item.harga)));

    const kanan = buat("div", "kanan-item");
    const stepper = buat("div", "stepper");
    const kurang = buatTombol("tombol-bulat", "Kurangi " + item.nama, "−", false);
    const tambah = buatTombol("tombol-bulat isi", "Tambah " + item.nama, "+", false);
    kurang.disabled = item.jumlah <= 1;
    tambah.disabled = item.jumlah >= MAKS_JUMLAH;
    kurang.addEventListener("click", function () {
      ubahJumlah(item.id, -1);
    });
    tambah.addEventListener("click", function () {
      ubahJumlah(item.id, 1);
    });
    stepper.append(kurang, buat("span", "jumlah", String(item.jumlah)), tambah);
    kanan.append(stepper, buat("p", "total-item", "Rp " + rupiah(item.harga * item.jumlah)));

    isi.append(foto, info, kanan);
    pasangGeser(isi, item.id);
    baris.append(aksi, isi);
    li.appendChild(baris);

    // Fokus ke tombol di balik baris (mis. lewat keyboard) ikut menggeser barisnya
    li.addEventListener("focusin", function (e) {
      if (e.target.closest(".aksi-geser")) {
        aturGeser(item.id);
      }
    });

    // Kotak catatan
    if (catatanTampil(item)) {
      const kotak = buat("div", "kotak-catatan");
      const kolom = document.createElement("textarea");
      kolom.rows = 2;
      kolom.maxLength = MAKS_CATATAN;
      kolom.placeholder = "Catatan";
      kolom.setAttribute("aria-label", "Catatan untuk " + item.nama);
      kolom.value = item.catatan;
      kolom.addEventListener("input", function () {
        item.catatan = kolom.value; 
        simpan();
      });
      kotak.appendChild(kolom);
      li.appendChild(kotak);
    }

    return li;
  }

  // Geser dengan jari (layar sentuh) atau drag mouse
  function pasangGeser(isi, id) {
    let awalX = null;

    isi.addEventListener("pointerdown", function (e) {
      if (e.target.closest("button")) {
        return; // tekan tombol +/- bukan untuk menggeser
      }
      awalX = e.clientX;
      isi.setPointerCapture(e.pointerId);
    });

    isi.addEventListener("pointerup", function (e) {
      if (awalX === null) {
        return;
      }
      const selisih = e.clientX - awalX;
      awalX = null;
      if (selisih < -40) {
        aturGeser(id); // geser ke kiri: tampilkan tombol
      } else if (selisih > 40) {
        aturGeser(null); // geser ke kanan: tutup kembali
      }
    });

    isi.addEventListener("pointercancel", function () {
      awalX = null;
    });
  }

  function gambarDaftar() {
    daftar.innerHTML = "";
    data.item.forEach(function (item) {
      daftar.appendChild(buatBaris(item));
    });
  }

  function gambarBar() {
    const jumlah = totalJumlah();
    barJumlah.textContent = jumlah + (jumlah > 1 ? " Items" : " Item");
    barTotal.textContent = "RP " + rupiah(totalHarga());
    barKeranjang.hidden = data.item.length === 0; // bar hanya tampil jika ada isi
  }

  function gambarSubtotal() {
    teksSubtotal.textContent = "Rp. " + rupiah(totalHarga()) + ",-";
  }

  function render() {
    gambarBar();
    gambarDaftar();
    gambarSubtotal();
    if (data.item.length === 0) {
      tutupKeranjang();
    }
  }

  // ---------- Jam pengambilan ----------
  // Pilihan tiap 30 menit dari 08.00 sampai 16.00. Jam yang sudah lewat tidak ditampilkan.

function isiPilihanJam() {
    pilihJam.innerHTML = "";
    for (let menit = JAM_BUKA; menit <= JAM_TUTUP; menit += LOMPAT) {
      const jam = dua(Math.floor(menit / 60)) + "." + dua(menit % 60);
      const opsi = document.createElement("option");
      opsi.value = jam;
      opsi.textContent = jam;
      pilihJam.appendChild(opsi);
    }

    pilihJam.disabled = false;
    pilihJam.selectedIndex = 0;
    if (data.jam) {
      pilihJam.value = data.jam;
      if (pilihJam.selectedIndex === -1) {
        pilihJam.selectedIndex = 0;
      }
    }
    data.jam = pilihJam.value;
    simpan();
  }
  pilihJam.addEventListener("change", function () {
    data.jam = pilihJam.value;
    simpan();
  });

  // ---------- Buka / tutup lembar keranjang ----------
  function bukaKeranjang() {
    if (data.item.length === 0) {
      return;
    }
    isiPilihanJam();
    lembar.classList.add("terbuka");
    lapisan.classList.add("terbuka");
    lembar.setAttribute("aria-hidden", "false");
    document.body.classList.add("kunci-gulir");
    tombolTutup.focus();
  }

  function tutupKeranjang() {
    if (!lembar.classList.contains("terbuka")) {
      return;
    }
    lembar.classList.remove("terbuka");
    lapisan.classList.remove("terbuka");
    lembar.setAttribute("aria-hidden", "true");
    document.body.classList.remove("kunci-gulir");
    aturGeser(null);
    if (!barKeranjang.hidden) {
      tombolBuka.focus();
    }
  }

  tombolBuka.addEventListener("click", bukaKeranjang);
  tombolPanah.addEventListener("click", bukaKeranjang);
  tombolTutup.addEventListener("click", tutupKeranjang);
  lapisan.addEventListener("click", tutupKeranjang);
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      tutupKeranjang();
    }
  });

  // ---------- Tombol + di kartu menu ----------
  document.addEventListener("click", function (e) {
    const tombol = e.target.closest(".tombol-tambah");
    if (!tombol) {
      return;
    }
    const kartu = tombol.closest(".kartu-menu");
    tambahKeKeranjang(
      Number(kartu.dataset.id),
      kartu.dataset.nama,
      Number(kartu.dataset.harga),
      kartu.dataset.gambar
    );
  });

  // Keranjang di tab lain berubah: ikuti
  window.addEventListener("storage", function (e) {
    if (e.key === KUNCI) {
      data = muatData();
      render();
    }
  });

  render();
})();