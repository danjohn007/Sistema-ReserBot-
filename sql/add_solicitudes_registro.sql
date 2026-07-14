-- Solicitudes conjuntas de sucursal y profesionistas.
-- Ejecutar una sola vez antes de publicar el codigo PHP de esta funcionalidad.

ALTER TABLE `sucursales`
    ADD COLUMN `autorizado` TINYINT(1) NOT NULL DEFAULT 1 AFTER `activo`,
    ADD KEY `idx_autorizado` (`autorizado`);

ALTER TABLE `especialistas`
    ADD COLUMN `autorizado` TINYINT(1) NOT NULL DEFAULT 1 AFTER `activo`,
    ADD KEY `idx_autorizado` (`autorizado`);

CREATE TABLE `solicitudes_registro` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `sucursal_id` INT NOT NULL,
    `creado_por` INT NOT NULL,
    `estado` ENUM('pendiente', 'aprobada', 'rechazada') NOT NULL DEFAULT 'pendiente',
    `motivo_rechazo` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `revisado_por` INT DEFAULT NULL,
    `fecha_revision` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_solicitud_sucursal` (`sucursal_id`),
    KEY `idx_solicitud_estado` (`estado`),
    KEY `idx_solicitud_creado_por` (`creado_por`),
    KEY `idx_solicitud_revisado_por` (`revisado_por`),
    CONSTRAINT `solicitudes_registro_sucursal_fk`
        FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE,
    CONSTRAINT `solicitudes_registro_creado_por_fk`
        FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `solicitudes_registro_revisado_por_fk`
        FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La contrasena inicial se guarda como SHA-256 y el sistema la convierte a
-- password_hash() automaticamente durante el primer inicio de sesion.
INSERT INTO `usuarios`
    (`nombre`, `apellidos`, `email`, `telefono`, `password`, `rol_id`, `sucursal_id`, `email_verificado`, `activo`)
VALUES
    ('Registro', 'AIDE', 'registro@aide.com', NULL,
     'f2d19a0c16e2635450cd6ecb6e8598dd4359f51ab736e8961343911a7e53d907',
     6, NULL, 1, 1)
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `apellidos` = VALUES(`apellidos`),
    `password` = VALUES(`password`),
    `rol_id` = VALUES(`rol_id`),
    `sucursal_id` = NULL,
    `email_verificado` = 1,
    `activo` = 1;
