-- Agregar campo primera_consulta a la tabla reservaciones
-- Este campo indica si es la primera consulta del paciente con el especialista
-- Fecha: 2026-03-06

ALTER TABLE `reservaciones` 
ADD COLUMN `primera_consulta` TINYINT(1) DEFAULT 0 
COMMENT 'Indica si es la primera consulta del paciente (0=No, 1=Sí)'
AFTER `es_extraordinaria`;

-- Opcional: Establecer valor por defecto para registros existentes
UPDATE `reservaciones` SET `primera_consulta` = 0 WHERE `primera_consulta` IS NULL;
