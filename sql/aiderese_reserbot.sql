-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 10-02-2026 a las 10:29:17
-- Versión del servidor: 5.7.23-23
-- Versión de PHP: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aiderese_reserbot`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueos_horario`
--

CREATE TABLE `bloqueos_horario` (
  `id` int(11) NOT NULL,
  `especialista_id` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('vacaciones','pausa','personal','otro') COLLATE utf8mb4_unicode_ci DEFAULT 'otro',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_servicios`
--

CREATE TABLE `categorias_servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-concierge-bell',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `orden` int(11) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_servicios`
--

INSERT INTO `categorias_servicios` (`id`, `nombre`, `descripcion`, `icono`, `color`, `orden`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Medicina General', 'Consultas médicas generales y especialidades', 'fas fa-stethoscope', '#EF4444', 1, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(2, 'Belleza y Estética', 'Servicios de belleza, spa y estética', 'fas fa-spa', '#EC4899', 2, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(3, 'Barbería', 'Cortes de cabello, barba y tratamientos capilares', 'fas fa-cut', '#8B5CF6', 3, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(4, 'Asesoría Legal', 'Consultoría y asesoría jurídica', 'fas fa-balance-scale', '#6366F1', 4, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(5, 'Consultoría Financiera', 'Asesoría en finanzas e inversiones', 'fas fa-chart-line', '#10B981', 5, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(6, 'Psicología', 'Terapia y consulta psicológica', 'fas fa-brain', '#F59E0B', 6, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(7, 'Categoria Ejemplo', 'Descripción ejemplo', 'fas fa-concierge-bell', '#009942', 6, 1, '2025-12-02 17:39:26', '2025-12-02 17:39:44'),
(8, 'Otros', 'Otros servicios', 'fas fa-ellipsis-h', '#9CA3AF', 999, 1, '2026-01-27 16:21:42', '2026-01-27 16:21:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('text','number','boolean','json','color','image') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `tipo`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'nombre_sitio', 'AIDE Reservaciones', 'text', 'Nombre del sistema', '2025-11-29 03:20:01', '2025-12-04 15:26:58'),
(2, 'logotipo', NULL, 'image', 'Logotipo del sistema', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(3, 'email_sistema', 'contacto@reserbot.com', 'text', 'Correo principal del sistema', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(4, 'telefono_contacto', '+52 442 123 4567', 'text', 'Teléfono de contacto', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(5, 'horario_atencion', 'Lunes a Viernes 8:00 - 20:00', 'text', 'Horario de atención', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(6, 'color_primario', '#3B82F6', 'color', 'Color primario del sistema', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(7, 'color_secundario', '#1E40AF', 'color', 'Color secundario del sistema', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(8, 'color_acento', '#10B981', 'color', 'Color de acento', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(9, 'paypal_client_id', '', 'text', 'Client ID de PayPal', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(10, 'paypal_secret', '', 'text', 'Secret de PayPal', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(11, 'paypal_modo', 'sandbox', 'text', 'Modo de PayPal (sandbox/live)', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(12, 'qr_api_url', '', 'text', 'URL de API para QRs masivos', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(13, 'confirmacion_automatica', '0', 'boolean', 'Confirmar citas automáticamente', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(14, 'recordatorio_24h', '1', 'boolean', 'Enviar recordatorio 24h antes', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(15, 'recordatorio_1h', '1', 'boolean', 'Enviar recordatorio 1h antes', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(16, 'permitir_cancelacion_cliente', '1', 'boolean', 'Permitir que clientes cancelen citas', '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(17, 'horas_anticipacion_cancelacion', '24', 'number', 'Horas mínimas de anticipación para cancelar', '2025-11-29 03:20:01', '2025-11-29 03:20:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_feriados`
--

CREATE TABLE `dias_feriados` (
  `id` int(11) NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL COMMENT 'NULL = aplica a todas',
  `fecha` date NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurrente` tinyint(1) DEFAULT '0' COMMENT 'Se repite cada año',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dias_feriados`
--

INSERT INTO `dias_feriados` (`id`, `sucursal_id`, `fecha`, `nombre`, `recurrente`, `created_at`) VALUES
(1, NULL, '2024-01-01', 'Año Nuevo', 1, '2025-11-29 03:20:01'),
(2, NULL, '2024-02-05', 'Día de la Constitución', 1, '2025-11-29 03:20:01'),
(3, NULL, '2024-03-18', 'Natalicio de Benito Juárez', 1, '2025-11-29 03:20:01'),
(4, NULL, '2024-05-01', 'Día del Trabajo', 1, '2025-11-29 03:20:01'),
(5, NULL, '2024-09-16', 'Día de la Independencia', 1, '2025-11-29 03:20:01'),
(6, NULL, '2024-11-18', 'Revolución Mexicana', 1, '2025-11-29 03:20:01'),
(7, NULL, '2024-12-25', 'Navidad', 1, '2025-11-29 03:20:01'),
(8, NULL, '2024-09-25', 'Aniversario de la Fundación de Querétaro', 1, '2025-11-29 03:20:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialistas`
--

CREATE TABLE `especialistas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `profesion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `especialidad` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `experiencia_anos` int(11) DEFAULT '0',
  `tarifa_base` decimal(10,2) DEFAULT '0.00',
  `duracion_cita_default` int(11) DEFAULT '30' COMMENT 'minutos',
  `calificacion_promedio` decimal(3,2) DEFAULT '0.00',
  `total_resenas` int(11) DEFAULT '0',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `especialistas`
--

INSERT INTO `especialistas` (`id`, `usuario_id`, `sucursal_id`, `profesion`, `especialidad`, `descripcion`, `experiencia_anos`, `tarifa_base`, `duracion_cita_default`, `calificacion_promedio`, `total_resenas`, `foto`, `activo`, `created_at`, `updated_at`) VALUES
(18, 20, 16, 'Fraccionamiento', '', '', 1, 0.00, 30, 0.00, 0, NULL, 1, '2026-01-28 18:41:39', '2026-01-28 18:41:39'),
(26, 5, 11, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(27, 5, 7, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(28, 5, 1, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(29, 5, 3, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(30, 5, 2, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(31, 5, 9, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(32, 5, 12, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(33, 5, 13, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 30, 0.00, 0, NULL, 1, '2026-01-30 19:07:17', '2026-01-30 19:07:17'),
(34, 14, 6, 'Médico', 'Otorrinolaringologo', 'Consultas', 13, 1200.00, 30, 0.00, 0, NULL, 1, '2026-02-03 19:59:53', '2026-02-03 19:59:53'),
(35, 14, 17, 'Médico', 'Otorrinolaringologo', 'Consultas', 13, 1200.00, 30, 0.00, 0, NULL, 1, '2026-02-03 19:59:53', '2026-02-03 19:59:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialistas_servicios`
--

CREATE TABLE `especialistas_servicios` (
  `id` int(11) NOT NULL,
  `especialista_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `precio_personalizado` decimal(10,2) DEFAULT NULL,
  `duracion_personalizada` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `es_emergencia` tinyint(1) DEFAULT '0' COMMENT '1 si el servicio solo está disponible en horarios de emergencia, 0 si está disponible en horarios normales'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `especialistas_servicios`
--

INSERT INTO `especialistas_servicios` (`id`, `especialista_id`, `servicio_id`, `precio_personalizado`, `duracion_personalizada`, `activo`, `es_emergencia`) VALUES
(63, 18, 24, NULL, 60, 1, 0),
(78, 26, 10, NULL, NULL, 1, 0),
(79, 26, 11, NULL, NULL, 1, 0),
(80, 27, 10, NULL, NULL, 1, 0),
(81, 27, 11, NULL, NULL, 1, 0),
(82, 28, 10, NULL, NULL, 1, 0),
(83, 28, 11, NULL, NULL, 1, 0),
(84, 29, 10, NULL, NULL, 1, 0),
(85, 29, 11, NULL, NULL, 1, 0),
(86, 30, 10, NULL, NULL, 1, 0),
(87, 30, 11, NULL, NULL, 1, 0),
(88, 31, 10, NULL, NULL, 1, 0),
(89, 31, 11, NULL, NULL, 1, 0),
(90, 32, 10, NULL, NULL, 1, 0),
(91, 32, 11, NULL, NULL, 1, 0),
(92, 33, 10, NULL, NULL, 1, 0),
(93, 33, 11, NULL, NULL, 1, 0),
(94, 34, 1, 1000.00, NULL, 1, 0),
(95, 34, 25, 2000.00, NULL, 1, 1),
(96, 35, 1, NULL, NULL, 1, 0),
(97, 35, 25, NULL, NULL, 1, 0),
(98, 34, 20, 700.00, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_especialistas`
--

CREATE TABLE `horarios_especialistas` (
  `id` int(11) NOT NULL,
  `especialista_id` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '1=Lunes, 7=Domingo',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `intervalo_espacios` int(11) DEFAULT '60' COMMENT 'Intervalo en minutos para separar las citas (30 o 60)',
  `hora_inicio_bloqueo` time DEFAULT NULL COMMENT 'Hora de inicio del bloqueo dentro del día',
  `hora_fin_bloqueo` time DEFAULT NULL COMMENT 'Hora de fin del bloqueo dentro del día',
  `bloqueo_activo` tinyint(1) DEFAULT '0' COMMENT '1 si el bloqueo está activo para este día',
  `hora_inicio_emergencia` time DEFAULT NULL COMMENT 'Hora de inicio del horario de emergencia (fuera del horario normal)',
  `hora_fin_emergencia` time DEFAULT NULL COMMENT 'Hora de fin del horario de emergencia (fuera del horario normal)',
  `emergencia_activa` tinyint(1) DEFAULT '0' COMMENT '1 si el horario de emergencia está activo para este día'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios_especialistas`
--

INSERT INTO `horarios_especialistas` (`id`, `especialista_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `intervalo_espacios`, `hora_inicio_bloqueo`, `hora_fin_bloqueo`, `bloqueo_activo`, `hora_inicio_emergencia`, `hora_fin_emergencia`, `emergencia_activa`) VALUES
(129, 18, 2, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(130, 18, 3, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(131, 18, 4, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(132, 18, 5, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(133, 18, 6, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(134, 18, 7, '09:00:00', '17:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(139, 26, 1, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(140, 26, 7, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(141, 31, 2, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(142, 31, 6, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(143, 27, 3, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(144, 27, 5, '09:00:00', '18:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(176, 34, 1, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, '18:00:00', '20:00:00', 1),
(177, 34, 2, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, '19:00:00', '20:00:00', 1),
(178, 34, 3, '09:00:00', '14:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(182, 35, 4, '16:00:00', '19:00:00', 1, 60, NULL, NULL, 0, NULL, NULL, 0),
(183, 35, 5, '16:00:00', '19:00:00', 1, 60, NULL, NULL, 0, '20:00:00', '21:00:00', 1),
(184, 35, 6, '11:00:00', '13:00:00', 1, 60, NULL, NULL, 0, '20:00:00', '21:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_seguridad`
--

CREATE TABLE `logs_seguridad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datos_adicionales` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs_seguridad`
--

INSERT INTO `logs_seguridad` (`id`, `usuario_id`, `accion`, `descripcion`, `ip_address`, `user_agent`, `datos_adicionales`, `created_at`) VALUES
(1, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 07:47:05'),
(2, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@ejemplo.com\"}', '2025-11-29 07:47:09'),
(3, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 07:47:20'),
(4, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 07:48:15'),
(5, 1, 'settings_update', 'Configuraciones generales actualizadas', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-29 07:48:38'),
(6, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-29 07:49:43'),
(7, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 07:50:02'),
(8, 1, 'settings_email', 'Configuración de correo actualizada', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-29 07:55:08'),
(9, 1, 'settings_email', 'Configuración de correo actualizada', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-29 07:55:38'),
(10, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 15:32:59'),
(11, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-29 15:33:12'),
(12, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '189.128.120.253', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"admin@reserverbot.com\"}', '2025-11-29 18:25:12'),
(13, 1, 'login', 'Inicio de sesión exitoso', '189.128.120.253', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-11-29 18:25:56'),
(14, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-02 16:58:36'),
(15, 1, 'branch_update', 'Sucursal actualizada: ReserBot Centro', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:08:40'),
(16, 1, 'branch_create', 'Sucursal creada: ReserBot Ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:10:01'),
(17, 1, 'service_create', 'Servicio creado: Asesoria ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:38:33'),
(18, 1, 'service_update', 'Servicio actualizado: Asesoria ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:38:46'),
(19, 1, 'category_create', 'Categoría creada: Categoria Ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:39:26'),
(20, 1, 'category_update', 'Categoría actualizada: Categoria Ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 17:39:44'),
(21, 1, 'specialist_create', 'Especialista creado: Ejemplo ejemploso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 18:14:19'),
(22, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-02 20:51:18'),
(23, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-02 21:42:14'),
(24, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@ejemplo.com\"}', '2025-12-02 21:45:29'),
(25, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-02 21:45:34'),
(26, 1, 'specialist_create', 'Especialista creado: Elsa Molina', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 21:47:22'),
(27, 1, 'branch_create', 'Sucursal creada: Consultorio Elsa Molina', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 21:47:51'),
(28, 1, 'specialist_update', 'Especialista actualizado: Elsa Molina', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 21:48:29'),
(29, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar', '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 21:50:00'),
(30, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-03 15:18:14'),
(31, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-03 16:21:38'),
(32, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-03 16:21:52'),
(33, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-03 18:34:51'),
(34, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-03 19:36:08'),
(35, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-03 21:04:13'),
(36, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 15:26:29'),
(37, 1, 'settings_update', 'Configuraciones generales actualizadas', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 15:26:58'),
(38, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 16:12:26'),
(39, 1, 'reservation_create', 'Reservación creada: RES-2025-81772', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:19:52'),
(40, 1, 'branch_update', 'Sucursal actualizada: ReserBot Centro', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:32:38'),
(41, 1, 'branch_update', 'Sucursal actualizada: ReserBot El Marqués', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:33:07'),
(42, 1, 'service_create', 'Servicio creado: Servicio ejemplo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:34:14'),
(43, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:34:39'),
(44, 1, 'specialist_update', 'Especialista actualizado: Dr. Roberto Martínez Pérez', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:35:53'),
(45, 1, 'specialist_update', 'Especialista actualizado: Lic. Ana García Ramírez', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:36:11'),
(46, 1, 'specialist_update', 'Especialista actualizado: Mtro. Juan Rodríguez Torres', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:36:27'),
(47, 1, 'client_update', 'Cliente actualizado: Miguel Torres Castillo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:36:54'),
(48, 1, 'client_update', 'Cliente actualizado: Pedro González Vega', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:37:20'),
(49, 1, 'client_update', 'Cliente actualizado: Sofía Ramírez Luna', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 16:37:34'),
(50, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 17:47:55'),
(51, 1, 'branch_update', 'Sucursal actualizada: Consultorio Elsa Molina', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:03:58'),
(52, 1, 'branch_update', 'Sucursal actualizada: Consultorio Elsa Molina', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:04:32'),
(53, 1, 'branch_update', 'Sucursal actualizada: ReserBot Juriquilla', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:04:53'),
(54, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:31:29'),
(55, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 18:31:33'),
(56, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:31:42'),
(57, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 18:31:53'),
(58, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 18:32:00'),
(59, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ejemplo@reserbot.com\"}', '2025-12-04 18:32:44'),
(60, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ejemplo@reserbot.com\"}', '2025-12-04 18:33:01'),
(61, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:33:02'),
(62, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:34:09'),
(63, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ejemplo@reserbot.com\"}', '2025-12-04 18:34:15'),
(64, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:34:47'),
(65, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:35:03'),
(66, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:35:12'),
(67, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2025-12-04 18:35:33'),
(68, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:35:36'),
(69, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:37:54'),
(70, 5, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 18:39:13'),
(71, 5, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 18:47:49'),
(72, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 18:47:53'),
(73, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 19:00:51'),
(74, 5, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 19:00:55'),
(75, 5, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 20:23:13'),
(76, 5, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 20:29:28'),
(77, 2, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"carlos.hernandez@reserbot.com\"}', '2025-12-04 20:30:02'),
(78, 2, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 20:33:17'),
(79, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-04 21:23:20'),
(80, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:23:23'),
(81, 5, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-04 21:23:26'),
(82, 5, 'password_change', 'Contraseña cambiada', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:23:59'),
(83, 5, 'password_change', 'Contraseña cambiada', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:24:24'),
(84, 5, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:24:32'),
(85, 2, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '{\"email\": \"carlos.hernandez@reserbot.com\"}', '2025-12-04 21:24:37'),
(86, 2, 'password_change', 'Contraseña cambiada', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:24:51'),
(87, 2, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 21:25:01'),
(88, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2025-12-09 20:38:55'),
(89, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2025-12-09 20:39:13'),
(90, 4, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2025-12-09 20:39:36'),
(91, 4, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-09 20:40:16'),
(92, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-09 20:40:20'),
(93, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-09 20:41:12'),
(94, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.191.8.222', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"admin@restobot.com\"}', '2025-12-09 20:42:55'),
(95, 1, 'login', 'Inicio de sesión exitoso', '187.191.8.222', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"admin@reserbot.com\"}', '2025-12-09 20:43:50'),
(96, 5, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-12 18:05:13'),
(97, 5, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-12 18:20:20'),
(98, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-12 18:20:25'),
(99, 1, 'branch_create', 'Sucursal creada: Hospital Angeles Centro Sur', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-12 18:24:12'),
(100, 1, 'service_create', 'Servicio creado: Consulta Otorrino', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-12 18:28:28'),
(101, 1, 'specialist_create', 'Especialista creado: Rodrigo Luegnas Capetillo', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-12 18:28:47'),
(102, 1, 'logout', 'Cierre de sesión', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-12 18:28:56'),
(103, 14, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2025-12-12 18:28:59'),
(104, 1, 'login', 'Inicio de sesión exitoso', '187.145.46.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-12 20:28:58'),
(105, 1, 'login', 'Inicio de sesión exitoso', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-15 18:00:11'),
(106, 1, 'logout', 'Cierre de sesión', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-15 18:00:16'),
(107, 14, 'login', 'Inicio de sesión exitoso', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2025-12-15 18:04:12'),
(108, 14, 'login', 'Inicio de sesión exitoso', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2025-12-16 04:28:13'),
(109, 14, 'logout', 'Cierre de sesión', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-16 04:45:26'),
(110, 1, 'login', 'Inicio de sesión exitoso', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2025-12-16 04:45:31'),
(111, 1, 'branch_create', 'Sucursal creada vía API: Consultorio Roberto  Ejemplo1', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-16 05:15:08'),
(112, 1, 'service_create', 'Servicio creado vía API: Corte ejemplo1', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-16 05:16:28'),
(113, 1, 'logout', 'Cierre de sesión', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-16 05:32:17'),
(114, 5, 'login', 'Inicio de sesión exitoso', '187.190.202.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2025-12-16 05:32:26'),
(115, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-21 19:42:51'),
(116, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:43:15'),
(117, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-21 19:45:41'),
(118, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:49:14'),
(119, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-21 19:49:24'),
(120, 1, 'branch_create', 'Sucursal creada vía API: Consultorio Mario Hernnandez', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:51:47'),
(121, 1, 'service_create', 'Servicio creado vía API: Consulta rápida', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:52:56'),
(122, 1, 'specialist_create', 'Especialista creado: Mario Hernandez Perez', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:53:08'),
(123, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-21 19:53:19'),
(124, 15, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"mariodoc@gmail.com\"}', '2026-01-21 19:53:24'),
(125, 15, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"mariodoc@gmail.com\"}', '2026-01-22 16:36:13'),
(126, 15, 'logout', 'Cierre de sesión', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-22 16:36:18'),
(127, 15, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"mariodoc@gmail.com\"}', '2026-01-22 16:36:20'),
(128, 15, 'logout', 'Cierre de sesión', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-22 16:41:32'),
(129, 1, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-22 16:41:38'),
(130, 1, 'logout', 'Cierre de sesión', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-22 16:43:39'),
(131, 15, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"email\": \"mariodoc@gmail.com\"}', '2026-01-22 16:43:45'),
(132, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-23 17:48:09'),
(133, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 17:49:20'),
(134, 15, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"mariodoc@gmail.com\"}', '2026-01-23 17:49:26'),
(135, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-23 18:06:30'),
(136, 1, 'service_create', 'Servicio creado vía API: Consultoria de negocios con IA', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 18:09:55'),
(137, 1, 'service_update', 'Servicio actualizado: Consultoria de negocios con IA', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 18:11:55'),
(138, 1, 'branch_create', 'Sucursal creada vía API: Agencia Experiencia', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 18:15:07'),
(139, 1, 'specialist_create', 'Especialista creado: Crack Andrés Raso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 18:16:01'),
(140, 15, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-23 18:27:09'),
(141, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-23 18:27:13'),
(142, 1, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"admin@reserbot.com\"}', '2026-01-26 19:20:21'),
(143, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-26 19:28:32'),
(144, 1, 'specialist_update', 'Especialista actualizado: Rodrigo Luengas Capetillo', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:33:38'),
(145, 1, 'specialist_update', 'Especialista actualizado: Rodrigo Luengas Capetillo', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:34:17'),
(146, 1, 'specialist_update', 'Especialista actualizado: Rodrigo Luengas Capetillo', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 19:34:17'),
(147, 1, 'branch_create', 'Sucursal creada vía API: Barberia de Javi ', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:41:06'),
(148, 1, 'specialist_create', 'Especialista creado: Javier Ortiz', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:41:18'),
(149, 1, 'branch_create', 'Sucursal creada: A  Domicilio', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:45:18'),
(150, 1, 'specialist_update', 'Especialista actualizado: Javier Ortiz', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:46:08'),
(151, 1, 'specialist_update', 'Especialista actualizado: Javier Ortiz', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 19:46:31'),
(152, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 19:49:28'),
(153, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-26 19:49:33'),
(154, 5, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 19:50:26'),
(155, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-26 19:50:30'),
(156, 1, 'logout', 'Cierre de sesión', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-01-26 20:00:27'),
(157, 17, 'login', 'Inicio de sesión exitoso', '189.141.32.43', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"diaz.javier.arturo84@gmail.com\"}', '2026-01-26 20:00:44'),
(158, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 20:01:15'),
(159, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-26 20:01:19'),
(160, 5, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 20:02:10'),
(161, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-26 20:02:14'),
(162, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 20:35:51'),
(163, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-26 20:35:55'),
(164, 5, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 21:07:01'),
(165, 5, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-26 21:07:32'),
(166, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-27 14:38:58'),
(167, 5, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:40:20'),
(168, 5, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:42:33'),
(169, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-27 14:42:42'),
(170, 5, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:51:36'),
(171, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 14:51:39'),
(172, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:51:59'),
(173, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:52:02'),
(174, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ejemplo@ejemplo.com\"}', '2026-01-27 14:52:09'),
(175, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 14:52:24'),
(176, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:52:50'),
(177, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 14:52:58'),
(178, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:54:39'),
(179, 2, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"carlos.hernandez@reserbot.com\"}', '2026-01-27 14:54:48'),
(180, 2, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:55:51'),
(181, 4, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2026-01-27 14:55:59'),
(182, 4, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:56:04'),
(183, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 14:56:09'),
(184, 1, 'specialist_update', 'Especialista actualizado: Dr. Roberto Martínez Pérez', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:56:56'),
(185, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:56:58'),
(186, 4, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2026-01-27 14:57:01'),
(187, 4, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:57:41'),
(188, 4, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 14:58:23'),
(189, 4, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"roberto.martinez@reserbot.com\"}', '2026-01-27 16:08:54'),
(190, 4, 'specialist_service_created', 'Servicio personal creado: Evento de ejemlo 1', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 16:28:00'),
(191, 4, 'specialist_services_update', 'Precios de servicios actualizados', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 16:28:20'),
(192, 4, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 16:50:27'),
(193, 4, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 16:57:16'),
(194, 4, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 16:57:57'),
(195, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 17:03:39'),
(196, 1, 'branch_create', 'Sucursal creada vía API: Sucursal Ejemplo Varias1', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 17:11:28'),
(197, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 18:22:55'),
(198, 1, 'branch_create', 'Sucursal creada vía API: Sucursal Ejemplo Varias2', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 18:26:42'),
(199, 1, 'specialist_update', 'Especialista actualizado: Ejemplo ejemplar en 2 sucursal(es)', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 19:32:30'),
(200, 1, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 20:03:45'),
(201, 5, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-27 20:03:48'),
(202, 5, 'reservation_cancel', 'Reservación cancelada: RES-2025-001', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 20:21:09'),
(203, 5, 'reservation_cancel', 'Reservación cancelada: RES-2025-81773', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 20:21:51'),
(204, 5, 'reservation_cancel', 'Reservación cancelada: RES-2025-002', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 20:21:58'),
(205, 5, 'logout', 'Cierre de sesión', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-27 20:22:25'),
(206, 1, 'login', 'Inicio de sesión exitoso', '187.154.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-27 20:22:28'),
(207, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 15:01:03'),
(208, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 18:06:22'),
(209, 1, 'branch_create', 'Sucursal creada vía API: Hacienda el Tintero', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:11:08'),
(210, 1, 'branch_create', 'Sucursal creada vía API: Hacienda Chichimequillas', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:11:52'),
(211, 1, 'specialist_create', 'Especialista creado: Javier Diaz en 2 sucursal(es)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:19:11'),
(212, 1, 'specialist_create', 'Especialista creado: Javier Diaz en 2 sucursal(es)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:20:32'),
(213, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:20:56');
INSERT INTO `logs_seguridad` (`id`, `usuario_id`, `accion`, `descripcion`, `ip_address`, `user_agent`, `datos_adicionales`, `created_at`) VALUES
(214, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"javier@reserbot.com\"}', '2026-01-28 18:21:06'),
(215, 19, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"javier@reserbot.com\"}', '2026-01-28 18:21:16'),
(216, 19, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:26:43'),
(217, 19, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:30:09'),
(218, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 18:30:12'),
(219, 1, 'specialist_update', 'Especialista actualizado: Javier Diaz Ortiz en 2 sucursal(es)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:32:44'),
(220, 1, 'branch_create', 'Sucursal creada vía API: Amealco', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:40:40'),
(221, 1, 'service_create', 'Servicio creado vía API: Ventas-Informes', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:41:36'),
(222, 1, 'specialist_create', 'Especialista creado: La Trinidad Fraccionamiento Campestre en 1 sucursal(es)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:41:39'),
(223, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 18:41:50'),
(224, 20, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-01-28 18:42:03'),
(225, 5, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-28 19:43:30'),
(226, 5, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 19:43:36'),
(227, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 19:43:42'),
(228, 1, 'reservation_cancel', 'Reservación cancelada: RES-2026-002', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:03:41'),
(229, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Mario Hernandez Perez', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:03:55'),
(230, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Mtro. Juan Rodríguez Torres', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:03'),
(231, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Ejemplo ejemplar', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:08'),
(232, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Javier Diaz', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:23'),
(233, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Javier Diaz Ortiz', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:29'),
(234, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Javier Ortiz', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:33'),
(235, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Dr. Roberto Martínez Pérez', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:37'),
(236, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Elsa Molina', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:04:41'),
(237, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:39:14'),
(238, 20, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-01-28 20:39:27'),
(239, 20, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:39:42'),
(240, 20, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 20:50:00'),
(241, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 20:50:03'),
(242, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:07:38'),
(243, 20, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-01-28 21:07:42'),
(244, 20, 'reservation_cancel', 'Reservación cancelada: RES-2026-005', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:09:40'),
(245, 20, 'reservation_cancel', 'Reservación cancelada: RES-2026-003', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:09:44'),
(246, 20, 'reservation_cancel', 'Reservación cancelada: RES-2026-004', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:09:46'),
(247, 20, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:20:07'),
(248, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 21:20:11'),
(249, 1, 'branch_update', 'Sucursal actualizada: La Trinidad Fraccionamiento Campestre, 20°11&#039;53.0&quot;N 100°13&#039;30.8&quot;W, 61015 michoacan, Mich.', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:24:54'),
(250, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:25:47'),
(251, 20, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-01-28 21:25:51'),
(252, 20, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-28 21:28:22'),
(253, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-28 21:28:26'),
(254, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 15:42:18'),
(255, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 16:51:21'),
(256, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:10:48'),
(257, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 17:10:54'),
(258, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:11:07'),
(259, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 17:11:10'),
(260, 5, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:11:18'),
(261, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 17:11:22'),
(262, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:11:28'),
(263, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 17:11:31'),
(264, 5, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:11:34'),
(265, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 17:11:37'),
(266, 1, 'specialist_update', 'Especialista actualizado: Lic. Ana García Ramírez en 2 sucursal(es)', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:11:51'),
(267, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 17:12:08'),
(268, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 17:12:11'),
(269, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 18:54:38'),
(270, 5, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:06:03'),
(271, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 19:06:06'),
(272, 1, 'specialist_update', 'Especialista actualizado: Lic. Ana García Ramírez en 5 sucursal(es)', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:06:27'),
(273, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:06:29'),
(274, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 19:06:32'),
(275, 5, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:06:53'),
(276, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 19:06:56'),
(277, 1, 'specialist_update', 'Especialista actualizado: Lic. Ana García Ramírez en 8 sucursal(es)', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:07:17'),
(278, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:18:26'),
(279, 1, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-01-30 19:19:14'),
(280, 1, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:42:37'),
(281, 14, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-01-30 19:42:45'),
(282, 14, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:43:06'),
(283, 14, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-01-30 19:43:44'),
(284, 14, 'logout', 'Cierre de sesión', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-01-30 19:43:51'),
(285, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 19:43:58'),
(286, 5, 'login', 'Inicio de sesión exitoso', '189.141.15.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-01-30 20:15:49'),
(287, 14, 'login', 'Inicio de sesión exitoso', '189.201.9.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-01-31 00:11:06'),
(288, 14, 'login', 'Inicio de sesión exitoso', '189.180.25.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-02 17:20:21'),
(289, 14, 'login', 'Inicio de sesión exitoso', '189.203.54.187', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-03 15:36:20'),
(290, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 15:40:11'),
(291, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 17:05:04'),
(292, 5, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-02-03 17:05:08'),
(293, 5, 'reservation_create', 'Reservación creada: RES-2026-86998', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 17:24:17'),
(294, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 18:22:12'),
(295, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 19:52:08'),
(296, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 19:52:16'),
(297, 5, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-02-03 19:52:19'),
(298, 5, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 19:52:45'),
(299, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 19:52:49'),
(300, 1, 'specialist_delete', 'Especialista eliminado permanentemente: Crack Andrés Raso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 19:52:56'),
(301, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-03 19:54:07'),
(302, 14, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 19:56:40'),
(303, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-03 19:56:41'),
(304, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 19:57:24'),
(305, 1, 'branch_create', 'Sucursal creada vía API: Hospital angeles Ensueño ', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 19:58:31'),
(306, 1, 'service_create', 'Servicio creado vía API: Consulta de emergencia ', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 19:59:46'),
(307, 1, 'specialist_update', 'Especialista actualizado: Rodrigo Luengas Capetillo en 2 sucursal(es)', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 19:59:53'),
(308, 1, 'logout', 'Cierre de sesión', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 20:00:38'),
(309, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-03 20:00:55'),
(310, 14, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-03 20:03:26'),
(311, 1, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-03 20:05:23'),
(312, 14, 'login', 'Inicio de sesión exitoso', '38.65.136.94', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-03 23:49:06'),
(313, 20, 'login', 'Inicio de sesión exitoso', '38.65.174.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-02-04 01:05:51'),
(314, 20, 'login', 'Inicio de sesión exitoso', '38.65.174.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-02-05 02:37:35'),
(315, 20, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-02-05 17:51:44'),
(316, 20, 'logout', 'Cierre de sesión', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-05 17:51:56'),
(317, 1, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-05 17:51:59'),
(318, 1, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-05 22:12:32'),
(319, 1, 'logout', 'Cierre de sesión', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-05 22:52:26'),
(320, 20, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-02-05 22:52:31'),
(321, 20, 'reservation_cancel', 'Reservación cancelada: RES-2026-87003', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-05 22:53:13'),
(322, 1, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-06 04:00:40'),
(323, 1, 'logout', 'Cierre de sesión', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 04:01:41'),
(324, 5, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-02-06 04:01:45'),
(325, 1, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-06 16:08:29'),
(326, 1, 'logout', 'Cierre de sesión', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 16:12:29'),
(327, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-06 16:12:57'),
(328, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-06 17:31:13'),
(329, 14, 'login', 'Inicio de sesión exitoso', '189.201.8.84', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-06 18:44:06'),
(330, 14, 'logout', 'Cierre de sesión', '189.201.8.84', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', NULL, '2026-02-06 18:48:37'),
(331, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-06 18:52:03'),
(332, 14, 'reservation_confirm', 'Reservación confirmada: RES-2026-87006', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 19:51:58'),
(333, 14, 'reservation_create', 'Reservación creada: RES-2026-72013', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 20:18:25'),
(334, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-06 21:11:39'),
(335, 14, 'reservation_reschedule', 'Reservación reagendada: RES-2026-87001 - Nueva fecha: 2026-02-11 12:00:00', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 21:12:55'),
(336, 14, 'reservation_reschedule', 'Reservación reagendada: RES-2026-87001 - Nueva fecha: 2026-02-10 10:30:00', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 21:13:13'),
(337, 14, 'reservation_reschedule', 'Reservación reagendada: RES-2026-87001 - Nueva fecha: 2026-02-11 10:30:00', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-06 21:13:41'),
(338, 14, 'login', 'Inicio de sesión exitoso', '187.200.96.213', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-07 00:23:11'),
(339, 14, 'reservation_confirm', 'Reservación confirmada: RES-2026-72014', '187.200.96.213', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', NULL, '2026-02-07 00:25:50'),
(340, 14, 'login', 'Inicio de sesión exitoso', '189.201.8.84', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-07 00:51:41'),
(341, 5, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"ana.garcia@reserbot.com\"}', '2026-02-08 19:38:44'),
(342, 5, 'logout', 'Cierre de sesión', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 19:38:57'),
(343, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-08 19:39:03'),
(344, 14, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 19:41:43'),
(345, 14, 'specialist_service_assigned', 'Servicio asignado al especialista', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 19:42:01'),
(346, 14, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 19:42:10'),
(347, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-08 23:04:56'),
(348, 14, 'specialist_services_update', 'Servicios actualizados (precios, duración y estado)', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 23:48:13'),
(349, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-09 18:11:10'),
(350, 20, 'login', 'Inicio de sesión exitoso', '202.5.99.95', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '{\"email\": \"trinidad@reserbot.com\"}', '2026-02-09 18:46:36'),
(351, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-09 19:15:11'),
(352, 14, 'reservation_create', 'Reservación creada: RES-2026-98645', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-09 19:20:42'),
(353, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-09 19:21:04'),
(354, 14, 'reservation_create', 'Reservación creada: RES-2026-93545', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', NULL, '2026-02-09 19:21:37'),
(355, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-09 20:40:05'),
(356, 14, 'login', 'Inicio de sesión exitoso', '187.190.197.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-09 23:56:44'),
(357, NULL, 'login_failed', 'Intento de inicio de sesión fallido', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"admin@reserbot.com\"}', '2026-02-10 15:32:48'),
(358, 14, 'login', 'Inicio de sesión exitoso', '189.180.252.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"email\": \"rodrigo@aide.com\"}', '2026-02-10 15:32:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('cita_nueva','cita_confirmada','cita_cancelada','cita_reprogramada','recordatorio','sistema') COLLATE utf8mb4_unicode_ci DEFAULT 'sistema',
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enlace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `enviada_email` tinyint(1) DEFAULT '0',
  `enviada_sms` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `enlace`, `leida`, `enviada_email`, `enviada_sms`, `created_at`) VALUES
(1, 10, 'cita_nueva', 'Nueva cita programada', 'Se ha programado una cita para el 08/12/2025 a las 09:00', NULL, 0, 0, 0, '2025-12-04 16:19:52'),
(3, 13, 'cita_cancelada', 'Cita cancelada', 'Su cita del 05/01/2026 ha sido cancelada.', NULL, 0, 0, 0, '2026-01-27 20:21:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `reservacion_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('paypal','efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_ci DEFAULT 'efectivo',
  `estado` enum('pendiente','completado','fallido','reembolsado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `referencia_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de transacción PayPal u otro',
  `comprobante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_pago` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_servicios`
--

CREATE TABLE `paquetes_servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `descuento_porcentaje` decimal(5,2) DEFAULT '0.00',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_servicios_items`
--

CREATE TABLE `paquetes_servicios_items` (
  `id` int(11) NOT NULL,
  `paquete_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantillas_notificaciones`
--

CREATE TABLE `plantillas_notificaciones` (
  `id` int(11) NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `tipo` enum('cita_nueva','confirmacion','recordatorio_24h','recordatorio_1h','cancelacion','reprogramacion') COLLATE utf8mb4_unicode_ci NOT NULL,
  `canal` enum('email','sms','whatsapp') COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservaciones`
--

CREATE TABLE `reservaciones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único de reservación',
  `cliente_id` int(11) DEFAULT NULL COMMENT 'ID del cliente en la tabla usuarios (NULL para reservas de chatbot)',
  `nombre_cliente` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre completo del cliente',
  `especialista_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `duracion_minutos` int(11) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmada','en_progreso','completada','cancelada','no_asistio') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `notas_cliente` text COLLATE utf8mb4_unicode_ci,
  `notas_especialista` text COLLATE utf8mb4_unicode_ci,
  `calificacion` tinyint(4) DEFAULT NULL COMMENT '1-5 estrellas',
  `comentario_resena` text COLLATE utf8mb4_unicode_ci,
  `recordatorio_enviado` tinyint(1) DEFAULT '0',
  `creado_por` int(11) DEFAULT NULL COMMENT 'Usuario que creó la cita (recepcionista)',
  `cancelado_por` int(11) DEFAULT NULL,
  `motivo_cancelacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_cancelacion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservaciones`
--

INSERT INTO `reservaciones` (`id`, `codigo`, `cliente_id`, `nombre_cliente`, `especialista_id`, `servicio_id`, `sucursal_id`, `fecha_cita`, `hora_inicio`, `hora_fin`, `duracion_minutos`, `precio_total`, `estado`, `notas_cliente`, `notas_especialista`, `calificacion`, `comentario_resena`, `recordatorio_enviado`, `creado_por`, `cancelado_por`, `motivo_cancelacion`, `fecha_cancelacion`, `created_at`, `updated_at`) VALUES
(15, 'RES-2026-003', NULL, 'Javier diaz', 18, 24, 16, '2026-01-31', '11:00:00', '11:30:00', 30, 0.00, 'cancelada', NULL, NULL, NULL, 'Quiero.informes u donde es', 0, NULL, 20, 'Cancelada por el usuario', '2026-01-28 15:09:44', '2026-01-28 18:45:49', '2026-01-28 21:09:44'),
(16, 'RES-2026-004', NULL, 'Roberto Pérez', 18, 24, 16, '2026-01-31', '12:00:00', '12:30:00', 30, 0.00, 'cancelada', NULL, NULL, NULL, 'Vendas', 0, NULL, 20, 'Cancelada por el usuario', '2026-01-28 15:09:46', '2026-01-28 18:53:28', '2026-01-28 21:09:46'),
(17, 'RES-2026-005', NULL, 'Juan Ejemplo', 18, 24, 16, '2026-01-31', '10:00:00', '10:30:00', 30, 0.00, 'cancelada', NULL, NULL, NULL, 'No', 0, NULL, 20, 'Cancelada por el usuario', '2026-01-28 15:09:40', '2026-01-28 18:53:51', '2026-01-28 21:09:40'),
(18, 'RES-2026-006', NULL, 'Claro Obscuro', 18, 24, 16, '2026-01-31', '09:00:00', '10:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, 'Nada', 0, NULL, NULL, NULL, NULL, '2026-01-28 21:08:34', '2026-01-28 21:08:34'),
(19, 'RES-2026-007', NULL, 'Roberto Ejemplo', 18, 24, 16, '2026-01-30', '11:00:00', '12:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, 'Nada', 0, NULL, NULL, NULL, NULL, '2026-01-28 21:10:45', '2026-01-28 21:10:45'),
(20, 'RES-2026-008', NULL, 'Ejemplo Dos', 18, 24, 16, '2026-02-01', '10:00:00', '11:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, 'Nada', 0, NULL, NULL, NULL, NULL, '2026-01-28 21:27:38', '2026-01-28 21:27:38'),
(22, 'RES-2026-010', NULL, 'Javier diaz', 18, 24, 16, '2026-02-01', '12:00:00', '13:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, 'Prueba', 0, NULL, NULL, NULL, NULL, '2026-01-31 00:09:46', '2026-01-31 00:09:46'),
(25, 'RES-2026-013', NULL, 'Sis non', 31, 10, 11, '2026-10-10', '11:00:00', '11:30:00', 30, 800.00, 'pendiente', NULL, NULL, NULL, 'Nada', 0, NULL, NULL, NULL, NULL, '2026-02-03 15:56:00', '2026-02-03 15:56:00'),
(26, 'RES-2026-014', NULL, 'Sis non3', 26, 10, 11, '2026-02-22', '14:30:00', '15:00:00', 30, 800.00, 'pendiente', NULL, NULL, NULL, 'Non', 0, NULL, NULL, NULL, NULL, '2026-02-03 16:27:12', '2026-02-03 16:27:12'),
(27, 'RES-2026-86998', NULL, 'Sis NonEjemplo', 26, 10, 11, '2026-02-08', '13:30:00', '14:00:00', 30, 800.00, 'pendiente', 'Nada', NULL, NULL, NULL, 0, 5, NULL, NULL, NULL, '2026-02-03 17:24:17', '2026-02-03 17:24:17'),
(29, 'RES-2026-86999', NULL, 'Javier diaz', 34, 1, 6, '2026-02-04', '10:00:00', '10:30:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, 'No', 0, NULL, NULL, NULL, NULL, '2026-02-03 20:07:29', '2026-02-03 20:07:29'),
(30, 'RES-2026-87000', NULL, 'Robeto Ejemplo', 35, 25, 17, '2026-02-05', '17:00:00', '17:30:00', 30, 1200.00, 'pendiente', NULL, NULL, NULL, 'nada', 0, NULL, NULL, NULL, NULL, '2026-02-03 20:08:42', '2026-02-03 20:08:42'),
(31, 'RES-2026-87001', NULL, 'Javier diaz', 34, 1, 6, '2026-02-11', '10:30:00', '11:00:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, 'Ngjg', 0, NULL, NULL, NULL, NULL, '2026-02-03 23:47:12', '2026-02-06 21:13:41'),
(32, 'RES-2026-87002', NULL, 'Javier diaz', 18, 24, 16, '2026-02-10', '15:00:00', '16:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, 'Prueba de maj', 0, NULL, NULL, NULL, NULL, '2026-02-05 17:39:33', '2026-02-05 17:39:33'),
(33, 'RES-2026-87003', NULL, 'Ejemplo Prueba', 18, 24, 16, '2026-02-10', '14:00:00', '15:00:00', 60, 0.00, 'cancelada', NULL, NULL, NULL, 'Nada', 0, NULL, 20, 'Cancelada por el usuario', '2026-02-05 16:53:13', '2026-02-05 22:51:27', '2026-02-05 22:53:13'),
(34, 'RES-2026-87004', NULL, 'Javier diaz', 35, 1, 17, '2026-02-14', '11:30:00', '12:00:00', 30, 500.00, 'pendiente', NULL, NULL, NULL, 'Nsr', 0, NULL, NULL, NULL, NULL, '2026-02-06 18:35:43', '2026-02-06 18:35:43'),
(35, 'RES-2026-87005', NULL, 'Javier diaz', 35, 25, 17, '2026-02-06', '18:00:00', '18:30:00', 30, 1200.00, 'pendiente', NULL, NULL, NULL, 'Nro', 0, NULL, NULL, NULL, NULL, '2026-02-06 18:42:57', '2026-02-06 18:42:57'),
(36, 'RES-2026-87006', NULL, 'Ejemplo Emergencia', 34, 25, 6, '2026-02-17', '19:00:00', '19:30:00', 30, 2000.00, 'confirmada', NULL, NULL, NULL, 'Nada', 0, NULL, NULL, NULL, NULL, '2026-02-06 19:05:10', '2026-02-06 19:51:58'),
(37, 'RES-2026-72013', NULL, 'ReservaPrueba Calendar', 35, 25, 17, '2026-02-06', '17:30:00', '18:00:00', 30, 1200.00, 'pendiente', '', NULL, NULL, NULL, 0, 14, NULL, NULL, NULL, '2026-02-06 20:18:25', '2026-02-06 20:18:25'),
(38, 'RES-2026-72014', NULL, 'Javier das', 34, 1, 6, '2026-02-16', '11:00:00', '11:30:00', 30, 1000.00, 'confirmada', NULL, NULL, NULL, 'Nel', 0, NULL, NULL, NULL, NULL, '2026-02-07 00:25:20', '2026-02-07 00:25:50'),
(39, 'RES-2026-72015', NULL, 'Ramon palacios', 35, 25, 17, '2026-02-07', '11:30:00', '12:00:00', 30, 1200.00, 'pendiente', NULL, NULL, NULL, 'No', 0, NULL, NULL, NULL, NULL, '2026-02-07 00:51:26', '2026-02-07 00:51:26'),
(40, 'RES-2026-72016', NULL, 'Ejemplo Eme', 34, 25, 6, '2026-02-17', '19:30:00', '20:00:00', 30, 2000.00, 'pendiente', NULL, NULL, NULL, 'Urgente', 0, NULL, NULL, NULL, NULL, '2026-02-09 00:13:18', '2026-02-09 00:13:18'),
(41, 'RES-2026-72017', NULL, 'Pavel Rodriguez', 18, 24, 16, '2026-02-14', '11:00:00', '12:00:00', 60, 0.00, 'pendiente', NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, '2026-02-09 18:49:13', '2026-02-09 18:49:13'),
(42, 'RES-2026-98645', NULL, 'Maly soria', 35, 1, 17, '2026-02-12', '17:30:00', '18:00:00', 30, 500.00, 'pendiente', '', NULL, NULL, NULL, 0, 14, NULL, NULL, NULL, '2026-02-09 19:20:42', '2026-02-09 19:20:42'),
(43, 'RES-2026-93545', NULL, 'ana peres', 34, 1, 6, '2026-02-25', '13:30:00', '14:00:00', 30, 1000.00, 'pendiente', '', NULL, NULL, NULL, 0, 14, NULL, NULL, NULL, '2026-02-09 19:21:37', '2026-02-09 19:21:37'),
(44, 'RES-2026-93546', NULL, 'Maly Soria', 34, 1, 6, '2026-02-16', '13:00:00', '13:30:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, '2026-02-09 20:45:09', '2026-02-09 20:45:09'),
(45, 'RES-2026-93547', NULL, 'Arturo ortiz', 34, 1, 6, '2026-02-23', '11:00:00', '11:30:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, 'Prueba de msj', 0, NULL, NULL, NULL, NULL, '2026-02-09 20:45:41', '2026-02-09 20:45:41'),
(46, 'RES-2026-93548', NULL, 'Pepe madero', 34, 1, 6, '2026-02-23', '11:30:00', '12:00:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, '2026-02-09 20:47:12', '2026-02-09 20:47:12'),
(47, 'RES-2026-93549', NULL, 'Adolfo Avila', 34, 1, 6, '2026-02-18', '11:00:00', '11:30:00', 30, 1000.00, 'pendiente', NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, '2026-02-09 22:54:35', '2026-02-09 22:54:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `duracion_minutos` int(11) DEFAULT '30',
  `precio` decimal(10,2) DEFAULT '0.00',
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `categoria_id`, `nombre`, `descripcion`, `duracion_minutos`, `precio`, `precio_oferta`, `imagen`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Consulta General', 'Consulta médica general con diagnóstico', 30, 500.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(2, 1, 'Chequeo Completo', 'Examen médico completo con análisis básicos', 60, 1500.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(3, 1, 'Consulta Pediatría', 'Atención médica para niños y adolescentes', 30, 600.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(4, 2, 'Facial Hidratante', 'Tratamiento facial de hidratación profunda', 45, 450.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(5, 2, 'Manicure Completo', 'Manicure con esmaltado y tratamiento de cutículas', 30, 200.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(6, 2, 'Masaje Relajante', 'Masaje corporal completo de relajación', 60, 600.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(7, 3, 'Corte de Cabello', 'Corte de cabello clásico o moderno', 30, 150.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(8, 3, 'Arreglo de Barba', 'Recorte y diseño de barba', 20, 100.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(9, 3, 'Corte + Barba', 'Servicio completo de corte y arreglo de barba', 45, 220.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(10, 4, 'Consulta Legal Básica', 'Asesoría legal general de 30 minutos', 30, 800.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(11, 4, 'Revisión de Contratos', 'Análisis y revisión de contratos', 60, 1500.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(12, 5, 'Asesoría de Inversiones', 'Consultoría en inversiones y portafolios', 45, 1000.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(13, 5, 'Planificación Financiera', 'Desarrollo de plan financiero personal', 60, 1200.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(14, 6, 'Sesión Individual', 'Sesión de terapia psicológica individual', 50, 700.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(15, 6, 'Terapia de Pareja', 'Sesión de terapia para parejas', 60, 900.00, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(16, 4, 'Asesoria ejemplo', 'Descripción de ejemplo', 30, 300.00, NULL, NULL, 1, '2025-12-02 17:38:33', '2025-12-02 17:38:46'),
(17, 7, 'Servicio ejemplo', 'Descripción de ejemplo', 30, 500.00, NULL, NULL, 1, '2025-12-04 16:34:14', '2025-12-04 16:34:14'),
(18, 1, 'Consulta Otorrino', 'Consulta Otorrino', 30, 0.00, NULL, NULL, 1, '2025-12-12 18:28:28', '2025-12-12 18:28:28'),
(19, 3, 'Corte ejemplo1', 'Descripción de corte ejemplo 1', 45, 350.00, NULL, NULL, 1, '2025-12-16 05:16:28', '2025-12-16 05:16:28'),
(20, 1, 'Consulta rápida', 'Consulta rapida', 40, 800.00, NULL, NULL, 1, '2026-01-21 19:52:56', '2026-01-21 19:52:56'),
(21, 7, 'Consultoria de negocios con IA', 'Integramos las mejores herramientas de IA y sus agentes para incrementar ventas en tu modelo de negocio', 45, 1300.00, 1000.00, NULL, 1, '2026-01-23 18:09:55', '2026-01-23 18:11:55'),
(23, 8, 'Evento de ejemlo 1', 'Descripción ejemplo 1', 35, 12000.00, NULL, NULL, 1, '2026-01-27 16:28:00', '2026-01-27 16:28:00'),
(24, 8, 'Ventas-Informes', '', 30, 0.00, NULL, NULL, 1, '2026-01-28 18:41:36', '2026-01-28 18:41:36'),
(25, 1, 'Consulta de emergencia ', 'consulta fuera del horario habitual para emergencias ', 30, 1200.00, NULL, NULL, 1, '2026-02-03 19:59:46', '2026-02-03 19:59:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zona_horaria` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'America/Mexico_City',
  `horario_apertura` time DEFAULT '08:00:00',
  `horario_cierre` time DEFAULT '20:00:00',
  `dias_laborales` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '1,2,3,4,5,6' COMMENT 'Días de la semana (1=Lunes, 7=Domingo)',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `ciudad`, `estado`, `codigo_postal`, `telefono`, `email`, `zona_horaria`, `horario_apertura`, `horario_cierre`, `dias_laborales`, `latitud`, `longitud`, `imagen`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'ReserBot Centro', 'Av. Constituyentes 200, Centro Histórico', 'Santiago de Querétaro', 'Querétaro', '76000', '5244221234', 'centro@reserbot.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', 20.59300000, -100.39290000, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 16:32:38'),
(2, 'ReserBot Juriquilla', 'Blvd. Juriquilla 1000, Juriquilla', 'Santiago de Querétaro', 'Querétaro', '76226', '4422345678', 'juriquilla@reserbot.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', 20.71130000, -100.44970000, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 18:04:53'),
(3, 'ReserBot El Marqués', 'Av. Paseo Constituyentes 500, Zona Industrial', 'El Marqués', 'Querétaro', '76240', '5244225678', 'marques@reserbot.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', 20.54650000, -100.26570000, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 16:33:07'),
(4, 'ReserBot Ejemplo', 'Calle ejemplo #1000', 'Queretaro', 'Querétaro', '70000', '1234567890', 'ejemplo@ejemplo.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2025-12-02 17:10:01', '2025-12-02 17:10:01'),
(5, 'Consultorio Elsa Molina', 'Avenida Constitución #30', 'Querétaro', 'Querétaro', '76087', '4421623671', 'elsamolina@gmail.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2025-12-02 21:47:51', '2025-12-04 18:04:32'),
(6, 'Hospital Angeles Centro Sur', 'Calle ejemplo', 'Qro', 'Querétaro', '76001', '2345678903', 'aide@aide.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2025-12-12 18:24:12', '2025-12-12 18:24:12'),
(7, 'Consultorio Roberto  Ejemplo1', 'Calle ejemplo1', 'Ciudad ejemplo1', 'Querétaro', '76000', '4427869807', 'robertoejemplo1@gmail.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2025-12-16 05:15:08', '2025-12-16 05:15:08'),
(8, 'Consultorio Mario Hernnandez', 'Calle ejemplo Mario', 'QUETETARO', 'Querétaro', '76096', '4423223423', 'docMario@gmail.com', 'America/Mexico_City', '09:00:00', '18:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-21 19:51:47', '2026-01-21 19:51:47'),
(9, 'Agencia Experiencia', 'PASEO DE LA CONSTITUCION 100, 3', 'Querétaro', 'Querétaro', '76140', '4422956843', 'contacto@agenciaexperiencia.com', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-23 18:15:07', '2026-01-23 18:15:07'),
(10, 'Barberia de Javi ', 'Hacienda el tintero 370 ', 'Queretaro ', 'Querétaro', '76230 ', '4422474539', 'sucursal@aide.com', 'America/Mexico_City', '13:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-26 19:41:06', '2026-01-26 19:41:06'),
(11, 'A  Domicilio', '', 'Queretaro', 'Querétaro', '', '', '', 'America/Mexico_City', '08:00:00', '20:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-26 19:45:18', '2026-01-26 19:45:18'),
(12, 'Sucursal Ejemplo Varias1', 'Dirección Sucursal Ejemplo Varias1', 'Querétaro', 'Querétaro', '76087', '4427869809', 'SucursalEjemploVarias1@gmail.com', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-27 17:11:28', '2026-01-27 17:11:28'),
(13, 'Sucursal Ejemplo Varias2', 'Dirección Sucursal Ejemplo Varias2', 'Querétaro', 'Querétaro', '76083', '4427869800', 'SucursalEjemploVarias2@gmail.com', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-27 18:26:42', '2026-01-27 18:26:42'),
(14, 'Hacienda el Tintero', '370', 'Querétaro', 'Querétaro', '76230', '', 'hacienda@gmail.com', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-28 18:11:08', '2026-01-28 18:11:08'),
(15, 'Hacienda Chichimequillas', '118', 'Querétaro', 'Querétaro', '76178', '4427869805', 'chchimequillas@gmail.com', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-28 18:11:52', '2026-01-28 18:11:52'),
(16, 'La Trinidad Fraccionamiento Campestre, 20°11&#039;53.0&quot;N 100°13&#039;30.8&quot;W, 61015 michoacan, Mich.', '123', 'Querétaro', 'Querétaro', '76088', '4427869800', 'trinidad@gmail.com', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-01-28 18:40:40', '2026-01-28 21:24:54'),
(17, 'Hospital angeles Ensueño ', 'bernardino del raso 27', 'queretaro ', 'Querétaro', '76178', '1122334455', '', 'America/Mexico_City', '00:00:00', '00:00:00', '1,2,3,4,5,6', NULL, NULL, NULL, 1, '2026-02-03 19:58:31', '2026-02-03 19:58:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` tinyint(4) NOT NULL DEFAULT '4' COMMENT '1=Superadmin, 2=Admin Sucursal, 3=Especialista, 4=Cliente, 5=Recepcionista',
  `sucursal_id` int(11) DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verificado` tinyint(1) DEFAULT '0',
  `token_verificacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_recuperacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `telefono`, `password`, `rol_id`, `sucursal_id`, `avatar`, `email_verificado`, `token_verificacion`, `token_recuperacion`, `token_expira`, `ultimo_acceso`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Sistema', 'admin@reserbot.com', '+52 442 100 0001', '$2y$10$XDSI5Te7YJzHwYyPgS4syua0RHEjBen7/MYsu7st5asjxR/tKjxkO', 1, NULL, NULL, 1, NULL, NULL, NULL, '2026-02-06 10:08:29', 1, '2025-11-29 03:20:01', '2026-02-06 16:08:29'),
(2, 'Carlos', 'Hernández García', 'carlos.hernandez@reserbot.com', '+52 442 111 1111', '$2y$10$uj.seCFdr.4Qf82BM8mcreO2nnyjsBZTNbWtvhLrYoZgFfNY08CuW', 2, 1, NULL, 1, NULL, NULL, NULL, '2026-01-27 08:54:48', 1, '2025-11-29 03:20:01', '2026-01-27 14:54:48'),
(3, 'María', 'López Sánchez', 'maria.lopez@reserbot.com', '+52 442 222 2222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 2, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(5, 'Lic. Ana', 'García Ramírez', 'ana.garcia@reserbot.com', '5244244444', '$2y$10$SbxjtuYU2wXo6Por31PXhusS21dqyzel2vt7CKQVb0TjkSZyA1bS6', 3, 11, NULL, 1, NULL, NULL, NULL, '2026-02-08 13:38:44', 1, '2025-11-29 03:20:01', '2026-02-08 19:38:44'),
(7, 'Laura', 'Sánchez Mendoza', 'laura.sanchez@reserbot.com', '+52 442 666 6666', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 1, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-11-29 03:20:01'),
(8, 'Pedro', 'González Vega', 'pedro.gonzalez@email.com', '5244277777', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 16:37:20'),
(9, 'Sofía', 'Ramírez Luna', 'sofia.ramirez@email.com', '5244288888', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 16:37:34'),
(10, 'Miguel', 'Torres Castillo', 'miguel.torres@email.com', '5244299999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-11-29 03:20:01', '2025-12-04 16:36:54'),
(13, 'Claro', 'Obscuro', '', '4427869806', '', 4, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, '2025-12-04 19:30:15', '2025-12-04 21:38:03'),
(14, 'Rodrigo', 'Luengas Capetillo', 'rodrigo@aide.com', '1234567899', '$2y$10$.c8YqjlDZoLLrZmoDS8Lq.yB4ESrdtMxZXgw/.gqI30ea5SIg3P0.', 3, 6, NULL, 1, NULL, NULL, NULL, '2026-02-10 09:32:51', 1, '2025-12-12 18:28:47', '2026-02-10 15:32:51'),
(20, 'La Trinidad', 'Fraccionamiento Campestre', 'trinidad@reserbot.com', '4427869807', '$2y$10$DMx6EImI3afm4YUe5s9CNOXf5x.98WC/FFk5ECCYULg9wqK23UvGm', 3, 16, NULL, 1, NULL, NULL, NULL, '2026-02-09 12:46:36', 1, '2026-01-28 18:41:39', '2026-02-09 18:46:36');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bloqueos_horario`
--
ALTER TABLE `bloqueos_horario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_especialista` (`especialista_id`),
  ADD KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `categorias_servicios`
--
ALTER TABLE `categorias_servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `dias_feriados`
--
ALTER TABLE `dias_feriados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_sucursal` (`sucursal_id`);

--
-- Indices de la tabla `especialistas`
--
ALTER TABLE `especialistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `especialistas_servicios`
--
ALTER TABLE `especialistas_servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_especialista_servicio` (`especialista_id`,`servicio_id`),
  ADD KEY `servicio_id` (`servicio_id`),
  ADD KEY `idx_es_emergencia` (`es_emergencia`);

--
-- Indices de la tabla `horarios_especialistas`
--
ALTER TABLE `horarios_especialistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_especialista` (`especialista_id`),
  ADD KEY `idx_dia` (`dia_semana`);

--
-- Indices de la tabla `logs_seguridad`
--
ALTER TABLE `logs_seguridad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_fecha` (`created_at`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_leida` (`leida`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservacion` (`reservacion_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `paquetes_servicios`
--
ALTER TABLE `paquetes_servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paquetes_servicios_items`
--
ALTER TABLE `paquetes_servicios_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paquete_id` (`paquete_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `plantillas_notificaciones`
--
ALTER TABLE `plantillas_notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sucursal` (`sucursal_id`);

--
-- Indices de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_especialista` (`especialista_id`),
  ADD KEY `idx_fecha` (`fecha_cita`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ciudad` (`ciudad`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_rol` (`rol_id`),
  ADD KEY `idx_sucursal` (`sucursal_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bloqueos_horario`
--
ALTER TABLE `bloqueos_horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_servicios`
--
ALTER TABLE `categorias_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `dias_feriados`
--
ALTER TABLE `dias_feriados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `especialistas`
--
ALTER TABLE `especialistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `especialistas_servicios`
--
ALTER TABLE `especialistas_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `horarios_especialistas`
--
ALTER TABLE `horarios_especialistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT de la tabla `logs_seguridad`
--
ALTER TABLE `logs_seguridad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=359;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paquetes_servicios`
--
ALTER TABLE `paquetes_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paquetes_servicios_items`
--
ALTER TABLE `paquetes_servicios_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plantillas_notificaciones`
--
ALTER TABLE `plantillas_notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bloqueos_horario`
--
ALTER TABLE `bloqueos_horario`
  ADD CONSTRAINT `bloqueos_horario_ibfk_1` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `dias_feriados`
--
ALTER TABLE `dias_feriados`
  ADD CONSTRAINT `dias_feriados_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `especialistas`
--
ALTER TABLE `especialistas`
  ADD CONSTRAINT `especialistas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `especialistas_ibfk_2` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `especialistas_servicios`
--
ALTER TABLE `especialistas_servicios`
  ADD CONSTRAINT `especialistas_servicios_ibfk_1` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `especialistas_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `horarios_especialistas`
--
ALTER TABLE `horarios_especialistas`
  ADD CONSTRAINT `horarios_especialistas_ibfk_1` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reservacion_id`) REFERENCES `reservaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `paquetes_servicios_items`
--
ALTER TABLE `paquetes_servicios_items`
  ADD CONSTRAINT `paquetes_servicios_items_ibfk_1` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes_servicios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paquetes_servicios_items_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `plantillas_notificaciones`
--
ALTER TABLE `plantillas_notificaciones`
  ADD CONSTRAINT `plantillas_notificaciones_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD CONSTRAINT `reservaciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservaciones_ibfk_2` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservaciones_ibfk_3` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservaciones_ibfk_4` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_servicios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
