<?php
/**
 * ReserBot - Sistema de Reservaciones y Citas Profesionales
 * Punto de entrada raíz - Redirige al punto de entrada principal en public/
 * 
 * Este archivo existe para manejar casos donde mod_rewrite no está habilitado
 * o cuando .htaccess no es procesado por el servidor.
 */

// Si acceden directamente a index.php, mostrar la página de inicio
if (basename($_SERVER['SCRIPT_NAME']) === 'index.php' && 
    strpos($_SERVER['REQUEST_URI'], '/index.php') !== false) {
    $_SERVER['REQUEST_URI'] = '/';
}

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
