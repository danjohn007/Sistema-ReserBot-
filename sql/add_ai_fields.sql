-- Migración: Agregar campos de configuración de IA en tabla usuarios
-- Ejecutar una sola vez en producción

ALTER TABLE `usuarios`
    ADD COLUMN `ai_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Chatbot IA activo para este especialista',
    ADD COLUMN `ai_contexto` TEXT NULL COMMENT 'Contexto/instrucciones del chatbot IA (máx. 5000 chars)';
