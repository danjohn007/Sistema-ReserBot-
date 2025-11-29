<?php
/**
 * ReserBot - Redirección a Login
 * 
 * Este archivo existe como fallback para servidores donde mod_rewrite
 * no está habilitado o .htaccess no es procesado.
 */

// Establecer la ruta deseada
define('RESERBOT_ROUTE', '/login');

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
