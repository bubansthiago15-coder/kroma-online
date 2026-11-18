USE kroma;
-- Conexión / tablas
SHOW TABLES;
-- Búsqueda de catálogo y agrupación
SELECT p.nombre,m.nombre AS marca,c.nombre AS categoria,p.precio,i.cantidad_disponible FROM productos p JOIN marcas m USING(id_marca) JOIN categorias c USING(id_categoria) JOIN inventario i USING(id_producto) WHERE p.nombre LIKE '%monitor%' ORDER BY p.precio LIMIT 10;
-- Trigger: cada movimiento insertado debe generar auditoría vinculada. Dentro de transacción, ROLLBACK revierte ambos.
START TRANSACTION;
INSERT INTO movimientos_inventario(id_producto,tipo_movimiento,cantidad,motivo) SELECT id_producto,'ajuste',1,'Prueba trigger' FROM productos LIMIT 1;
SELECT * FROM auditoria ORDER BY id_auditoria DESC LIMIT 1;
ROLLBACK;
-- La fila de auditoría anterior debe desaparecer por rollback.
-- Reporte de ventas confirmadas / más vendidos
SELECT p.numero_pedido,p.fecha_pedido,p.total FROM pedidos p WHERE p.estado='confirmado' ORDER BY p.fecha_pedido DESC;
SELECT d.nombre_producto_historico,SUM(d.cantidad) unidades,SUM(d.subtotal_linea) importe FROM detalle_pedido d JOIN pedidos p USING(id_pedido) WHERE p.estado='confirmado' GROUP BY d.id_producto,d.nombre_producto_historico ORDER BY unidades DESC LIMIT 10;
-- Existencias válidas
SELECT id_producto,cantidad_disponible FROM inventario WHERE cantidad_disponible<0;
-- COMMIT y ROLLBACK se ejercitan desde el checkout de la aplicación: compra aprobada persiste;
-- compra con pago rechazado invierte el stock y no deja venta confirmada.
