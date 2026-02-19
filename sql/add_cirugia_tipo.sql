-- =====================================================
-- Agregar tipo 'cirugia' al ENUM de bloqueos_horario
-- Para programar cirugías desde el calendario
-- =====================================================

-- Modificar tipo ENUM para incluir 'cirugia'
ALTER TABLE bloqueos_horario 
MODIFY tipo ENUM('vacaciones', 'pausa', 'personal', 'puntual', 'cirugia', 'otro') DEFAULT 'otro';

-- Nota: 
-- - tipo 'cirugia' = procedimiento quirúrgico programado
-- - El campo 'motivo' se usa para almacenar los asistentes de la cirugía
-- - Bloquea el calendario del especialista durante el tiempo especificado
