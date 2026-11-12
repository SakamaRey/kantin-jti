--menambahkan kolom baru pada users
ALTER TABLE users
ADD COLUMN IF NOT EXISTS security_question VARCHAR(255),
ADD COLUMN IF NOT EXISTS security_answer VARCHAR(255),
ADD COLUMN IF NOT EXISTS no_hp varchar(15);

--Menambahkan nilai true pada tabel menu
ALTER TABLE menu ALTER COLUMN tersedia SET DEFAULT TRUE;