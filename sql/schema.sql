-- =====================================================
-- ReserBot - Sistema de Reservaciones y Citas Profesionales
-- Base de datos MySQL 5.7+
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `reserbot_db` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `reserbot_db`;

-- =====================================================
-- TABLA: Configuración del Sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(100) NOT NULL UNIQUE,
    `valor` TEXT,
    `tipo` ENUM('text', 'number', 'boolean', 'json', 'color', 'image') DEFAULT 'text',
    `descripcion` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `telefono` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `rol_id` TINYINT NOT NULL DEFAULT 4 COMMENT '1=Superadmin, 2=Admin Sucursal, 3=Especialista, 4=Cliente, 5=Recepcionista',
    `sucursal_id` INT DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `email_verificado` TINYINT(1) DEFAULT 0,
    `token_verificacion` VARCHAR(100) DEFAULT NULL,
    `token_recuperacion` VARCHAR(100) DEFAULT NULL,
    `token_expira` DATETIME DEFAULT NULL,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_rol` (`rol_id`),
    INDEX `idx_sucursal` (`sucursal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Sucursales
-- =====================================================
CREATE TABLE IF NOT EXISTS `sucursales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `direccion` TEXT,
    `ciudad` VARCHAR(100),
    `estado` VARCHAR(100),
    `codigo_postal` VARCHAR(10),
    `telefono` VARCHAR(20),
    `email` VARCHAR(150),
    `zona_horaria` VARCHAR(50) DEFAULT 'America/Mexico_City',
    `horario_apertura` TIME DEFAULT '08:00:00',
    `horario_cierre` TIME DEFAULT '20:00:00',
    `dias_laborales` VARCHAR(50) DEFAULT '1,2,3,4,5,6' COMMENT 'Días de la semana (1=Lunes, 7=Domingo)',
    `latitud` DECIMAL(10, 8) DEFAULT NULL,
    `longitud` DECIMAL(11, 8) DEFAULT NULL,
    `imagen` VARCHAR(255) DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ciudad` (`ciudad`),
    INDEX `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Días Feriados
-- =====================================================
CREATE TABLE IF NOT EXISTS `dias_feriados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sucursal_id` INT DEFAULT NULL COMMENT 'NULL = aplica a todas',
    `fecha` DATE NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `recurrente` TINYINT(1) DEFAULT 0 COMMENT 'Se repite cada año',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_fecha` (`fecha`),
    INDEX `idx_sucursal` (`sucursal_id`),
    FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Categorías de Servicios
-- =====================================================
CREATE TABLE IF NOT EXISTS `categorias_servicios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `icono` VARCHAR(50) DEFAULT 'fas fa-concierge-bell',
    `color` VARCHAR(20) DEFAULT '#3B82F6',
    `orden` INT DEFAULT 0,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Servicios
-- =====================================================
CREATE TABLE IF NOT EXISTS `servicios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `categoria_id` INT NOT NULL,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT,
    `duracion_minutos` INT DEFAULT 30,
    `precio` DECIMAL(10, 2) DEFAULT 0.00,
    `precio_oferta` DECIMAL(10, 2) DEFAULT NULL,
    `imagen` VARCHAR(255) DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_categoria` (`categoria_id`),
    INDEX `idx_activo` (`activo`),
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias_servicios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Especialistas
-- =====================================================
CREATE TABLE IF NOT EXISTS `especialistas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `sucursal_id` INT NOT NULL,
    `profesion` VARCHAR(100),
    `especialidad` VARCHAR(150),
    `descripcion` TEXT,
    `experiencia_anos` INT DEFAULT 0,
    `tarifa_base` DECIMAL(10, 2) DEFAULT 0.00,
    `duracion_cita_default` INT DEFAULT 30 COMMENT 'minutos',
    `calificacion_promedio` DECIMAL(3, 2) DEFAULT 0.00,
    `total_resenas` INT DEFAULT 0,
    `foto` VARCHAR(255) DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_sucursal` (`sucursal_id`),
    INDEX `idx_activo` (`activo`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Servicios por Especialista
-- =====================================================
CREATE TABLE IF NOT EXISTS `especialistas_servicios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `especialista_id` INT NOT NULL,
    `servicio_id` INT NOT NULL,
    `precio_personalizado` DECIMAL(10, 2) DEFAULT NULL,
    `duracion_personalizada` INT DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    UNIQUE KEY `unique_especialista_servicio` (`especialista_id`, `servicio_id`),
    FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Horarios de Especialistas
