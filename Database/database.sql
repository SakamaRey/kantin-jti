-- Database Kantin JTI (PostgreSQL)
-- Cara pakai:
--   createdb -U postgres kantin_jti
--   psql -U postgres -d kantin_jti -f database.sql

CREATE TABLE pengguna (
    id          SERIAL PRIMARY KEY,
    email       VARCHAR(120) NOT NULL UNIQUE,
    username    VARCHAR(80)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    peran       VARCHAR(10)  NOT NULL DEFAULT 'pelanggan'
                CHECK (peran IN ('pelanggan', 'admin')),
    dibuat_pada TIMESTAMP    NOT NULL DEFAULT NOW()
);

CREATE TABLE menu (
    id        SERIAL PRIMARY KEY,
    nama      VARCHAR(80)  NOT NULL,
    deskripsi VARCHAR(200) NOT NULL,
    harga     INT          NOT NULL,
    gambar    VARCHAR(120) NOT NULL,   -- nama file di folder images/
    tersedia  BOOLEAN      NOT NULL DEFAULT TRUE
);

CREATE TABLE pesanan (
    id          SERIAL PRIMARY KEY,
    kode        VARCHAR(10) NOT NULL UNIQUE,   -- contoh: P123456
    pengguna_id INT         NOT NULL REFERENCES pengguna(id),
    status      VARCHAR(10) NOT NULL DEFAULT 'menunggu'
                CHECK (status IN ('menunggu', 'diterima', 'ditolak', 'dibatalkan')),
    jam_ambil   TIME        NOT NULL,
    total       INT         NOT NULL,
    dibuat_pada TIMESTAMP   NOT NULL DEFAULT NOW()
);

CREATE TABLE item_pesanan (
    id         SERIAL PRIMARY KEY,
    pesanan_id INT NOT NULL REFERENCES pesanan(id) ON DELETE CASCADE,
    menu_id    INT NOT NULL REFERENCES menu(id),
    jumlah     INT NOT NULL,
    harga      INT NOT NULL,          -- harga saat dipesan
    catatan    VARCHAR(200)
);

INSERT INTO menu (nama, deskripsi, harga, gambar) VALUES
    ('Paket 1', 'Nasi + Ayam Pedas Kemangi + Telur',           10000, 'paket-1.jpg'),
    ('Paket 2', 'Nasi Goreng + Telur + Sosis + Udang + Sayur', 15000, 'paket-2.jpg'),
    ('Paket 3', 'Nasi + Ayam Bawang Krispi',                   10000, 'paket-3.jpg'),
    ('Paket 4', 'Mie Pangsit + Ayam Kecap + Sayur',            15000, 'paket-4.jpg');