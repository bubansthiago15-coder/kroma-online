# Informe técnico de Kroma

**Estado revisado:** 17 de noviembre de 2026  
**Entorno comprobado:** XAMPP, PHP 8.5, MySQL/MariaDB, Apache
**Modo PostgreSQL:** disponible mediante PDO; falta comprobar una conexión real con el proyecto Supabase y el alojamiento online.

## Estado de ejecución

La aplicación está disponible en `http://localhost/kroma/`. La raíz de Apache reescribe la entrada a `public/index.php`; el código PHP, la configuración, los scripts SQL y el almacenamiento permanecen fuera del acceso público. La conexión comprobada usa la base `kroma`.

La aplicación es una tienda académica de demostración. El pago se registra como aprobado o rechazado dentro del sistema y no procesa tarjetas ni cobros bancarios.

## Estructura del proyecto

| Carpeta | Contenido |
|---|---|
| `app/Controllers` | Enrutamiento de acciones y coordinación de operaciones. |
| `app/Core` | Router y conexión PDO. |
| `app/Services` | Autenticación, catálogo, carrito y recursos de producto. |
| `app/Views` | Vistas PHP de tienda, cuenta, pedidos y administración. |
| `config` | Configuración de aplicación y conexión PDO MySQL o PostgreSQL. |
| `database` | Esquema actual, datos de demostración, permisos, consultas de referencia y migraciones. |
| `docs` | Manual operativo e informe técnico vigente. |
| `exports` | Paquetes ZIP de entrega. |
| `output` | Folleto PDF generado. |
| `public` | Punto de entrada web, CSS, JavaScript e imágenes servidas. |
| `storage` | Registro de errores y sesiones PHP. |

## Carrito y concurrencia

Los carritos de visitantes se guardan en su sesión PHP. Al iniciar sesión, sus productos se combinan con el carrito persistente de la cuenta y se limitan al stock disponible. Los carritos de cuentas usan `carritos` y `detalle_carrito`; cada alta, cambio o eliminación se realiza dentro de una transacción InnoDB.

Agregar un producto no reserva existencias. El stock se vuelve a validar al confirmar el pedido. En esa operación, la aplicación bloquea la cuenta, el carrito activo y sus líneas, y después bloquea las filas de producto e inventario en orden ascendente. Las escrituras de administración del stock siguen el mismo orden producto–inventario–unidad.

Si dos cuentas compran las últimas unidades, InnoDB serializa las operaciones en la fila de inventario. La segunda compra lee el stock actualizado y se rechaza si ya no alcanza. La actualización condicionada `cantidad_disponible >= cantidad` protege además el descuento contra una cantidad insuficiente.

Para artículos usados, la compra aprobada bloquea una unidad individual por cada unidad comprada. Cada unidad queda en una línea de pedido propia, pasa a estado vendida y genera su movimiento de inventario. Esto mantiene la unicidad de `detalle_pedido.id_unidad_usada`.

## Límites transaccionales de compra

Una compra aprobada mantiene en una sola transacción el pedido, sus detalles, el descuento de existencias, el cambio de estado de unidades usadas, los movimientos, la auditoría del trigger, la notificación de stock, el pago, el historial y la conversión del carrito. `COMMIT` confirma el conjunto completo.

Si una validación o escritura falla antes del `COMMIT`, `ROLLBACK` deshace todas esas escrituras y el carrito conserva sus productos. Un pago rechazado es un resultado de negocio: confirma el pedido cancelado, el pago rechazado y el historial, sin descontar stock ni convertir el carrito. El formulario usa CSRF y un token de un solo uso para evitar que el mismo envío cree pedidos duplicados.

Los totales se calculan en centavos enteros antes de persistirse en columnas `DECIMAL`. Los importes históricos del pedido no dependen de cambios posteriores en el catálogo.

## Configuración y acceso

La aplicación lee `.env` en la raíz del proyecto. Las variables definidas en Apache o Windows prevalecen. `.env.example` contiene los nombres requeridos: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `SESSION_NAME` y `APP_DEBUG`.

`database/schema.sql` crea el esquema para una instalación nueva y contiene instrucciones de reconstrucción de tablas. No se debe importar sobre una base que ya contiene datos. Para actualizar una base existente se aplican solo las migraciones faltantes, una vez y en orden.

Para Supabase, `DB_DRIVER=pgsql` selecciona el conector PDO PostgreSQL con `sslmode` configurable. Las consultas con diferencias de motor y los IDs generados usan sintaxis PostgreSQL cuando corresponde. La conexión depende de que el alojamiento tenga habilitado `pdo_pgsql` y reciba las credenciales mediante variables secretas. El XAMPP revisado actualmente solo ofrece los drivers PDO `mysql` y `sqlite`, por lo que el modo remoto aún no fue ejercitado desde este equipo.

Las cuentas de demostración y el pago simulado son solo para uso local/académico. El sistema no implementa una pasarela de pago.

## Verificaciones realizadas

- `http://localhost/kroma/`, las hojas CSS y el JavaScript del checkout respondieron con HTTP 200.
- Las rutas directas a `config` y a los scripts SQL respondieron con HTTP 403.
- PDO conectó con `kroma`; las columnas de DNI y facturación consultadas existen.
- El carrito persistente guardó, leyó y quitó una línea; la fila temporal se limpió.
- Una cantidad mayor al stock produjo rollback y no dejó creado un carrito activo.
- Dos conexiones PDO compitieron por la misma fila InnoDB; la segunda esperó el bloqueo y ambas transacciones terminaron en rollback, sin alterar existencias.
- Los archivos PHP y JavaScript pasaron comprobaciones sintácticas.
- La sintaxis PHP volvió a revisarse después de añadir el modo PostgreSQL. No se realizó una compra ni conexión de prueba contra Supabase porque el XAMPP local no tiene `pdo_pgsql` habilitado.

La verificación de concurrencia ejercitó el bloqueo real de InnoDB sin crear pedidos ni cambiar existencias. No se registró una compra de prueba que alterara el inventario.
