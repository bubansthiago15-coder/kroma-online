-- Ejemplo DCL para MariaDB/MySQL. Reemplazar la contraseña antes de usar fuera de local.
CREATE USER IF NOT EXISTS 'kroma_app'@'localhost' IDENTIFIED BY 'CAMBIAR_POR_SECRETO_UNICO';
GRANT SELECT, INSERT, UPDATE, DELETE ON kroma.* TO 'kroma_app'@'localhost';
SHOW GRANTS FOR 'kroma_app'@'localhost';
-- REVOKE DELETE ON kroma.* FROM 'kroma_app'@'localhost';
-- En producción, separar migraciones de la cuenta de ejecución y conceder privilegios mínimos.
