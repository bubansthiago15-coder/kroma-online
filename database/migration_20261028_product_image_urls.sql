USE kroma;

-- Product image URLs returned by image providers can exceed 255 characters.
ALTER TABLE imagenes_producto
  MODIFY COLUMN ruta_imagen VARCHAR(2048) NOT NULL;
