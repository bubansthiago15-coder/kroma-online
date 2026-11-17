# Publicar Kroma con Supabase

Kroma puede usar MySQL/MariaDB en XAMPP o PostgreSQL en Supabase seleccionando el controlador con `DB_DRIVER`. GitHub aloja el código fuente; un servidor PHP ejecuta la tienda y se conecta a Supabase. GitHub Pages no ejecuta el backend PHP.

## Ruta sencilla para una demostración: Render

El proyecto ahora incluye un `Dockerfile` que configura Apache para servir `public/` y habilita `pdo_pgsql`, `mbstring` y `fileinfo`. Render puede construirlo desde GitHub.

1. Crea la cuenta de Render y conecta tu cuenta de GitHub.
2. En Render selecciona **New → Web Service** y elige el repositorio de Kroma.
3. En Runtime/Language selecciona **Docker**, usa la rama `main` y la ruta `/Dockerfile`.
4. Para una demostración académica puedes elegir **Free**. Render publica una URL `onrender.com`; el primer acceso puede tardar porque el servicio gratuito se pausa después de 15 minutos sin visitas y demora cerca de un minuto en despertarse. El plan gratuito puede reiniciar el servicio y no conserva cambios de archivos; no lo uses para ventas reales ni cargues imágenes que necesites conservar desde el panel.
5. Antes de crear el servicio, añade las variables secretas indicadas abajo. Luego pulsa **Deploy Web Service** y revisa **Logs** si el despliegue falla.

La tarifa gratuita tiene límites de uso y está destinada a pruebas y proyectos personales; revisa el panel de uso y la página de planes antes de dejarlo funcionando. Si necesitas servicio siempre activo o conservar imágenes que se suban desde administración, hace falta un plan/almacenamiento persistente o un servicio externo para imágenes.

## 1. Completar el esquema existente

En Supabase, abre **SQL Editor** y ejecuta `database/supabase_reparaciones.sql`. El script repara índices únicos, secuencias y triggers sin borrar filas. Si devuelve un error por duplicados, corrige esos datos primero. Una ejecución correcta suele indicar `Success. No rows returned`, porque son operaciones DDL.

El script no importa catálogo ni usuarios. Confirma que Supabase tenga los registros que necesita la tienda, incluidos al menos los roles, categorías, marcas, productos e inventario. Si solo migraste la estructura, tendrás que importar también los datos desde `database/seed.sql`, convertido a PostgreSQL y revisado antes de ejecutarlo.

## 2. Subir el código a GitHub

1. Crea un repositorio vacío. No subas `.env`, contenido de `storage/` ni el ZIP de `exports/`.
2. Sube la raíz del proyecto conservando `app/`, `config/`, `database/`, `docs/`, `public/` y `storage/.gitkeep`.
3. Incluye `.gitignore`, `.env.example`, `.htaccess`, `README.md` e imágenes del catálogo que estén bajo `public/assets/`.
4. Confirma los archivos en la rama `main`.

El `.gitignore` excluye secretos y archivos locales cuando usas Git/GitHub Desktop. Al subir desde el navegador, omite esos archivos manualmente: GitHub no aplica tu `.gitignore` a una carga por arrastrar.

## 3. Desplegar en un servidor PHP

Conecta el repositorio a un servicio de alojamiento que ejecute PHP 8.2 o superior y tenga las extensiones `pdo_pgsql`, `mbstring` y `fileinfo`. Configura el directorio público del sitio en `public/`; `app/`, `config/` y `storage/` deben quedar fuera de la raíz web. El servidor debe soportar HTTPS y las rutas de Apache/rewrite del proyecto o configurar su equivalente.

En el panel de variables secretas del alojamiento define:

| Variable | Valor |
|---|---|
| `APP_DEBUG` | `0` |
| `DB_DRIVER` | `pgsql` |
| `DB_HOST` | Host copiado de Supabase → **Connect** → **Session pooler** |
| `DB_PORT` | `5432` para Session pooler |
| `DB_NAME` | Nombre de base indicado por Supabase, normalmente `postgres` |
| `DB_USER` | Usuario exacto copiado del Session pooler, normalmente `postgres.<project-ref>` |
| `DB_PASS` | Contraseña de la base de datos Supabase |
| `DB_SSLMODE` | `require` |
| `SESSION_NAME` | `kroma_session` |

Usa el host y usuario exactos del botón **Connect**; no los construyas a partir del nombre o la región del proyecto. El Session pooler funciona desde redes IPv4 y admite conexiones con consultas preparadas. No uses el pooler de transacciones para este backend persistente.

No agregues estas variables a `.env.example`, `README.md`, GitHub Actions con logs públicos ni al JavaScript del navegador. Deben permanecer en el panel privado del alojamiento. No pegues la contraseña en chats.

## 4. Comprobar la instalación

1. Confirma en los logs del alojamiento que PHP cargó `pdo_pgsql` y pudo conectarse a PostgreSQL.
2. Abre la URL HTTPS y revisa inicio, catálogo e imágenes.
3. Comprueba registro e inicio de sesión con una cuenta de demostración.
4. Prueba añadir al carrito, rechazar el pago simulado y confirmar una compra de prueba con stock reservado para pruebas.
5. Confirma en Supabase pedido, pago, historial, movimiento, auditoría y stock. Revisa también el rollback si una validación de stock falla.

El pago de Kroma es simulado; no procesa tarjetas ni pagos reales. Los archivos que se suban desde administración se guardan en `public/assets/images/uploads/`; el alojamiento debe conservar ese directorio entre despliegues o se debe cambiar el almacenamiento a un servicio persistente.

Antes de compartir la URL públicamente, cambia o desactiva las cuentas de demostración, especialmente el administrador, cuyas credenciales de ejemplo están documentadas en el repositorio. No uses esas contraseñas conocidas en la instancia online.

## GitHub Pages

No selecciones GitHub Pages como alojamiento de la tienda PHP. Pages sirve HTML, CSS y JavaScript estáticos; no procesa `public/index.php` ni puede guardar las credenciales PostgreSQL como secretos de backend. Si se necesita una página de presentación en Pages, debe ser un frontend estático aparte y no debe contener `DB_PASS` ni permisos privilegiados de Supabase.
