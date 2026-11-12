--menambahkan kolom baru pada users
ALTER TABLE users
ADD COLUMN security_question VARCHAR(255),
ADD COLUMN security_answer VARCHAR(255);