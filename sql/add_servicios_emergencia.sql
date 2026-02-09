-- =====================================================
-- AGREGAR SERVICIOS DE EMERGENCIA
-- Permite marcar servicios que solo están disponibles en horarios de emergencia
-- =====================================================

-- Agregar columna a la tabla especialistas_servicios
-- Esta tabla maneja las personalizaciones del especialista para cada servicio
ALTER TABLE `especialistas_servicios`
ADD COLUMN `es_emergencia` TINYINT(1) DEFAULT 0 
    COMMENT '1 si el servicio solo está disponible en horarios de emergencia, 0 si está disponible en horarios normales' 
    AFTER `activo`;

-- Actualizar registros existentes (todos los servicios actuales son de horario normal)
UPDATE `especialistas_servicios` 
SET `es_emergencia` = 0;

-- Crear índice para optimizar búsquedas
ALTER TABLE `especialistas_servicios`
ADD INDEX `idx_es_emergencia` (`es_emergencia`);

-- Consulta de verificación
-- SELECT es.id, e.usuario_id, s.nombre as servicio, es.activo, es.es_emergencia
-- FROM especialistas_servicios es
-- JOIN especialistas e ON es.especialista_id = e.id
-- JOIN servicios s ON es.servicio_id = s.id
-- ORDER BY e.usuario_id, es.es_emergencia, s.nombre;
