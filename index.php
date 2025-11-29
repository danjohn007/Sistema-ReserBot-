<?php
/**
 * ReserBot - Sistema de Reservaciones y Citas Profesionales
 * Punto de entrada raíz - Redirige al punto de entrada principal en public/
 * 
 * Este archivo existe para manejar casos donde mod_rewrite no está habilitado
 * o cuando .htaccess no es procesado por el servidor.
 */

// Establecer la ruta deseada para la página de inicio
define('RESERBOT_ROUTE', '/');

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
