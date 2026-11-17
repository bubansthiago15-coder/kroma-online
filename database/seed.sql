USE kroma;
SET NAMES utf8mb4;
INSERT INTO roles(nombre,descripcion) VALUES ('cliente','Compra y consulta sus propios pedidos'),('administrador','Administración del sistema');
INSERT INTO usuarios(id_rol,nombre,apellido,email,password_hash,estado) VALUES
((SELECT id_rol FROM roles WHERE nombre='administrador'),'Admin','Kroma','admin@kroma.test','$2y$10$2R6Y6.wtfc4Xi8j/YOExH.K8oarS0g0XQAbquQfmIk6If29qTJ4eO',1),
((SELECT id_rol FROM roles WHERE nombre='cliente'),'Cliente','Demo','cliente@kroma.test','$2y$10$KGBY8V4peGMjF7jdcfChoegiDtXQ7JA34Fr3hW7rQjEUVUNEUgAnO',1),
((SELECT id_rol FROM roles WHERE nombre='cliente'),'Lucía','Prueba','lucia@kroma.test','$2y$10$LbbRDtX4kwACaCpIbo2kGuPIFaTswUmlsRWFU1kN2H.WUUjnNIiKS',1);
INSERT INTO categorias(nombre,slug,descripcion,orden_visualizacion) VALUES
('Equipos portátiles','portatiles','Notebooks para estudio, trabajo y juego',1),('PC de escritorio','pc-escritorio','Computadoras de escritorio y mini PC',2),('Componentes informáticos','componentes','Partes y componentes para PC',3),('Periféricos','perifericos','Controles de entrada y salida',4),('Accesorios y movilidad','accesorios','Accesorios y movilidad',5),('Redes y conectividad','redes','Equipos de red y conectividad',6),('Monitores y pantallas','monitores','Pantallas para trabajo y entretenimiento',7),('Audio y video','audio-video','Audio, video y streaming',8),('Gaming','gaming','Productos para videojuegos',9),('Usados y reacondicionados','usados','Unidades de segunda mano inspeccionadas',10);
INSERT INTO marcas(nombre,slug) VALUES ('Lenovo','lenovo'),('ASUS','asus'),('Apple','apple'),('Acer','acer'),('Dell','dell'),('Kroma','kroma'),('AMD','amd'),('Intel','intel'),('NVIDIA','nvidia'),('Kingston','kingston'),('Logitech','logitech'),('Redragon','redragon'),('Samsung','samsung'),('HyperX','hyperx'),('TP-Link','tp-link'),('ViewSonic','viewsonic'),('Anker','anker'),('Xiaomi','xiaomi'),('JBL','jbl'),('Gigabyte','gigabyte'),('Xbox','xbox');
CREATE TEMPORARY TABLE demo_productos(cat VARCHAR(40),marca VARCHAR(40),nombre VARCHAR(180),modelo VARCHAR(120),precio DECIMAL(12,2),condicion VARCHAR(20),descripcion TEXT);
INSERT INTO demo_productos VALUES
('portatiles','lenovo','Lenovo IdeaPad 3 de 15,6 pulgadas','IdeaPad 3 15IAU7',679000,'nuevo','Notebook para estudio y tareas diarias. Configuración de referencia.'),('portatiles','asus','ASUS TUF Gaming F15','FX507ZC4',1499000,'nuevo','Notebook de juego con gráfica dedicada, ventilación reforzada.'),('portatiles','apple','Apple MacBook Air M2','MBA M2 13',1899000,'nuevo','Portátil ultraligero con chip Apple M2.'),('portatiles','acer','Acer Aspire 5','A515-57',899000,'nuevo','Notebook versátil para productividad.'),('portatiles','dell','Dell Inspiron 15','3530',849000,'nuevo','Notebook de 15 pulgadas para hogar y oficina.'),
('pc-escritorio','intel','PC de oficina Intel Core i5','K-Office i5',790000,'nuevo','Equipo de oficina, 16 GB RAM y SSD.'),('pc-escritorio','amd','PC de escritorio AMD Ryzen 5','K-Ryzen 5600G',890000,'nuevo','Equipo equilibrado con gráficos integrados.'),('pc-escritorio','kroma','PC gamer de gama media','K-Gamer M',1599000,'nuevo','Equipo gamer con componentes compatibles de gama media.'),('pc-escritorio','kroma','PC gamer de alto rendimiento','K-Gamer X',2899000,'nuevo','Equipo de alto rendimiento con gráfica dedicada.'),('pc-escritorio','intel','Mini PC de oficina','K-Mini i5',589000,'nuevo','Formato compacto para tareas de oficina.'),
('componentes','amd','Procesador AMD Ryzen 5 5600','Ryzen 5 5600',229000,'nuevo','Procesador de seis núcleos para escritorio.'),('componentes','intel','Procesador Intel Core i5-12400F','i5-12400F',279000,'nuevo','Procesador de escritorio de 12.ª generación.'),('componentes','nvidia','Placa de video GeForce RTX 4060','RTX 4060 8GB',699000,'nuevo','GPU dedicada con 8 GB de memoria.'),('componentes','kingston','Memoria RAM DDR4 de 16 GB','Fury Beast 16GB',59000,'nuevo','Módulo DDR4 para equipos compatibles.'),('componentes','kingston','SSD NVMe de 1 TB','NV2 1TB',119000,'nuevo','Unidad de estado sólido NVMe M.2.'),
('perifericos','logitech','Mouse Logitech G502 HERO','G502 HERO',78000,'nuevo','Mouse con cable, sensor HERO y botones programables.'),('perifericos','redragon','Teclado mecánico Redragon K552','K552 Kumara',69000,'nuevo','Teclado mecánico compacto con conexión USB.'),('perifericos','samsung','Monitor Samsung de 24 pulgadas','S24C310',229000,'nuevo','Monitor Full HD de 24 pulgadas.'),('perifericos','hyperx','Auriculares HyperX Cloud II','Cloud II',179000,'nuevo','Auriculares con micrófono para PC y consola.'),('perifericos','logitech','Webcam Logitech C920','C920 HD Pro',129000,'nuevo','Cámara web Full HD para videollamadas.'),
('accesorios','anker','Cargador USB-C 30 W','Nano 30W',39000,'nuevo','Cargador compacto USB-C.'),('accesorios','anker','Batería externa de 20.000 mAh','PowerCore 20K',69000,'nuevo','Batería externa con capacidad de 20.000 mAh.'),('accesorios','kroma','Cable USB-C de 1 metro','K-USB-C1',8500,'nuevo','Cable USB-C para carga y datos.'),('accesorios','kroma','Hub USB-C multipuerto','K-Hub 6en1',49000,'nuevo','Hub con puertos para periféricos y pantalla.'),('accesorios','xiaomi','Reloj inteligente Redmi Watch','Watch 4 Active',99000,'nuevo','Reloj inteligente con monitoreo básico.'),
('redes','tp-link','Router TP-Link Archer AX12','Archer AX12',89000,'nuevo','Router Wi-Fi 6 de doble banda.'),('redes','tp-link','Switch Gigabit de 8 puertos','TL-SG108',49000,'nuevo','Switch no administrable de ocho puertos.'),('redes','tp-link','Adaptador Wi-Fi USB AC600','Archer T2U Nano',24000,'nuevo','Adaptador inalámbrico USB de doble banda.'),('redes','tp-link','Repetidor Wi-Fi AC1200','RE305',59000,'nuevo','Extensor de cobertura Wi-Fi de doble banda.'),('redes','gigabyte','Placa de red PCIe Gigabit','GC-WBAX200',69000,'nuevo','Adaptador PCIe con conexión Gigabit y Wi-Fi.'),
('monitores','samsung','Monitor Full HD de 24 pulgadas','S24R350',219000,'nuevo','Pantalla Full HD de 24 pulgadas.'),('monitores','asus','Monitor gamer de 144 Hz','VG248QG',389000,'nuevo','Monitor gamer Full HD de alta frecuencia.'),('monitores','viewsonic','Monitor IPS de 27 pulgadas','VA2732-H',329000,'nuevo','Panel IPS de 27 pulgadas para productividad.'),('monitores','samsung','Monitor ultraancho de 34 pulgadas','SJ55W',699000,'nuevo','Pantalla ultrawide para multitarea.'),('monitores','kroma','Monitor portátil de 15,6 pulgadas','K-Portable 156',289000,'nuevo','Monitor portátil Full HD con entrada USB-C.'),
('audio-video','hyperx','Micrófono USB de escritorio','SoloCast',109000,'nuevo','Micrófono USB para llamadas y streaming.'),('audio-video','jbl','Auriculares inalámbricos JBL','Tune 520BT',79000,'nuevo','Auriculares Bluetooth de uso diario.'),('audio-video','logitech','Parlantes de escritorio 2.0','Z150',39000,'nuevo','Parlantes estéreo compactos.'),('audio-video','logitech','Cámara web Full HD','Brio 100',69000,'nuevo','Webcam Full HD para reuniones.'),('audio-video','hyperx','Micrófono para streaming','QuadCast S',229000,'nuevo','Micrófono USB con controles para streaming.'),
('gaming','xbox','Control inalámbrico para PC','Xbox Wireless Controller',119000,'nuevo','Control inalámbrico compatible con PC.'),('gaming','redragon','Teclado gamer mecánico','Kumara K552 RGB',74000,'nuevo','Teclado mecánico compacto para gaming.'),('gaming','logitech','Mouse gamer','G203 LIGHTSYNC',49000,'nuevo','Mouse gamer con cable y sensibilidad ajustable.'),('gaming','hyperx','Auriculares gamer','Cloud Stinger 2',99000,'nuevo','Auriculares con micrófono para sesiones de juego.'),('gaming','asus','Monitor gamer de 24 pulgadas y 144 Hz','VG249Q1A',419000,'nuevo','Monitor Full HD de 144 Hz para gaming.'),
('usados','lenovo','Notebook Lenovo usada IdeaPad','IdeaPad 3 U-014',429000,'usado','Unidad de segunda mano revisada. Marcas leves de uso; garantía demostrativa de 3 meses.'),('usados','dell','Notebook Dell usada Inspiron','Inspiron U-021',399000,'usado','Unidad usada revisada. Desgaste cosmético visible; batería de capacidad reducida.'),('usados','kroma','PC de escritorio reacondicionada','K-Office R5 Refurb',490000,'reacondicionado','Equipo reacondicionado y probado; puede presentar marcas estéticas.'),('usados','samsung','Monitor usado de 24 pulgadas','S24 Used-09',99000,'usado','Monitor usado y revisado, con marcas estéticas en el soporte.'),('usados','nvidia','Placa de video usada GTX 1660','GTX 1660 U-032',219000,'usado','Unidad usada probada; se vende individualmente con garantía limitada de 30 días.');
INSERT INTO productos(id_categoria,id_marca,nombre,slug,modelo,descripcion,tipo_producto,condicion,precio,precio_anterior,garantia_meses,publicado)
SELECT c.id_categoria,m.id_marca,d.nombre,LOWER(REPLACE(REPLACE(d.modelo,' ','-'),'.','-')),d.modelo,d.descripcion,d.condicion,d.condicion,d.precio,NULL,IF(d.condicion='nuevo',12,IF(d.condicion='usado',3,6)),1 FROM demo_productos d JOIN categorias c ON c.slug=d.cat JOIN marcas m ON m.slug=d.marca;
INSERT IGNORE INTO producto_categoria(id_producto,id_categoria) SELECT id_producto,id_categoria FROM productos;
INSERT IGNORE INTO producto_categoria(id_producto,id_categoria) SELECT p.id_producto,c.id_categoria FROM productos p JOIN categorias c ON c.slug='gaming' WHERE p.modelo IN ('Xbox Wireless Controller','Kumara K552 RGB','G203 LIGHTSYNC','Cloud Stinger 2','VG249Q1A');
INSERT INTO inventario(id_producto,cantidad_disponible) SELECT id_producto,CASE WHEN condicion='usado' THEN 1 WHEN id_producto%9=0 THEN 0 ELSE 3+(id_producto%8) END FROM productos;
INSERT INTO imagenes_producto(id_producto,ruta_imagen,texto_alternativo,es_principal) SELECT id_producto,CONCAT('assets/images/products/',slug,'.svg'),CONCAT('Ilustración individual de catálogo: ',nombre),1 FROM productos;
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Modelo',modelo,NULL FROM productos;
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Condición',condicion,NULL FROM productos;
INSERT INTO unidades_usadas(id_producto,codigo_unidad,estado_conservacion,descripcion_estado,precio_especifico,garantia_meses) SELECT id_producto,CONCAT('KRU-',LPAD(id_producto,5,'0')),condicion,descripcion,precio,garantia_meses FROM productos WHERE condicion IN ('usado','reacondicionado');
INSERT INTO promociones(id_producto,precio_promocional,fecha_inicio,fecha_fin,estado) SELECT id_producto,ROUND(precio*0.9,2),NOW()-INTERVAL 1 DAY,NOW()+INTERVAL 14 DAY,1 FROM productos WHERE id_producto%11=0;
INSERT INTO sucursales(nombre,direccion,ciudad,provincia,telefono) VALUES('Kroma Centro','Av. Tecnología 123','Buenos Aires','Buenos Aires','011-0000-0000');
INSERT INTO configuracion_envio(modalidad,costo,activo) VALUES('retiro',0,1),('envio',3500,1);
-- Pedido reservado como dato de práctica: todas las cifras son demostrativas, no ventas reales.
INSERT INTO pedidos(numero_pedido,id_usuario,estado,subtotal,descuento_total,costo_envio,total,modalidad_entrega,datos_destinatario_historicos) SELECT 'KR-DEMO-0001',u.id_usuario,'confirmado',p.precio,0,0,p.precio,'retiro',CONCAT(u.nombre,' ',u.apellido) FROM usuarios u JOIN productos p ON p.slug='ideapad-3-15iau7' WHERE u.email='cliente@kroma.test';
INSERT INTO detalle_pedido(id_pedido,id_producto,nombre_producto_historico,modelo_historico,precio_unitario,cantidad,descuento_unitario,subtotal_linea) SELECT o.id_pedido,p.id_producto,p.nombre,p.modelo,p.precio,1,0,p.precio FROM pedidos o JOIN productos p ON p.slug='ideapad-3-15iau7' WHERE o.numero_pedido='KR-DEMO-0001';
UPDATE inventario SET cantidad_disponible=cantidad_disponible-1 WHERE id_producto=(SELECT id_producto FROM productos WHERE slug='ideapad-3-15iau7');
INSERT INTO movimientos_inventario(id_producto,tipo_movimiento,cantidad,motivo,id_pedido) SELECT id_producto,'salida',1,'Pedido de demostración',id_pedido FROM pedidos JOIN productos ON productos.slug='ideapad-3-15iau7' WHERE numero_pedido='KR-DEMO-0001';
INSERT INTO pagos(id_pedido,metodo_pago,importe,estado_pago,referencia_operacion,fecha_procesamiento) SELECT id_pedido,'simulacion',total,'aprobado','DEMO-KR-DEMO-0001',NOW() FROM pedidos WHERE numero_pedido='KR-DEMO-0001';
INSERT INTO historial_pedidos(id_pedido,estado_anterior,estado_nuevo,observacion) SELECT id_pedido,NULL,'confirmado','Pedido de demostración; no es una venta real' FROM pedidos WHERE numero_pedido='KR-DEMO-0001';
DROP TEMPORARY TABLE demo_productos;


