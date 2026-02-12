-- =====================================================
-- Agregar sucursal_id a bloqueos_horario
-- Para bloqueos puntuales desde el calendario
-- =====================================================

-- Agregar columna sucursal_id
ALTER TABLE bloqueos_horario 
ADD COLUMN sucursal_id INT DEFAULT NULL AFTER especialista_id,
ADD INDEX idx_sucursal (sucursal_id),
ADD FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE;

-- Modificar tipo ENUM para incluir 'puntual'
ALTER TABLE bloqueos_horario 
MODIFY tipo ENUM('vacaciones', 'pausa', 'personal', 'puntual', 'otro') DEFAULT 'otro';

-- Nota: 
-- - sucursal_id NULL = aplica a todas las sucursales del especialista
-- - tipo 'puntual' = bloqueo específico desde el calendario (ej: reunión, cita personal)
-- - fecha_inicio/fecha_fin = formato DATETIME para día y hora específicos
