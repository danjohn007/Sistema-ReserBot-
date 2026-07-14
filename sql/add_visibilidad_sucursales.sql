-- Permite ocultar sucursales solo en el listado administrativo del Superadmin.
-- No modifica el estado activo ni la disponibilidad operativa de la sucursal.

ALTER TABLE `sucursales`
    ADD COLUMN `oculta_superadmin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `autorizado`,
    ADD KEY `idx_oculta_superadmin` (`oculta_superadmin`);
