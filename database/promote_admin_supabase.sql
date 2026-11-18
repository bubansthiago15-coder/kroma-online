-- Supabase/PostgreSQL: promote the already-registered admin@gmail.com account.
-- First create the account through Kroma's registration page. This script does
-- not contain or reset a password, so credentials are never stored in Git.
DO $$
DECLARE
    admin_role_id integer;
    updated_rows integer;
BEGIN
    SELECT id_rol
      INTO admin_role_id
      FROM roles
     WHERE nombre = 'administrador';

    IF admin_role_id IS NULL THEN
        RAISE EXCEPTION 'No existe el rol administrador en la tabla roles.';
    END IF;

    UPDATE usuarios
       SET id_rol = admin_role_id
     WHERE lower(email) = lower('admin@gmail.com');

    GET DIAGNOSTICS updated_rows = ROW_COUNT;
    IF updated_rows = 0 THEN
        RAISE EXCEPTION 'Primero registrá admin@gmail.com desde la aplicación.';
    END IF;
END $$;
