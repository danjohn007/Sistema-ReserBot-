-- Agregar campo color a la tabla sucursales
-- Para permitir colores personalizados en el calendario

ALTER TABLE `sucursales` 
ADD COLUMN `color` VARCHAR(7) DEFAULT '#3B82F6'

-- Actualizar sucursales existentes con colores predeterminados
UPDATE `sucursales` SET `color` = '#3B82F6' WHERE `id` = 1; -- Azul
UPDATE `sucursales` SET `color` = '#EC4899' WHERE `id` = 2; -- Rosa
UPDATE `sucursales` SET `color` = '#10B981' WHERE `id` = 3; -- Verde
UPDATE `sucursales` SET `color` = '#F59E0B' WHERE `id` = 4; -- Naranja
UPDATE `sucursales` SET `color` = '#8B5CF6' WHERE `id` = 5; -- Púrpura
UPDATE `sucursales` SET `color` = '#EF4444' WHERE `id` = 6; -- Rojo
