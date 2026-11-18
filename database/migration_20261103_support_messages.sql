USE kroma;

CREATE TABLE IF NOT EXISTS consultas_soporte (
    id_consulta BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    tipo VARCHAR(60) NOT NULL,
    asunto VARCHAR(180) NOT NULL,
    mensaje TEXT NOT NULL,
    estado ENUM('nuevo','en_revision','resuelto') NOT NULL DEFAULT 'nuevo',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_soporte_estado_fecha (estado, fecha_creacion),
    INDEX idx_soporte_email (email),
    CONSTRAINT fk_soporte_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
