-- Agregar campo visible_chatbot a la tabla especialistas_servicios
-- Para controlar qué servicios aparecen en el chatbot

ALTER TABLE `especialistas_servicios` 
ADD COLUMN `visible_chatbot` TINYINT(1) DEFAULT 1 
AFTER `activo`;

UPDATE `especialistas_servicios` SET `visible_chatbot` = 1 WHERE `activo` = 1;
