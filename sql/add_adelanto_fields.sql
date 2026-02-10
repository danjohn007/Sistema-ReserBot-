-- Agregar campos para porcentaje de adelanto GLOBAL por especialista
-- Se agrega a la tabla usuarios porque un usuario puede tener múltiples registros 
-- en especialistas (uno por sucursal), y estos campos deben aplicar globalmente

ALTER TABLE `usuarios` 
ADD COLUMN `requiere_adelanto` TINYINT(1) DEFAULT 0 COMMENT '0=desactivado, 1=activado' AFTER `activo`,
ADD COLUMN `porcentaje_adelanto` INT DEFAULT 0 COMMENT 'Porcentaje de adelanto requerido (0-100)' AFTER `requiere_adelanto`;

-- Índice para consultas rápidas de especialistas con adelanto
ALTER TABLE `usuarios`
ADD KEY `idx_adelanto` (`requiere_adelanto`);
