-- Asigna las nuevas fotografías locales a productos existentes compatibles.
-- Se usan rutas locales para que las imágenes no dependan de servicios externos.
UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/laptop gamer.webp',
    ip.texto_alternativo='Notebook gamer, imagen de referencia'
WHERE p.modelo='FX507ZC4' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/pc de escritorio lenovo.webp',
    ip.texto_alternativo='Mini PC Lenovo ThinkCentre, imagen de referencia'
WHERE p.modelo='K-Mini i5' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/pc de escritorio lenovo 2.webp',
    ip.texto_alternativo='PC de oficina con monitor y periféricos, imagen de referencia'
WHERE p.modelo='K-Office i5' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/pc de escritorio fravega.webp',
    ip.texto_alternativo='PC de escritorio con monitor y periféricos, imagen de referencia'
WHERE p.modelo='K-Ryzen 5600G' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/mouse gamer.webp',
    ip.texto_alternativo='Mouse gamer Logitech, imagen de referencia'
WHERE p.modelo='G203 LIGHTSYNC' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/cable usb-c.webp',
    ip.texto_alternativo='Cable USB-C, imagen de referencia'
WHERE p.modelo='K-USB-C1' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/hub.webp',
    ip.texto_alternativo='Hub USB-C multipuerto, imagen de referencia'
WHERE p.modelo='K-Hub 6en1' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/router.webp',
    ip.texto_alternativo='Router Wi-Fi TP-Link, imagen de referencia'
WHERE p.modelo='Archer AX12' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/repetidor.webp',
    ip.texto_alternativo='Repetidor Wi-Fi TP-Link, imagen de referencia'
WHERE p.modelo='RE305' AND ip.es_principal=1;

UPDATE imagenes_producto ip
JOIN productos p USING(id_producto)
SET ip.ruta_imagen='assets/images/uploads/adaptador wifi.webp',
    ip.texto_alternativo='Adaptador Wi-Fi USB TP-Link Archer T2U Nano, imagen de referencia'
WHERE p.modelo='Archer T2U Nano' AND ip.es_principal=1;
