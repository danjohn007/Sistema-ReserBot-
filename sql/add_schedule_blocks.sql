-- Script para agregar funcionalidad de bloqueo de horarios e intervalo de espacios
-- Fecha: 15 de diciembre de 2025

-- Agregar columnas a la tabla horarios_especialistas
ALTER TABLE `horarios_especialistas`
ADD COLUMN `intervalo_espacios` INT DEFAULT 60 COMMENT 'Intervalo en minutos para separar las citas (30 o 60)' AFTER `activo`,
ADD COLUMN `hora_inicio_bloqueo` TIME NULL COMMENT 'Hora de inicio del bloqueo dentro del día' AFTER `intervalo_espacios`,
ADD COLUMN `hora_fin_bloqueo` TIME NULL COMMENT 'Hora de fin del bloqueo dentro del día' AFTER `hora_inicio_bloqueo`,
ADD COLUMN `bloqueo_activo` TINYINT(1) DEFAULT 0 COMMENT '1 si el bloqueo está activo para este día' AFTER `hora_fin_bloqueo`;

-- Actualizar registros existentes con valores por defecto
UPDATE `horarios_especialistas` 
SET `intervalo_espacios` = 60, 
    `bloqueo_activo` = 0;
