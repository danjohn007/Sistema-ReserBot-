-- Agregar campo source a la tabla reservaciones
-- Permite rastrear desde qué liga de WhatsApp (o canal) llegó la reserva
-- Valores esperados: 'liga1', 'liga2', 'liga3', 'web', 'chatbot', 'manual', etc.

ALTER TABLE reservaciones 
    ADD COLUMN source VARCHAR(50) NULL DEFAULT NULL COMMENT 'Origen de la reserva: liga1, liga2, liga3, web, chatbot, manual, etc.'
    AFTER notas;
