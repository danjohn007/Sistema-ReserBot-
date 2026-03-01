-- Agregar campo es_extraordinaria a la tabla reservaciones
-- Para permitir citas sin validación de disponibilidad (walk-ins, pacientes no presentados)

ALTER TABLE `reservaciones` 
ADD COLUMN `es_extraordinaria` TINYINT(1) DEFAULT 0 
AFTER `estado`;

-- Actualizar reservaciones existentes como normales
UPDATE `reservaciones` SET `es_extraordinaria` = 0;
