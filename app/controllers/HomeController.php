<?php
/**
 * ReserBot - Controlador Home
 */

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    
    /**
     * Página de inicio
     */
    public function index() {
        // Si está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        // Mostrar página de bienvenida
        redirect('/login');
    }
}
