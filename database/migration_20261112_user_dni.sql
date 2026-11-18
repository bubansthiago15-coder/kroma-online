ALTER TABLE usuarios
    ADD COLUMN dni VARCHAR(8) NULL AFTER telefono,
    ADD UNIQUE KEY uq_usuarios_dni (dni);
