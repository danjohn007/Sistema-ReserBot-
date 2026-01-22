<?php
/**
 * ReserBot - Redirección a Registro
 * 
 * Este archivo existe como fallback para servidores donde mod_rewrite
 * no está habilitado o .htaccess no es procesado.
 */

// Establecer codificación UTF-8
header('Content-Type: text/html; charset=UTF-8');

// Establecer la ruta deseada
define('RESERBOT_ROUTE', '/registro');

// Incluir el punto de entrada principal
require_once __DIR__ . '/index.php';