-- Datos visuales y fichas representativas para las categorías visibles
-- Fotos externas y características verificadas para un producto representativo
-- de cada categoría visible. Las imágenes siguen servidas desde sus fuentes web.
CREATE TABLE IF NOT EXISTS producto_fuente_imagen(
  id_producto INT PRIMARY KEY,
  url_fuente VARCHAR(500) NOT NULL,
  FOREIGN KEY(id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

UPDATE productos p JOIN categorias c ON c.slug='monitores'
SET p.id_categoria=c.id_categoria WHERE p.modelo='S24C310';
DELETE pc FROM producto_categoria pc JOIN productos p USING(id_producto)
WHERE p.modelo='S24C310';
INSERT INTO producto_categoria(id_producto,id_categoria)
SELECT p.id_producto,p.id_categoria FROM productos p WHERE p.modelo='S24C310';

UPDATE productos SET descripcion='Notebook de 15,6 pulgadas Full HD con pantalla antirreflejo y procesadores Intel Core de 12.ª generación según la configuración. La memoria y el almacenamiento pueden variar por SKU; verificá la variante antes de comprar.' WHERE modelo='IdeaPad 3 15IAU7';
UPDATE productos SET descripcion='PC de escritorio de referencia con AMD Ryzen 5 5600G, gráficos Radeon integrados, 16 GB DDR4 y SSD NVMe de 1 TB. Adecuada para tareas cotidianas y juegos livianos; la configuración publicada es demostrativa.' WHERE modelo='K-Ryzen 5600G';
UPDATE productos SET descripcion='Procesador de escritorio AMD Zen 3 con 6 núcleos y 12 hilos, frecuencia de 3,5 GHz y boost de hasta 4,4 GHz. Usa socket AM4 y tiene TDP de 65 W; requiere placa madre compatible.' WHERE modelo='Ryzen 5 5600';
UPDATE productos SET descripcion='Mouse gamer cableado con sensor óptico HERO 25K, sensibilidad configurable hasta 25.600 DPI y 11 botones programables. Incluye pesas ajustables para personalizar el balance.' WHERE modelo='G502 HERO';
UPDATE productos SET descripcion='Cargador compacto Anker Nano con salida máxima de 30 W y un puerto USB-C. Compatible con Power Delivery y PPS; ActiveShield 2.0 monitorea la temperatura durante la carga.' WHERE modelo='Nano 30W';
UPDATE productos SET descripcion='Router TP-Link Wi-Fi 6 de doble banda AX1500: hasta 1.201 Mbps en 5 GHz y 300 Mbps en 2,4 GHz. Incluye cuatro antenas externas, seguridad WPA3 y un puerto WAN más tres LAN Gigabit.' WHERE modelo='Archer AX12';
UPDATE productos SET descripcion='Monitor Samsung de 24 pulgadas Full HD con panel IPS, frecuencia de actualización de 75 Hz y AMD FreeSync. Su diseño de tres lados con bisel delgado permite armar configuraciones con varias pantallas.' WHERE modelo='S24C310';
UPDATE productos SET descripcion='Control inalámbrico Xbox con empuñaduras y gatillos texturizados, cruceta híbrida y botón Compartir. Se conecta por Bluetooth y es compatible con Xbox Series X|S, Xbox One, Windows, Android e iOS.' WHERE modelo='Xbox Wireless Controller';
UPDATE productos SET descripcion='Notebook Lenovo usada, revisada y probada antes de publicarse. Presenta marcas leves de uso; la fotografía es referencial y el estado de cada unidad se informa en su descripción.' WHERE modelo='IdeaPad 3 U-014';

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://www.woot.uz/images/thumbnails/400/400/detailed/21/ksjgoxkag3ldxespxm7wweuq8i08zt3f.jpg',ip.texto_alternativo='Lenovo IdeaPad 3 15IAU7, imagen referencial'
WHERE p.modelo='IdeaPad 3 15IAU7';UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn.tgdd.vn/Products/Images/44/305011/asus-tuf-gaming-f15-fx507zc4-i7-hn095w-thumb-600x600.jpg',ip.texto_alternativo='Notebook ASUS TUF Gaming F15 FX507ZC4'
WHERE p.modelo='FX507ZC4';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn.verk.net/images/77/2_911050-4000x4000.jpeg',ip.texto_alternativo='Apple MacBook Air de 13 pulgadas con chip M2'
WHERE p.modelo='MBA M2 13';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn1.technopark.ru/technopark/photos_resized/product/1000_1000/742482/0_742482.jpg?timestamp=2024-06-13_10-49-21',ip.texto_alternativo='Notebook Acer Aspire 5 A515-57'
WHERE p.modelo='A515-57';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://pctech.co.in/image/cache/catalog/Laptops/DELL/dell-inspiron-15-3530-silver-1-800x800w.jpg',ip.texto_alternativo='Notebook Dell Inspiron 15 3530'
WHERE p.modelo='3530';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn.tgdd.vn/Products/Images/44/313615/lenovo-ideapad-3-15iau7-i3-82rk00rwvn-thumb-600x600.jpg',ip.texto_alternativo='Notebook Lenovo usada IdeaPad 3, imagen de referencia'
WHERE p.modelo='IdeaPad 3 U-014';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://thumb.pccomponentes.com/w-530-530/articles/1085/10853023/2307-pc-racing-gaming-top-amd-ryzen-5-5600g-16gb-1tb-ssd-comprar.jpg',ip.texto_alternativo='PC de escritorio Ryzen 5 5600G, imagen de referencia'
WHERE p.modelo='K-Ryzen 5600G';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn.hstatic.net/products/200000079075/55_f01d08bb-a774-446c-be2a-f33b879c6433_a099f060f38f4e8eb56d11cd34fce1ad_master.png',ip.texto_alternativo='Procesador AMD Ryzen 5 5600'
WHERE p.modelo='Ryzen 5 5600';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://resource.logitech.com/b_white/content/dam/gaming/en/non-braid/hyjal-g502-hero/g502-hero-gallery-2-nb.png',ip.texto_alternativo='Mouse Logitech G502 HERO'
WHERE p.modelo='G502 HERO';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn.shopify.com/s/files/1/0493/9834/9974/products/A2147121_TD02.jpg?v=1740137317',ip.texto_alternativo='Cargador Anker Nano USB-C de 30 W'
WHERE p.modelo='Nano 30W';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://static.tp-link.com/upload/image-line/AX12_EU_1.0_overview_02_normal_20250812005920c.jpg',ip.texto_alternativo='Router TP-Link Archer AX12'
WHERE p.modelo='Archer AX12';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://img.mvideo.ru/Pdb/400167477b.jpg',ip.texto_alternativo='Monitor Samsung S24C310 de 24 pulgadas'
WHERE p.modelo='S24C310';
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='https://cdn-dynmedia-1.microsoft.com/is/image/microsoftcorp/surface_xbox-wireless-controller?fmt=png-alpha&scl=1',ip.texto_alternativo='Control inalámbrico Xbox Carbon Black'
WHERE p.modelo='Xbox Wireless Controller';

INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://psref.lenovo.com/Product/IdeaPad/IdeaPad_3_15IAU7' FROM productos WHERE modelo='IdeaPad 3 15IAU7'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.dienmayxanh.com/laptop/asus-tuf-gaming-f15-fx507zc4-i5-hn095w' FROM productos WHERE modelo='FX507ZC4'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.verkkokauppa.com/fi/product/911050/Apple-MacBook-Air-13-M2-16-Gt-1-Tt-2022-kannettava-hopea-MC7/reviews' FROM productos WHERE modelo='MBA M2 13'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.technopark.ru/noutbuki-acer-aspire-5-a515-57-nxkn3cd00d/' FROM productos WHERE modelo='A515-57'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://pctech.co.in/dell-inspiron-15-3530-laptop-13th-gen-core-i7-1355U-16gb-512gb-silver' FROM productos WHERE modelo='3530'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.thegioididong.com/laptop/lenovo-ideapad-110-15isk-i3-6006u-vs-lenovo-ideapad-3-15iau7-i3-82rk00rwvn' FROM productos WHERE modelo='IdeaPad 3 U-014'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.pccomponentes.com/pc-racing-gaming-top-amd-ryzen-5-5600g-16gb-1tb-ssd' FROM productos WHERE modelo='K-Ryzen 5600G'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.amd.com/en/support/downloads/drivers.html/processors/ryzen/ryzen-5000-series/amd-ryzen-5-5600.html' FROM productos WHERE modelo='Ryzen 5 5600'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.logitech.com/en-gb/shop/p/g502-hero-gaming-mouse.910-006098' FROM productos WHERE modelo='G502 HERO'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.anker.com/products/a2147' FROM productos WHERE modelo='Nano 30W'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.tp-link.com/kr/home-networking/wifi-router/archer-ax12/' FROM productos WHERE modelo='Archer AX12'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.samsung.com/cl/business/monitors/flat/24-s31c-fhd-75hz-monitor-plano-ls24c310ealxzs/' FROM productos WHERE modelo='S24C310'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);
INSERT INTO producto_fuente_imagen(id_producto,url_fuente)
SELECT id_producto,'https://www.microsoft.com/en-us/d/xbox-wireless-controller/8xn59crbsqgz' FROM productos WHERE modelo='Xbox Wireless Controller'
ON DUPLICATE KEY UPDATE url_fuente=VALUES(url_fuente);

-- Características resumidas para las fichas de producto.
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Pantalla','15,6 pulgadas Full HD antirreflejo',NULL FROM productos WHERE modelo='IdeaPad 3 15IAU7' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Pantalla');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Procesador','Intel Core de 12.ª generación (según SKU)',NULL FROM productos WHERE modelo='IdeaPad 3 15IAU7' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Procesador');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Memoria y almacenamiento','Varían según la configuración',NULL FROM productos WHERE modelo='IdeaPad 3 15IAU7' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Memoria y almacenamiento');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Procesador','AMD Ryzen 5 5600G',NULL FROM productos WHERE modelo='K-Ryzen 5600G' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Procesador');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Gráficos','Radeon integrados',NULL FROM productos WHERE modelo='K-Ryzen 5600G' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Gráficos');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Memoria','16 GB DDR4',NULL FROM productos WHERE modelo='K-Ryzen 5600G' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Memoria');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Almacenamiento','SSD NVMe de 1 TB',NULL FROM productos WHERE modelo='K-Ryzen 5600G' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Almacenamiento');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Núcleos / hilos','6 / 12',NULL FROM productos WHERE modelo='Ryzen 5 5600' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Núcleos / hilos');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Frecuencia','3,5 GHz base; hasta 4,4 GHz',NULL FROM productos WHERE modelo='Ryzen 5 5600' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Frecuencia');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Socket / TDP','AM4 / 65 W',NULL FROM productos WHERE modelo='Ryzen 5 5600' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Socket / TDP');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Sensor','HERO 25K, hasta 25.600 DPI',NULL FROM productos WHERE modelo='G502 HERO' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Sensor');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Botones','11 programables',NULL FROM productos WHERE modelo='G502 HERO' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Botones');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Conexión','USB cableado',NULL FROM productos WHERE modelo='G502 HERO' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Conexión');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Potencia máxima','30 W',NULL FROM productos WHERE modelo='Nano 30W' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Potencia máxima');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Puerto','1 USB-C con Power Delivery y PPS',NULL FROM productos WHERE modelo='Nano 30W' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Puerto');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Protección','ActiveShield 2.0',NULL FROM productos WHERE modelo='Nano 30W' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Protección');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Wi-Fi','6, doble banda AX1500',NULL FROM productos WHERE modelo='Archer AX12' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Wi-Fi');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Velocidad','1201 Mbps (5 GHz) + 300 Mbps (2,4 GHz)',NULL FROM productos WHERE modelo='Archer AX12' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Velocidad');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Antenas / puertos','4 antenas; 1 WAN y 3 LAN Gigabit',NULL FROM productos WHERE modelo='Archer AX12' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Antenas / puertos');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Pantalla','24 pulgadas Full HD, panel IPS',NULL FROM productos WHERE modelo='S24C310' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Pantalla');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Frecuencia','75 Hz',NULL FROM productos WHERE modelo='S24C310' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Frecuencia');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Sincronización','AMD FreeSync',NULL FROM productos WHERE modelo='S24C310' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Sincronización');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Conexión','Bluetooth, Xbox Wireless y USB-C',NULL FROM productos WHERE modelo='Xbox Wireless Controller' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Conexión');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Compatibilidad','Xbox Series X|S, Xbox One, Windows, Android e iOS',NULL FROM productos WHERE modelo='Xbox Wireless Controller' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Compatibilidad');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Autonomía','Hasta 40 horas (según uso)',NULL FROM productos WHERE modelo='Xbox Wireless Controller' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Autonomía');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Revisión','Probada antes de publicar',NULL FROM productos WHERE modelo='IdeaPad 3 U-014' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Revisión');
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad) SELECT id_producto,'Estado estético','Marcas leves de uso',NULL FROM productos WHERE modelo='IdeaPad 3 U-014' AND NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=productos.id_producto AND e.atributo='Estado estético');

