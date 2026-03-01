-- Agregar campos telefono y correo a la tabla reservaciones
-- Para permitir capturar información de contacto en reservas manuales

ALTER TABLE `reservaciones` 
ADD COLUMN `telefono` VARCHAR(20) DEFAULT NULL 
COMMENT 'Teléfono del cliente para esta reservación' 
AFTER `nombre_cliente`;

ALTER TABLE `reservaciones` 
ADD COLUMN `correo` VARCHAR(150) DEFAULT NULL 
AFTER `telefono`;
