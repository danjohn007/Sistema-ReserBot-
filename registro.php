<?php
/**
 * ReserBot - Redirección a Registro
 * 
 * Este archivo existe como fallback para servidores donde mod_rewrite
 * no está habilitado o .htaccess no es procesado.
 */

// Simular la ruta /registro
$_SERVER['REQUEST_URI'] = '/registro';

// Incluir el punto de entrada principal
require_once __DIR__ . '/public/index.php';
