USE kroma;

ALTER TABLE usuarios
  ADD COLUMN token_sesion CHAR(64) NULL AFTER email_verificado,
  ADD COLUMN sesion_actividad DATETIME NULL AFTER token_sesion;

-- Preserve existing seller accounts as customers, without retaining staff access.
UPDATE usuarios
SET id_rol=(SELECT id_rol FROM roles WHERE nombre='cliente'),
    token_sesion=NULL,
    sesion_actividad=NULL
WHERE id_rol=(SELECT id_rol FROM roles WHERE nombre='vendedor');

DELETE FROM roles WHERE nombre='vendedor';
