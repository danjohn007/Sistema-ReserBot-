-- Agregar campo nombre_cliente a la tabla reservaciones
-- Para permitir que especialistas registren reservas con nombres en lugar de clientes registrados

ALTER TABLE `reservaciones` 
ADD COLUMN `nombre_cliente` VARCHAR(255) DEFAULT NULL 
COMMENT 'Nombre del cliente cuando no está registrado en el sistema' 
AFTER `cliente_id`;

-- Actualizar la restricción de cliente_id para permitir NULL
ALTER TABLE `reservaciones` 
MODIFY COLUMN `cliente_id` INT DEFAULT NULL;
