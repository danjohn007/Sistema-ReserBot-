-- =====================================================
-- AGREGAR HORARIOS DE EMERGENCIA
-- Sistema para extender disponibilidad fuera del horario normal
-- =====================================================

-- Agregar columnas a la tabla horarios_especialistas
ALTER TABLE `horarios_especialistas`
ADD COLUMN `hora_inicio_emergencia` TIME NULL 
    COMMENT 'Hora de inicio del horario de emergencia (fuera del horario normal)' 
    AFTER `bloqueo_activo`,
ADD COLUMN `hora_fin_emergencia` TIME NULL 
    COMMENT 'Hora de fin del horario de emergencia (fuera del horario normal)' 
    AFTER `hora_inicio_emergencia`,
ADD COLUMN `emergencia_activa` TINYINT(1) DEFAULT 0 
    COMMENT '1 si el horario de emergencia está activo para este día' 
    AFTER `hora_fin_emergencia`;

-- Actualizar registros existentes
UPDATE `horarios_especialistas` 
SET `emergencia_activa` = 0;

-- Consulta de verificación
-- SELECT dia_semana, hora_inicio, hora_fin, 
--        hora_inicio_bloqueo, hora_fin_bloqueo, bloqueo_activo,
--        hora_inicio_emergencia, hora_fin_emergencia, emergencia_activa
-- FROM horarios_especialistas WHERE especialista_id = 1;
