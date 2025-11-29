<?php
/**
 * ReserBot - Sistema de Reservaciones y Citas Profesionales
 * Archivo de configuración principal
 */

// Detectar la URL base automáticamente
function detectBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Detectar el directorio base desde el script
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = dirname($scriptName);
    
    // Si estamos en la raíz del proyecto
    if ($scriptDir == '/' || $scriptDir == '\\') {
        $basePath = '';
    } else {
        // Remover /public si está presente
        $basePath = preg_replace('/\/public.*$/', '', $scriptDir);
    }
    
    return rtrim($protocol . $host . $basePath, '/');
}

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiderese_reserbot');
define('DB_USER', 'aiderese_reserbot');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'ReserBot');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'America/Mexico_City');

// URL Base - se detecta automáticamente
define('BASE_URL', detectBaseUrl());
define('PUBLIC_URL', BASE_URL . '/public');

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('HELPERS_PATH', ROOT_PATH . '/helpers');

// Configuración de sesión
define('SESSION_NAME', 'reserbot_session');
define('SESSION_LIFETIME', 3600); // 1 hora

// Configuración de email (valores por defecto)
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_FROM', 'noreply@reserbot.com');
define('MAIL_FROM_NAME', 'ReserBot');

// PayPal (valores por defecto)
define('PAYPAL_MODE', 'sandbox'); // sandbox o live
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');

// Zona horaria
date_default_timezone_set(APP_TIMEZONE);

// Entorno de la aplicación (development, production)
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Configuración de errores basada en entorno
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Roles de usuario
define('ROLE_SUPERADMIN', 1);
define('ROLE_BRANCH_ADMIN', 2);
define('ROLE_SPECIALIST', 3);
define('ROLE_CLIENT', 4);
define('ROLE_RECEPTIONIST', 5);

// Nombres de roles
$GLOBALS['ROLE_NAMES'] = [
    ROLE_SUPERADMIN => 'Administrador General',
    ROLE_BRANCH_ADMIN => 'Administrador de Sucursal',
    ROLE_SPECIALIST => 'Especialista',
    ROLE_CLIENT => 'Cliente',
    ROLE_RECEPTIONIST => 'Recepcionista'
];
