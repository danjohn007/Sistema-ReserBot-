<?php
/**
 * ReserBot - Sistema de Reservaciones y Citas Profesionales
 * Punto de entrada principal
 */

// Iniciar sesión
session_name('reserbot_session');
session_start();

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

// Obtener la ruta solicitada y sanitizarla
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';

// Remover el basePath y /public de la URI
$route = str_replace($basePath, '', $requestUri);
$route = preg_replace('/^\/public/', '', $route);
$route = strtok($route, '?'); // Remover query string
$route = rtrim($route, '/');

// Validar que la ruta solo contiene caracteres válidos
$route = preg_replace('/[^a-zA-Z0-9\-\/]/', '', $route);

if (empty($route)) {
    $route = '/';
}

// Definir rutas
$routes = [
    // Rutas públicas
    '/' => ['controller' => 'HomeController', 'action' => 'index'],
    '/login' => ['controller' => 'AuthController', 'action' => 'login'],
    '/registro' => ['controller' => 'AuthController', 'action' => 'register'],
    '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    '/recuperar-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],
    '/reset-password' => ['controller' => 'AuthController', 'action' => 'resetPassword'],
    
    // Dashboard
    '/dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    
    // Sucursales
    '/sucursales' => ['controller' => 'BranchController', 'action' => 'index'],
    '/sucursales/crear' => ['controller' => 'BranchController', 'action' => 'create'],
    '/sucursales/editar' => ['controller' => 'BranchController', 'action' => 'edit'],
    '/sucursales/eliminar' => ['controller' => 'BranchController', 'action' => 'delete'],
    
    // Especialistas
    '/especialistas' => ['controller' => 'SpecialistController', 'action' => 'index'],
    '/especialistas/crear' => ['controller' => 'SpecialistController', 'action' => 'create'],
    '/especialistas/editar' => ['controller' => 'SpecialistController', 'action' => 'edit'],
    '/especialistas/eliminar' => ['controller' => 'SpecialistController', 'action' => 'delete'],
    '/especialistas/horarios' => ['controller' => 'SpecialistController', 'action' => 'schedules'],
    '/especialistas/perfil' => ['controller' => 'SpecialistController', 'action' => 'profile'],
    
    // Servicios
    '/servicios' => ['controller' => 'ServiceController', 'action' => 'index'],
    '/servicios/crear' => ['controller' => 'ServiceController', 'action' => 'create'],
    '/servicios/editar' => ['controller' => 'ServiceController', 'action' => 'edit'],
    '/servicios/eliminar' => ['controller' => 'ServiceController', 'action' => 'delete'],
    '/categorias' => ['controller' => 'ServiceController', 'action' => 'categories'],
    '/categorias/crear' => ['controller' => 'ServiceController', 'action' => 'createCategory'],
    '/categorias/editar' => ['controller' => 'ServiceController', 'action' => 'editCategory'],
    
    // Reservaciones
    '/reservaciones' => ['controller' => 'ReservationController', 'action' => 'index'],
    '/reservaciones/nueva' => ['controller' => 'ReservationController', 'action' => 'create'],
    '/reservaciones/ver' => ['controller' => 'ReservationController', 'action' => 'view'],
    '/reservaciones/confirmar' => ['controller' => 'ReservationController', 'action' => 'confirm'],
    '/reservaciones/cancelar' => ['controller' => 'ReservationController', 'action' => 'cancel'],
    '/reservaciones/disponibilidad' => ['controller' => 'ReservationController', 'action' => 'availability'],
    '/mis-citas' => ['controller' => 'ReservationController', 'action' => 'myAppointments'],
    
    // Calendario
    '/calendario' => ['controller' => 'CalendarController', 'action' => 'index'],
    '/calendario/eventos' => ['controller' => 'CalendarController', 'action' => 'events'],
    
    // Clientes
    '/clientes' => ['controller' => 'ClientController', 'action' => 'index'],
    '/clientes/ver' => ['controller' => 'ClientController', 'action' => 'view'],
    '/clientes/editar' => ['controller' => 'ClientController', 'action' => 'edit'],
    
    // Reportes
    '/reportes' => ['controller' => 'ReportController', 'action' => 'index'],
    '/reportes/citas' => ['controller' => 'ReportController', 'action' => 'appointments'],
    '/reportes/ingresos' => ['controller' => 'ReportController', 'action' => 'income'],
    '/reportes/especialistas' => ['controller' => 'ReportController', 'action' => 'specialists'],
    '/reportes/exportar' => ['controller' => 'ReportController', 'action' => 'export'],
    
    // Notificaciones
    '/notificaciones' => ['controller' => 'NotificationController', 'action' => 'index'],
    '/notificaciones/marcar-leida' => ['controller' => 'NotificationController', 'action' => 'markAsRead'],
    '/notificaciones/plantillas' => ['controller' => 'NotificationController', 'action' => 'templates'],
    
    // Configuraciones
    '/configuraciones' => ['controller' => 'SettingsController', 'action' => 'index'],
    '/configuraciones/general' => ['controller' => 'SettingsController', 'action' => 'general'],
    '/configuraciones/estilos' => ['controller' => 'SettingsController', 'action' => 'styles'],
    '/configuraciones/correo' => ['controller' => 'SettingsController', 'action' => 'email'],
    '/configuraciones/paypal' => ['controller' => 'SettingsController', 'action' => 'paypal'],
    '/configuraciones/feriados' => ['controller' => 'SettingsController', 'action' => 'holidays'],
    
    // Logs / Seguridad
    '/logs' => ['controller' => 'LogController', 'action' => 'index'],
    
    // Perfil de usuario
    '/perfil' => ['controller' => 'ProfileController', 'action' => 'index'],
    '/perfil/editar' => ['controller' => 'ProfileController', 'action' => 'edit'],
    '/perfil/cambiar-password' => ['controller' => 'ProfileController', 'action' => 'changePassword'],
    
    // API endpoints
    '/api/disponibilidad' => ['controller' => 'ApiController', 'action' => 'availability'],
    '/api/especialistas' => ['controller' => 'ApiController', 'action' => 'specialists'],
    '/api/servicios' => ['controller' => 'ApiController', 'action' => 'services'],
    '/api/sucursales' => ['controller' => 'ApiController', 'action' => 'branches'],
];

// Buscar la ruta
if (isset($routes[$route])) {
    $controllerName = $routes[$route]['controller'];
    $actionName = $routes[$route]['action'];
} else {
    // Intentar rutas con parámetros
    $matched = false;
    foreach ($routes as $pattern => $routeInfo) {
        // Convertir patrones con {param} a regex
        $regex = preg_replace('/\{[a-zA-Z]+\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $route, $matches)) {
            $controllerName = $routeInfo['controller'];
            $actionName = $routeInfo['action'];
            array_shift($matches); // Remover el match completo
            $_GET['params'] = $matches;
            $matched = true;
            break;
        }
    }
    
    if (!$matched) {
        // 404 Not Found
        http_response_code(404);
        include VIEWS_PATH . '/errors/404.php';
        exit;
    }
}

// Cargar el controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
        } else {
            http_response_code(404);
            include VIEWS_PATH . '/errors/404.php';
        }
    } else {
        http_response_code(500);
        echo "Error: Controller class not found: " . $controllerName;
    }
} else {
    http_response_code(500);
    echo "Error: Controller file not found: " . $controllerFile;
}
