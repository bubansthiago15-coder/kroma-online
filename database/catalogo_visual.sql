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
WHERE p.modelo IN ('IdeaPad 3 15IAU7','IdeaPad 3 U-014');
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
SELECT id_producto,'https://psref.lenovo.com/Product/IdeaPad/IdeaPad_3_15IAU7' FROM productos WHERE modelo IN ('IdeaPad 3 15IAU7','IdeaPad 3 U-014')
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