-- =====================================================
CREATE TABLE IF NOT EXISTS `horarios_especialistas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `especialista_id` INT NOT NULL,
    `dia_semana` TINYINT NOT NULL COMMENT '1=Lunes, 7=Domingo',
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    INDEX `idx_especialista` (`especialista_id`),
    INDEX `idx_dia` (`dia_semana`),
    FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Bloqueos de Horario (vacaciones, pausas)
-- =====================================================
CREATE TABLE IF NOT EXISTS `bloqueos_horario` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `especialista_id` INT NOT NULL,
    `fecha_inicio` DATETIME NOT NULL,
    `fecha_fin` DATETIME NOT NULL,
    `motivo` VARCHAR(255),
    `tipo` ENUM('vacaciones', 'pausa', 'personal', 'otro') DEFAULT 'otro',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_especialista` (`especialista_id`),
    INDEX `idx_fechas` (`fecha_inicio`, `fecha_fin`),
    FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Reservaciones / Citas
-- =====================================================
CREATE TABLE IF NOT EXISTS `reservaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código único de reservación',
    `cliente_id` INT NOT NULL,
    `especialista_id` INT NOT NULL,
    `servicio_id` INT NOT NULL,
    `sucursal_id` INT NOT NULL,
    `fecha_cita` DATE NOT NULL,
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `duracion_minutos` INT NOT NULL,
    `precio_total` DECIMAL(10, 2) NOT NULL,
    `estado` ENUM('pendiente', 'confirmada', 'en_progreso', 'completada', 'cancelada', 'no_asistio') DEFAULT 'pendiente',
    `notas_cliente` TEXT,
    `notas_especialista` TEXT,
    `calificacion` TINYINT DEFAULT NULL COMMENT '1-5 estrellas',
    `comentario_resena` TEXT,
    `recordatorio_enviado` TINYINT(1) DEFAULT 0,
    `creado_por` INT DEFAULT NULL COMMENT 'Usuario que creó la cita (recepcionista)',
    `cancelado_por` INT DEFAULT NULL,
    `motivo_cancelacion` VARCHAR(255),
    `fecha_cancelacion` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_codigo` (`codigo`),
    INDEX `idx_cliente` (`cliente_id`),
    INDEX `idx_especialista` (`especialista_id`),
    INDEX `idx_fecha` (`fecha_cita`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_sucursal` (`sucursal_id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Pagos
-- =====================================================
CREATE TABLE IF NOT EXISTS `pagos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reservacion_id` INT NOT NULL,
    `monto` DECIMAL(10, 2) NOT NULL,
    `metodo_pago` ENUM('paypal', 'efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
    `estado` ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    `referencia_pago` VARCHAR(100) DEFAULT NULL COMMENT 'ID de transacción PayPal u otro',
    `comprobante` VARCHAR(255) DEFAULT NULL,
    `notas` TEXT,
    `fecha_pago` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_reservacion` (`reservacion_id`),
    INDEX `idx_estado` (`estado`),
    FOREIGN KEY (`reservacion_id`) REFERENCES `reservaciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Paquetes de Servicios
-- =====================================================
CREATE TABLE IF NOT EXISTS `paquetes_servicios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT,
    `precio` DECIMAL(10, 2) NOT NULL,
    `descuento_porcentaje` DECIMAL(5, 2) DEFAULT 0,
    `imagen` VARCHAR(255) DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Servicios en Paquetes
-- =====================================================
CREATE TABLE IF NOT EXISTS `paquetes_servicios_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `paquete_id` INT NOT NULL,
    `servicio_id` INT NOT NULL,
    `cantidad` INT DEFAULT 1,
    FOREIGN KEY (`paquete_id`) REFERENCES `paquetes_servicios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Notificaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS `notificaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `tipo` ENUM('cita_nueva', 'cita_confirmada', 'cita_cancelada', 'cita_reprogramada', 'recordatorio', 'sistema') DEFAULT 'sistema',
    `titulo` VARCHAR(200) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `enlace` VARCHAR(255) DEFAULT NULL,
    `leida` TINYINT(1) DEFAULT 0,
    `enviada_email` TINYINT(1) DEFAULT 0,
    `enviada_sms` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_leida` (`leida`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Plantillas de Notificaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS `plantillas_notificaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sucursal_id` INT DEFAULT NULL,
    `tipo` ENUM('cita_nueva', 'confirmacion', 'recordatorio_24h', 'recordatorio_1h', 'cancelacion', 'reprogramacion') NOT NULL,
    `canal` ENUM('email', 'sms', 'whatsapp') NOT NULL,
    `asunto` VARCHAR(200),
    `contenido` TEXT NOT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sucursal` (`sucursal_id`),
    FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Logs de Seguridad (Bitácora)
-- =====================================================
CREATE TABLE IF NOT EXISTS `logs_seguridad` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(255),
    `datos_adicionales` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_accion` (`accion`),
    INDEX `idx_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE EJEMPLO - CONFIGURACIÓN DEL SISTEMA
-- =====================================================
INSERT INTO `configuraciones` (`clave`, `valor`, `tipo`, `descripcion`) VALUES
('nombre_sitio', 'ReserBot', 'text', 'Nombre del sistema'),
('logotipo', NULL, 'image', 'Logotipo del sistema'),
('email_sistema', 'contacto@reserbot.com', 'text', 'Correo principal del sistema'),
('telefono_contacto', '+52 442 123 4567', 'text', 'Teléfono de contacto'),
('horario_atencion', 'Lunes a Viernes 8:00 - 20:00', 'text', 'Horario de atención'),
('color_primario', '#3B82F6', 'color', 'Color primario del sistema'),
('color_secundario', '#1E40AF', 'color', 'Color secundario del sistema'),
('color_acento', '#10B981', 'color', 'Color de acento'),
('paypal_client_id', '', 'text', 'Client ID de PayPal'),
('paypal_secret', '', 'text', 'Secret de PayPal'),
('paypal_modo', 'sandbox', 'text', 'Modo de PayPal (sandbox/live)'),
('qr_api_url', '', 'text', 'URL de API para QRs masivos'),
('confirmacion_automatica', '0', 'boolean', 'Confirmar citas automáticamente'),
('recordatorio_24h', '1', 'boolean', 'Enviar recordatorio 24h antes'),
('recordatorio_1h', '1', 'boolean', 'Enviar recordatorio 1h antes'),
('permitir_cancelacion_cliente', '1', 'boolean', 'Permitir que clientes cancelen citas'),
('horas_anticipacion_cancelacion', '24', 'number', 'Horas mínimas de anticipación para cancelar');

-- =====================================================
-- DATOS DE EJEMPLO - USUARIO SUPERADMIN
-- Password: admin123 (hasheado con password_hash)
-- =====================================================
INSERT INTO `usuarios` (`nombre`, `apellidos`, `email`, `telefono`, `password`, `rol_id`, `email_verificado`, `activo`) VALUES
('Administrador', 'Sistema', 'admin@reserbot.com', '+52 442 100 0001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 1);

-- =====================================================
-- DATOS DE EJEMPLO - SUCURSALES EN QUERÉTARO
-- =====================================================
INSERT INTO `sucursales` (`nombre`, `direccion`, `ciudad`, `estado`, `codigo_postal`, `telefono`, `email`, `latitud`, `longitud`) VALUES
('ReserBot Centro', 'Av. Constituyentes 100, Centro Histórico', 'Santiago de Querétaro', 'Querétaro', '76000', '+52 442 212 3456', 'centro@reserbot.com', 20.5930, -100.3929),
('ReserBot Juriquilla', 'Blvd. Juriquilla 1000, Juriquilla', 'Santiago de Querétaro', 'Querétaro', '76226', '+52 442 234 5678', 'juriquilla@reserbot.com', 20.7113, -100.4497),
('ReserBot El Marqués', 'Av. Paseo Constituyentes 500, Zona Industrial', 'El Marqués', 'Querétaro', '76240', '+52 442 256 7890', 'marques@reserbot.com', 20.5465, -100.2657);

-- =====================================================
-- DATOS DE EJEMPLO - CATEGORÍAS DE SERVICIOS
-- =====================================================
INSERT INTO `categorias_servicios` (`nombre`, `descripcion`, `icono`, `color`, `orden`) VALUES
('Medicina General', 'Consultas médicas generales y especialidades', 'fas fa-stethoscope', '#EF4444', 1),
('Belleza y Estética', 'Servicios de belleza, spa y estética', 'fas fa-spa', '#EC4899', 2),
('Barbería', 'Cortes de cabello, barba y tratamientos capilares', 'fas fa-cut', '#8B5CF6', 3),
('Asesoría Legal', 'Consultoría y asesoría jurídica', 'fas fa-balance-scale', '#6366F1', 4),
('Consultoría Financiera', 'Asesoría en finanzas e inversiones', 'fas fa-chart-line', '#10B981', 5),
('Psicología', 'Terapia y consulta psicológica', 'fas fa-brain', '#F59E0B', 6);

-- =====================================================
-- DATOS DE EJEMPLO - SERVICIOS
-- =====================================================
INSERT INTO `servicios` (`categoria_id`, `nombre`, `descripcion`, `duracion_minutos`, `precio`) VALUES
-- Medicina General
(1, 'Consulta General', 'Consulta médica general con diagnóstico', 30, 500.00),
(1, 'Chequeo Completo', 'Examen médico completo con análisis básicos', 60, 1500.00),
(1, 'Consulta Pediatría', 'Atención médica para niños y adolescentes', 30, 600.00),
-- Belleza y Estética
(2, 'Facial Hidratante', 'Tratamiento facial de hidratación profunda', 45, 450.00),
(2, 'Manicure Completo', 'Manicure con esmaltado y tratamiento de cutículas', 30, 200.00),
(2, 'Masaje Relajante', 'Masaje corporal completo de relajación', 60, 600.00),
-- Barbería
(3, 'Corte de Cabello', 'Corte de cabello clásico o moderno', 30, 150.00),
(3, 'Arreglo de Barba', 'Recorte y diseño de barba', 20, 100.00),
(3, 'Corte + Barba', 'Servicio completo de corte y arreglo de barba', 45, 220.00),
-- Asesoría Legal
(4, 'Consulta Legal Básica', 'Asesoría legal general de 30 minutos', 30, 800.00),
(4, 'Revisión de Contratos', 'Análisis y revisión de contratos', 60, 1500.00),
-- Consultoría Financiera
(5, 'Asesoría de Inversiones', 'Consultoría en inversiones y portafolios', 45, 1000.00),
(5, 'Planificación Financiera', 'Desarrollo de plan financiero personal', 60, 1200.00),
-- Psicología
(6, 'Sesión Individual', 'Sesión de terapia psicológica individual', 50, 700.00),
(6, 'Terapia de Pareja', 'Sesión de terapia para parejas', 60, 900.00);

-- =====================================================
-- DATOS DE EJEMPLO - USUARIOS ADICIONALES
-- Password para todos: password123
-- =====================================================
INSERT INTO `usuarios` (`nombre`, `apellidos`, `email`, `telefono`, `password`, `rol_id`, `sucursal_id`, `email_verificado`, `activo`) VALUES
-- Administradores de Sucursal
('Carlos', 'Hernández García', 'carlos.hernandez@reserbot.com', '+52 442 111 1111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, 1, 1),
('María', 'López Sánchez', 'maria.lopez@reserbot.com', '+52 442 222 2222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 2, 1, 1),
-- Especialistas
('Dr. Roberto', 'Martínez Pérez', 'roberto.martinez@reserbot.com', '+52 442 333 3333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1, 1),
('Lic. Ana', 'García Ramírez', 'ana.garcia@reserbot.com', '+52 442 444 4444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1, 1),
('Mtro. Juan', 'Rodríguez Torres', 'juan.rodriguez@reserbot.com', '+52 442 555 5555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1, 1),
-- Recepcionistas
('Laura', 'Sánchez Mendoza', 'laura.sanchez@reserbot.com', '+52 442 666 6666', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 1, 1, 1),
-- Clientes
('Pedro', 'González Vega', 'pedro.gonzalez@email.com', '+52 442 777 7777', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, 1, 1),
('Sofía', 'Ramírez Luna', 'sofia.ramirez@email.com', '+52 442 888 8888', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, 1, 1),
('Miguel', 'Torres Castillo', 'miguel.torres@email.com', '+52 442 999 9999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, NULL, 1, 1);

-- =====================================================
-- DATOS DE EJEMPLO - ESPECIALISTAS
-- =====================================================
INSERT INTO `especialistas` (`usuario_id`, `sucursal_id`, `profesion`, `especialidad`, `descripcion`, `experiencia_anos`, `tarifa_base`, `calificacion_promedio`, `total_resenas`) VALUES
(4, 1, 'Médico', 'Medicina General', 'Médico general con amplia experiencia en diagnóstico y tratamiento de enfermedades comunes. Egresado de la UAQ.', 15, 500.00, 4.8, 120),
(5, 1, 'Abogada', 'Derecho Civil y Mercantil', 'Especialista en derecho civil, contratos y asuntos mercantiles. Miembro del Colegio de Abogados de Querétaro.', 10, 800.00, 4.9, 85),
(6, 2, 'Psicólogo', 'Psicología Clínica', 'Maestro en Psicología Clínica con especialidad en terapia cognitivo-conductual. Atención a adultos y parejas.', 8, 700.00, 4.7, 95);

-- =====================================================
-- DATOS DE EJEMPLO - SERVICIOS POR ESPECIALISTA
-- =====================================================
INSERT INTO `especialistas_servicios` (`especialista_id`, `servicio_id`) VALUES
(1, 1), (1, 2), (1, 3),  -- Dr. Roberto: servicios médicos
(2, 10), (2, 11),         -- Lic. Ana: servicios legales
(3, 14), (3, 15);         -- Mtro. Juan: servicios psicología

-- =====================================================
-- DATOS DE EJEMPLO - HORARIOS DE ESPECIALISTAS
-- =====================================================
INSERT INTO `horarios_especialistas` (`especialista_id`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
-- Dr. Roberto (Lunes a Viernes)
(1, 1, '09:00:00', '14:00:00'), (1, 1, '16:00:00', '19:00:00'),
(1, 2, '09:00:00', '14:00:00'), (1, 2, '16:00:00', '19:00:00'),
(1, 3, '09:00:00', '14:00:00'), (1, 3, '16:00:00', '19:00:00'),
(1, 4, '09:00:00', '14:00:00'), (1, 4, '16:00:00', '19:00:00'),
(1, 5, '09:00:00', '14:00:00'),
-- Lic. Ana (Lunes a Jueves)
(2, 1, '10:00:00', '14:00:00'), (2, 1, '16:00:00', '20:00:00'),
(2, 2, '10:00:00', '14:00:00'), (2, 2, '16:00:00', '20:00:00'),
(2, 3, '10:00:00', '14:00:00'), (2, 3, '16:00:00', '20:00:00'),
(2, 4, '10:00:00', '14:00:00'),
-- Mtro. Juan (Martes a Sábado)
(3, 2, '08:00:00', '13:00:00'), (3, 2, '15:00:00', '18:00:00'),
(3, 3, '08:00:00', '13:00:00'), (3, 3, '15:00:00', '18:00:00'),
(3, 4, '08:00:00', '13:00:00'), (3, 4, '15:00:00', '18:00:00'),
(3, 5, '08:00:00', '13:00:00'),
(3, 6, '09:00:00', '14:00:00');

-- =====================================================
-- DATOS DE EJEMPLO - DÍAS FERIADOS EN QUERÉTARO
-- =====================================================
INSERT INTO `dias_feriados` (`sucursal_id`, `fecha`, `nombre`, `recurrente`) VALUES
(NULL, '2024-01-01', 'Año Nuevo', 1),
(NULL, '2024-02-05', 'Día de la Constitución', 1),
(NULL, '2024-03-18', 'Natalicio de Benito Juárez', 1),
(NULL, '2024-05-01', 'Día del Trabajo', 1),
(NULL, '2024-09-16', 'Día de la Independencia', 1),
(NULL, '2024-11-18', 'Revolución Mexicana', 1),
(NULL, '2024-12-25', 'Navidad', 1),
(NULL, '2024-09-25', 'Aniversario de la Fundación de Querétaro', 1);

-- =====================================================
-- DATOS DE EJEMPLO - RESERVACIONES
-- =====================================================
INSERT INTO `reservaciones` (`codigo`, `cliente_id`, `especialista_id`, `servicio_id`, `sucursal_id`, `fecha_cita`, `hora_inicio`, `hora_fin`, `duracion_minutos`, `precio_total`, `estado`) VALUES
('RES-2024-001', 8, 1, 1, 1, CURDATE(), '10:00:00', '10:30:00', 30, 500.00, 'confirmada'),
('RES-2024-002', 9, 1, 2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', '12:00:00', 60, 1500.00, 'pendiente'),
('RES-2024-003', 10, 2, 10, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', '10:30:00', 30, 800.00, 'confirmada'),
('RES-2024-004', 8, 3, 14, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:00:00', '15:50:00', 50, 700.00, 'pendiente');

COMMIT;
