-- Permite incluir varias sucursales dentro de una misma solicitud de registro.
-- Ejecutar una sola vez despues de sql/add_solicitudes_registro.sql.

CREATE TABLE `solicitudes_registro_sucursales` (
    `solicitud_id` INT NOT NULL,
    `sucursal_id` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`solicitud_id`, `sucursal_id`),
    UNIQUE KEY `uk_registro_sucursal` (`sucursal_id`),
    KEY `idx_registro_sucursal_solicitud` (`solicitud_id`),
    CONSTRAINT `registro_sucursales_solicitud_fk`
        FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_registro` (`id`) ON DELETE CASCADE,
    CONSTRAINT `registro_sucursales_sucursal_fk`
        FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conserva las solicitudes existentes como solicitudes de una sola sucursal.
INSERT IGNORE INTO `solicitudes_registro_sucursales` (`solicitud_id`, `sucursal_id`)
SELECT `id`, `sucursal_id`
FROM `solicitudes_registro`;
