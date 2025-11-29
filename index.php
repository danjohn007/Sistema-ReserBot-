<?php
/**
 * ReserBot - Sistema de Reservaciones y Citas Profesionales
 * Punto de entrada raíz - Redirige al punto de entrada principal en public/
 * 
 * Este archivo existe para manejar casos donde mod_rewrite no está habilitado
 * o cuando .htaccess no es procesado por el servidor.
 */

// Establecer la ruta deseada si se accede directamente a index.php
if (basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
    define('RESERBOT_ROUTE', '/');
}

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
