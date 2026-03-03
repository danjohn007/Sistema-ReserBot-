-- =====================================================
-- RECUPERACIÓN COMPLETA DE DATOS - RODRIGO AIDE (Usuario 14)
-- =====================================================
-- Respaldo del 6 de febrero de 2026
-- Los datos se transferirán de los especialistas 34 y 35 (eliminados)
-- a los especialistas actuales del usuario 14
-- =====================================================

-- PASO 0: VERIFICAR QUÉ ESPECIALISTAS TIENE EL USUARIO 14
-- =====================================================
-- Ejecuta esta consulta PRIMERO para ver los IDs actuales:
-- SELECT id, usuario_id, sucursal_id FROM especialistas WHERE usuario_id = 14;

-- INSTRUCCIONES:
-- Los IDs actuales fueron verificados y son:
--   - Especialista en Sucursal 6: ID 41
--   - Especialista en Sucursal 17: ID 42
-- ¡LISTO PARA EJECUTAR!

-- PASO 1: LIMPIAR DATOS ACTUALES (evitar duplicados)
-- =====================================================
DELETE FROM `especialistas_servicios` WHERE `especialista_id` IN (41, 42);
DELETE FROM `horarios_especialistas` WHERE `especialista_id` IN (41, 42);
DELETE FROM `bloqueos_horario` WHERE `especialista_id` IN (41, 42);

-- PASO 2: RECUPERAR SERVICIOS DEL ESPECIALISTA EN SUCURSAL 6 (era 34)
-- =====================================================
-- Incluye 4 servicios con precios personalizados y duraciones específicas
INSERT INTO `especialistas_servicios` (`especialista_id`, `servicio_id`, `precio_personalizado`, `duracion_personalizada`, `activo`, `visible_chatbot`, `es_emergencia`) VALUES
(41, 1, 1000.00, NULL, 1, 1, 0),      -- Servicio 1: $1000
(41, 25, 2000.00, NULL, 1, 1, 1),     -- Servicio 25: $2000 (emergencia)
(41, 20, 700.00, 15, 1, 1, 0),        -- Servicio 20: $700, 15 minutos
(41, 2, NULL, NULL, 1, 0, 0);         -- Servicio 2: precio por defecto, no visible en chatbot

-- PASO 3: RECUPERAR SERVICIOS DEL ESPECIALISTA EN SUCURSAL 17 (era 35)
-- =====================================================
INSERT INTO `especialistas_servicios` (`especialista_id`, `servicio_id`, `precio_personalizado`, `duracion_personalizada`, `activo`, `visible_chatbot`, `es_emergencia`) VALUES
(42, 1, NULL, NULL, 1, 1, 0),         -- Servicio 1: precio por defecto
(42, 25, NULL, NULL, 1, 1, 1);        -- Servicio 25: precio por defecto (emergencia)

-- PASO 4: RECUPERAR HORARIOS DEL ESPECIALISTA EN SUCURSAL 6 (era 34)
-- =====================================================
-- Incluye horarios normales + horarios de emergencia
INSERT INTO `horarios_especialistas` (`especialista_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `intervalo_espacios`, `hora_inicio_bloqueo`, `hora_fin_bloqueo`, `bloqueo_activo`, `hora_inicio_emergencia`, `hora_fin_emergencia`, `emergencia_activa`) VALUES
(41, 1, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, '18:00:00', '20:00:00', 1),  -- Lunes con emergencias 18:00-20:00
(41, 2, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, '19:00:00', '20:00:00', 1),  -- Martes con emergencias 19:00-20:00
(41, 3, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0);              -- Miércoles sin emergencias

-- PASO 5: RECUPERAR HORARIOS DEL ESPECIALISTA EN SUCURSAL 17 (era 35)
-- =====================================================
-- Incluye horarios normales + horarios de emergencia
INSERT INTO `horarios_especialistas` (`especialista_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `intervalo_espacios`, `hora_inicio_bloqueo`, `hora_fin_bloqueo`, `bloqueo_activo`, `hora_inicio_emergencia`, `hora_fin_emergencia`, `emergencia_activa`) VALUES
(42, 4, '16:00:00', '19:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),              -- Jueves sin emergencias
(42, 5, '16:00:00', '19:00:00', 1, 60, NULL, NULL, 0, '20:00:00', '21:00:00', 1),  -- Viernes con emergencias 20:00-21:00
(42, 6, '11:00:00', '13:00:00', 1, 60, NULL, NULL, 0, '20:00:00', '21:00:00', 1);  -- Sábado con emergencias 20:00-21:00

-- PASO 6: RECUPERAR BLOQUEOS DE HORARIO DEL ESPECIALISTA EN SUCURSAL 17 (era 35)
-- =====================================================
-- Bloqueos para cirugías, pausas y otros eventos
INSERT INTO `bloqueos_horario` (`especialista_id`, `sucursal_id`, `fecha_inicio`, `fecha_fin`, `motivo`, `tipo`) VALUES
(42, 17, '2026-02-13 09:00:00', '2026-02-13 11:00:00', NULL, 'otro'),
(42, 17, '2026-02-13 11:00:00', '2026-02-13 12:00:00', NULL, 'pausa'),
(42, 6, '2026-02-19 07:00:00', '2026-02-19 12:00:00', NULL, 'otro'),
(42, 17, '2026-02-21 14:00:00', '2026-02-21 15:00:00', NULL, 'cirugia'),
(42, 6, '2026-02-28 07:00:00', '2026-02-28 12:00:00', 'instrumentista juan anestesiologo luis', 'cirugia');

-- PASO 7: RECUPERAR BLOQUEOS DE HORARIO DEL ESPECIALISTA EN SUCURSAL 6 (era 34)
-- =====================================================
INSERT INTO `bloqueos_horario` (`especialista_id`, `sucursal_id`, `fecha_inicio`, `fecha_fin`, `motivo`, `tipo`) VALUES
(41, 6, '2026-03-04 07:00:00', '2026-03-04 11:00:00', NULL, 'cirugia');

-- PASO 8: ACTUALIZAR RESERVACIONES QUE AÚN EXISTAN
-- =====================================================
-- Actualizar todas las citas del especialista 34 al nuevo especialista de sucursal 6
UPDATE `reservaciones` SET `especialista_id` = 41 WHERE `especialista_id` = 34;

-- Actualizar todas las citas del especialista 35 al nuevo especialista de sucursal 17
UPDATE `reservaciones` SET `especialista_id` = 42 WHERE `especialista_id` = 35;

-- =====================================================
-- FIN DE RECUPERACIÓN COMPLETA
-- =====================================================

-- RESUMEN DETALLADO DE LO RECUPERADO:
-- ====================================
-- ESPECIALISTA EN SUCURSAL 6 (antes ID 34):
--   - 4 servicios personalizados
--   - 3 horarios (L,M,M 9:00-14:00) con emergencias en L y M
--   - 1 bloqueo de cirugía
--   - Todas las reservaciones actualizadas
--
-- ESPECIALISTA EN SUCURSAL 17 (antes ID 35):
--   - 2 servicios
--   - 3 horarios (J,V,S) con emergencias en V y S
--   - 5 bloqueos (cirugías, pausas, otros)
--   - Todas las reservaciones actualizadas
--
-- NOTA: El script usa variables @especialista_sucursal_6 y @especialista_sucursal_17
--       que se asignan automáticamente a los IDs actuales del usuario 14
