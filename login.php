<?php
/**
 * ReserBot - Redirección a Login
 * 
 * Este archivo existe como fallback para servidores donde mod_rewrite
 * no está habilitado o .htaccess no es procesado.
 */

// Simular la ruta /login
$_SERVER['REQUEST_URI'] = '/login';

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
