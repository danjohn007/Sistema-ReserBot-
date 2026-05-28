-- =====================================================================
-- ReserBot - Recordatorios automáticos por WhatsApp
-- Migración: crea las tablas necesarias para configurar y auditar el
-- envío de recordatorios por especialista.
-- =====================================================================

-- Tabla de configuración por especialista
CREATE TABLE IF NOT EXISTS `reminder_configs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `especialista_id` INT NOT NULL COMMENT 'FK -> usuarios.id (rol especialista)',
    `enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = activo, 0 = desactivado',
    `hours_before` INT NOT NULL DEFAULT 3 COMMENT 'Horas de anticipación (1-24)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_especialista` (`especialista_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de auditoría: evita reenvíos para una misma fecha/especialista
CREATE TABLE IF NOT EXISTS `reminder_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `especialista_id` INT NOT NULL,
    `target_date` DATE NOT NULL COMMENT 'Fecha de las citas a las que se envió recordatorio',
    `sent_count` INT NOT NULL DEFAULT 0 COMMENT 'Cuántos recordatorios se enviaron con éxito',
    `total_count` INT NOT NULL DEFAULT 0 COMMENT 'Cuántas citas se intentaron',
    `error_message` TEXT NULL,
    `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_esp_fecha` (`especialista_id`, `target_date`),
    KEY `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: la columna `recordatorio_enviado` en `reservaciones` ya existe y se
-- reutiliza como bandera por cita (1 = ya se envió, 0 = pendiente).
