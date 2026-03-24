-- Agregar nombres personalizables para las ligas de WhatsApp de cada especialista
ALTER TABLE especialistas
    ADD COLUMN nombre_liga1 VARCHAR(60) NULL DEFAULT NULL COMMENT 'Nombre personalizado para Liga 1',
    ADD COLUMN nombre_liga2 VARCHAR(60) NULL DEFAULT NULL COMMENT 'Nombre personalizado para Liga 2',
    ADD COLUMN nombre_liga3 VARCHAR(60) NULL DEFAULT NULL COMMENT 'Nombre personalizado para Liga 3';
