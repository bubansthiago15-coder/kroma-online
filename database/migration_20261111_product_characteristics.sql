-- Complementa la ficha técnica con datos breves ya presentes en el catálogo.
INSERT INTO especificaciones_producto(id_producto,atributo,valor,unidad)
SELECT p.id_producto,s.atributo,s.valor,s.unidad
FROM productos p
JOIN (
 SELECT 'K-Office i5' modelo,'Memoria' atributo,'16 GB RAM' valor,NULL unidad UNION ALL
 SELECT 'K-Office i5','Almacenamiento','SSD',NULL UNION ALL
 SELECT 'K-Ryzen 5600G','Procesador','AMD Ryzen 5 5600G',NULL UNION ALL
 SELECT 'K-Ryzen 5600G','Memoria y almacenamiento','16 GB DDR4 · SSD NVMe 1 TB',NULL UNION ALL
 SELECT 'K-Gamer M','Perfil de uso','PC gamer de gama media',NULL UNION ALL
 SELECT 'K-Gamer X','Gráficos','Tarjeta gráfica dedicada',NULL UNION ALL
 SELECT 'K-Mini i5','Formato','Mini PC de escritorio',NULL UNION ALL
 SELECT 'Ryzen 5 5600','Núcleos e hilos','6 núcleos · 12 hilos',NULL UNION ALL
 SELECT 'i5-12400F','Componente','Procesador Intel Core i5',NULL UNION ALL
 SELECT 'RTX 4060 8GB','Memoria gráfica','8 GB',NULL UNION ALL
 SELECT 'Fury Beast 16GB','Capacidad','16 GB',NULL UNION ALL
 SELECT 'Fury Beast 16GB','Tipo','DDR4',NULL UNION ALL
 SELECT 'NV2 1TB','Capacidad','1 TB',NULL UNION ALL
 SELECT 'NV2 1TB','Formato','SSD NVMe M.2',NULL UNION ALL
 SELECT 'K552 Kumara','Tipo','Teclado mecánico compacto',NULL UNION ALL
 SELECT 'K552 Kumara','Conexión','USB',NULL UNION ALL
 SELECT 'Cloud II','Conexión de audio','PC y consola',NULL UNION ALL
 SELECT 'C920 HD Pro','Video','Full HD',NULL UNION ALL
 SELECT 'C920 HD Pro','Uso','Videollamadas',NULL UNION ALL
 SELECT 'Nano 30W','Potencia máxima','30 W',NULL UNION ALL
 SELECT 'PowerCore 20K','Capacidad','20.000 mAh',NULL UNION ALL
 SELECT 'K-USB-C1','Longitud','1 metro',NULL UNION ALL
 SELECT 'K-USB-C1','Uso','Carga y transferencia de datos',NULL UNION ALL
 SELECT 'K-Hub 6en1','Formato','Hub USB-C multipuerto',NULL UNION ALL
 SELECT 'Watch 4 Active','Funciones','Monitoreo básico',NULL UNION ALL
 SELECT 'Archer AX12','Wi-Fi','Wi-Fi 6 · doble banda',NULL UNION ALL
 SELECT 'TL-SG108','Puertos','8 puertos Gigabit',NULL UNION ALL
 SELECT 'TL-SG108','Administración','No administrable',NULL UNION ALL
 SELECT 'Archer T2U Nano','Conexión inalámbrica','Wi-Fi AC600 · doble banda',NULL UNION ALL
 SELECT 'RE305','Conexión inalámbrica','Wi-Fi AC1200 · doble banda',NULL UNION ALL
 SELECT 'GC-WBAX200','Conexión','PCIe · Gigabit · Wi-Fi',NULL UNION ALL
 SELECT 'S24R350','Pantalla','24 pulgadas Full HD',NULL UNION ALL
 SELECT 'VG248QG','Pantalla','Full HD · 144 Hz',NULL UNION ALL
 SELECT 'VA2732-H','Pantalla','27 pulgadas · panel IPS',NULL UNION ALL
 SELECT 'SJ55W','Pantalla','34 pulgadas · formato ultrawide',NULL UNION ALL
 SELECT 'K-Portable 156','Pantalla','15,6 pulgadas Full HD',NULL UNION ALL
 SELECT 'K-Portable 156','Conexión','USB-C',NULL UNION ALL
 SELECT 'SoloCast','Conexión','USB',NULL UNION ALL
 SELECT 'SoloCast','Uso','Llamadas y streaming',NULL UNION ALL
 SELECT 'Tune 520BT','Conexión','Bluetooth',NULL UNION ALL
 SELECT 'Z150','Formato','Parlantes estéreo 2.0',NULL UNION ALL
 SELECT 'Brio 100','Video','Full HD',NULL UNION ALL
 SELECT 'QuadCast S','Conexión','USB',NULL UNION ALL
 SELECT 'QuadCast S','Uso','Streaming',NULL UNION ALL
 SELECT 'Xbox Wireless Controller','Conexión','Inalámbrica',NULL UNION ALL
 SELECT 'Kumara K552 RGB','Tipo','Teclado mecánico gamer',NULL UNION ALL
 SELECT 'G203 LIGHTSYNC','Conexión','USB cableado',NULL UNION ALL
 SELECT 'Cloud Stinger 2','Tipo','Auriculares gamer con micrófono',NULL UNION ALL
 SELECT 'VG249Q1A','Pantalla','24 pulgadas · Full HD · 144 Hz',NULL UNION ALL
 SELECT 'IdeaPad 3 U-014','Estado','Usado · revisado y probado',NULL UNION ALL
 SELECT 'Inspiron U-021','Estado','Usado · desgaste cosmético visible',NULL UNION ALL
 SELECT 'K-Office R5 Refurb','Estado','Reacondicionado · revisado y probado',NULL UNION ALL
 SELECT 'S24 Used-09','Estado','Usado · revisado',NULL UNION ALL
 SELECT 'GTX 1660 U-032','Estado','Usado · probado antes de publicar',NULL
) s ON s.modelo=p.modelo
WHERE NOT EXISTS(SELECT 1 FROM especificaciones_producto e WHERE e.id_producto=p.id_producto AND e.atributo=s.atributo);
