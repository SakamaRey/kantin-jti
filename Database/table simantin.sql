-- Database: SIMANTIN-JTI

-- DROP DATABASE IF EXISTS "SIMANTIN-JTI";

CREATE DATABASE "SIMANTIN-JTI"
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'Indonesian_Indonesia.1252'
    LC_CTYPE = 'Indonesian_Indonesia.1252'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;


CREATE TABLE users(
	users_id serial primary key,
	email varchar(50) NOT NULL UNIQUE,
	username varchar (50) NOT NULL UNIQUE,
	password varchar (100) NOT NULL ,
	role varchar(15) NOT NULL DEFAULT 'pelanggan'
	CHECK (role IN('pelanggan', 'admin')),
	create_account TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pesanan(
	id_pesanan serial primary key,
	kode varchar(15) NOT NULL UNIQUE,
	users_id int NOT NULL REFERENCES users(users_id),
	status varchar(10) NOT NULL DEFAULT 'menunggu'
	CHECK (status IN('menunggu', 'diterima', 'ditolak', 'dibatalkan', 'selesai')),
	jam_ambil time NOT NULL,
	total int NOT NULL CHECK (total >= 0),
	create_pesanan timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu(
	id_menu serial primary key,
	nama_menu varchar(20) NOT NULL,
	deskripsi varchar(150) NOT NULL,
	harga int NOT NULL CHECK (harga >= 0),
	gambar varchar(100) NOT NULL,
	tersedia boolean NOT NULL
);

CREATE TABLE item_pesanan(
	id_item serial primary key,
	id_pesanan int NOT NULL REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
	id_menu int NOT NULL REFERENCES menu(id_menu),
	jumlah int NOT NULL CHECK (jumlah > 0),
	harga int NOT NULL CHECK (harga > 0),
	catatan varchar(200)
);

INSERT INTO menu (nama_menu, deskripsi, harga, gambar, tersedia) VALUES
    ('Paket 1', 'Nasi + Ayam Pedas Kemangi + Telur',           10000, 'paket-1.jpg', true),
    ('Paket 2', 'Nasi Goreng + Telur + Sosis + Udang + Sayur', 15000, 'paket-2.jpg',true),
    ('Paket 3', 'Nasi + Ayam Bawang Krispi',                   10000, 'paket-3.jpg',true),
    ('Paket 4', 'Mie Pangsit + Ayam Kecap + Sayur',            15000, 'paket-4.jpg', true);