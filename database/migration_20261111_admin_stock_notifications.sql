CREATE TABLE IF NOT EXISTS notificaciones_administrador(
  id_notificacion BIGINT AUTO_INCREMENT PRIMARY KEY,
  id_producto INT NULL,
  id_pedido INT NULL,
  titulo VARCHAR(180) NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  leida TINYINT NOT NULL DEFAULT 0,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notificacion_leida_fecha(leida,fecha_creacion),
  FOREIGN KEY(id_producto) REFERENCES productos(id_producto) ON DELETE SET NULL,
  FOREIGN KEY(id_pedido) REFERENCES pedidos(id_pedido) ON DELETE SET NULL
) ENGINE=InnoDB;
