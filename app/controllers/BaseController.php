<?php
/**
 * ReserBot - Controlador Base
 */

class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Renderiza una vista
     */
    protected function render($view, $data = []) {
        // Extraer datos para que estén disponibles en la vista
        extract($data);
        
        // Incluir el layout principal
        $content = VIEWS_PATH . '/' . $view . '.php';
        
        if (file_exists($content)) {
            include VIEWS_PATH . '/layouts/main.php';
        } else {
            echo "Vista no encontrada: " . $view;
        }
    }
    
    /**
     * Renderiza una vista sin layout
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Vista no encontrada: " . $view;
        }
    }
    
    /**
     * Renderiza una vista con layout de autenticación
     */
    protected function renderAuth($view, $data = []) {
        extract($data);
        
        $content = VIEWS_PATH . '/' . $view . '.php';
        
        if (file_exists($content)) {
            include VIEWS_PATH . '/layouts/auth.php';
        } else {
            echo "Vista no encontrada: " . $view;
        }
    }
    
    /**
     * Devuelve JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Requiere autenticación
     */
    protected function requireAuth() {
        if (!isLoggedIn()) {
            setFlashMessage('error', 'Debe iniciar sesión para acceder a esta página.');
            redirect('/login');
        }
    }
    
    /**
     * Requiere un rol específico
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!hasAnyRole($roles)) {
            setFlashMessage('error', 'No tiene permisos para acceder a esta página.');
            redirect('/dashboard');
        }
    }
    
    /**
     * Obtiene un parámetro GET
     */
    protected function get($key, $default = null) {
        return isset($_GET[$key]) ? cleanInput($_GET[$key]) : $default;
    }
    
    /**
     * Obtiene un parámetro POST
     */
    protected function post($key, $default = null) {
        return isset($_POST[$key]) ? cleanInput($_POST[$key]) : $default;
    }
    
    /**
     * Verifica si es una petición POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica si es una petición AJAX
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
