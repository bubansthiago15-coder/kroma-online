-- Vincula todas las fotografías nuevas a productos existentes de su categoría.
-- Las fotos que no son del SKU exacto se describen como ilustrativas.
UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/teclado y mouse.webp',ip.texto_alternativo='Teclado y mouse inalámbricos, imagen ilustrativa'
WHERE p.modelo='Kumara K552 RGB' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/teclado magnetico.webp',ip.texto_alternativo='Teclado mecánico compacto, imagen ilustrativa'
WHERE p.modelo='K552 Kumara' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/placa madre.webp',ip.texto_alternativo='Placa madre para PC, imagen ilustrativa de componente'
WHERE p.modelo='K-Gamer M' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/monitor xiaomi.webp',ip.texto_alternativo='Monitor Xiaomi, imagen ilustrativa de monitor'
WHERE p.modelo='VA2732-H' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/monitor hp.webp',ip.texto_alternativo='Monitor HP, imagen ilustrativa de monitor'
WHERE p.modelo='S24R350' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/monitor curvo.webp',ip.texto_alternativo='Monitor curvo gamer, imagen ilustrativa'
WHERE p.modelo='VG248QG' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/kit gamer.webp',ip.texto_alternativo='Kit gamer de teclado y mouse, imagen ilustrativa'
WHERE p.modelo='K-Gamer X' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/joystick g7 pro.webp',ip.texto_alternativo='Control inalámbrico Gamesir, imagen ilustrativa de joystick'
WHERE p.modelo='Xbox Wireless Controller' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/intel i7.webp',ip.texto_alternativo='Procesador Intel Core i7, imagen ilustrativa de procesador'
WHERE p.modelo='i5-12400F' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/cable vga.webp',ip.texto_alternativo='Cable VGA, imagen ilustrativa de conexión'
WHERE p.modelo='K-Portable 156' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/auriculares xiaomi.webp',ip.texto_alternativo='Auriculares Xiaomi inalámbricos, imagen ilustrativa de audio'
WHERE p.modelo='Tune 520BT' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/auriculares gamer red dragon.webp',ip.texto_alternativo='Auriculares gamer Redragon, imagen ilustrativa'
WHERE p.modelo='Cloud Stinger 2' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/amd ryzen 9.webp',ip.texto_alternativo='Procesador AMD Ryzen 9, imagen ilustrativa de procesador'
WHERE p.modelo='Ryzen 5 5600' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/amd ryzen 7.webp',ip.texto_alternativo='Procesador AMD Ryzen 7, imagen ilustrativa de procesador'
WHERE p.modelo='K-Ryzen 5600G' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/pc de escritorio fravega.webp',ip.texto_alternativo='PC de escritorio con monitor y periféricos, imagen ilustrativa'
WHERE p.modelo='K-Gamer M' AND ip.es_principal=1;

UPDATE imagenes_producto ip JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/placa madre.webp',ip.texto_alternativo='Placa madre instalada en PC, imagen ilustrativa'
WHERE p.modelo='K-Office R5 Refurb' AND ip.es_principal=1;
