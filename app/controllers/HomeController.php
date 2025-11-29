<?php
/**
 * ReserBot - Controlador Home
 */

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    
    /**
     * Página de inicio pública
     */
    public function index() {
        // Si está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        // Obtener datos para la landing page
        $categories = [];
        $branches = [];
        
        try {
            $categories = $this->db->fetchAll(
                "SELECT * FROM categorias_servicios WHERE activo = 1 ORDER BY orden"
            );
            
            $branches = $this->db->fetchAll(
                "SELECT * FROM sucursales WHERE activo = 1 ORDER BY nombre"
            );
        } catch (Exception $e) {
            // Si hay error de BD, usar arrays vacíos
        }
        
        // Renderizar landing page (sin layout)
        $viewFile = VIEWS_PATH . '/home/index.php';
        if (file_exists($viewFile)) {
            extract([
                'categories' => $categories,
                'branches' => $branches
            ]);
            include $viewFile;
        } else {
            // Fallback: redirigir a login
            redirect('/login');
        }
    }
}
