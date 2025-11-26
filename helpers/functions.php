<?php
/**
 * ReserBot - Funciones auxiliares
 */

/**
 * Redirige a una URL
 */
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

/**
 * Genera una URL completa
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Genera una URL pública (para assets)
 */
function asset($path = '') {
    return PUBLIC_URL . '/' . ltrim($path, '/');
}

/**
 * Escapa texto para HTML
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Muestra mensajes flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Verifica si el usuario está autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtiene el usuario actual
 */
function currentUser() {
    if (isLoggedIn()) {
        return $_SESSION['user'] ?? null;
    }
    return null;
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function hasRole($roleId) {
    $user = currentUser();
    return $user && $user['rol_id'] == $roleId;
}

/**
 * Verifica si el usuario tiene alguno de los roles especificados
 */
function hasAnyRole($roles) {
    $user = currentUser();
    if (!$user) return false;
    return in_array($user['rol_id'], $roles);
}

/**
 * Obtiene el nombre del rol
 */
function getRoleName($roleId) {
    global $ROLE_NAMES;
    return $ROLE_NAMES[$roleId] ?? 'Desconocido';
}

/**
 * Formatea una fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * Formatea una hora
 */
function formatTime($time, $format = 'H:i') {
    if (!$time) return '';
    return date($format, strtotime($time));
}

/**
 * Formatea dinero
 */
function formatMoney($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Genera un código único para reservaciones
 */
function generateReservationCode() {
    return 'RES-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * Genera un token aleatorio
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Obtiene la IP del cliente
 */
function getClientIP() {
    $ip = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Registra una acción en el log de seguridad
 */
function logAction($action, $description = '', $data = null) {
    if (!class_exists('Database')) return;
    
    try {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'] ?? null;
        $ip = getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $db->insert(
            "INSERT INTO logs_seguridad (usuario_id, accion, descripcion, ip_address, user_agent, datos_adicionales) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $action, $description, $ip, $userAgent, $data ? json_encode($data) : null]
        );
    } catch (Exception $e) {
        // Silently fail
    }
}

/**
 * Obtiene una configuración del sistema
 */
function getConfig($key, $default = null) {
    if (!class_exists('Database')) return $default;
    
    try {
        $db = Database::getInstance();
        $result = $db->fetch("SELECT valor FROM configuraciones WHERE clave = ?", [$key]);
        return $result ? $result['valor'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Actualiza una configuración del sistema
 */
function setConfig($key, $value) {
    if (!class_exists('Database')) return false;
    
    try {
        $db = Database::getInstance();
        $db->update("UPDATE configuraciones SET valor = ? WHERE clave = ?", [$value, $key]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Valida un email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Limpia una cadena de texto
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Devuelve la clase CSS para el estado de una reservación
 */
function getStatusBadgeClass($status) {
    $classes = [
        'pendiente' => 'bg-yellow-100 text-yellow-800',
        'confirmada' => 'bg-green-100 text-green-800',
        'en_progreso' => 'bg-blue-100 text-blue-800',
        'completada' => 'bg-gray-100 text-gray-800',
        'cancelada' => 'bg-red-100 text-red-800',
        'no_asistio' => 'bg-orange-100 text-orange-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Devuelve el texto del estado de una reservación
 */
function getStatusText($status) {
    $texts = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'en_progreso' => 'En Progreso',
        'completada' => 'Completada',
        'cancelada' => 'Cancelada',
        'no_asistio' => 'No Asistió'
    ];
    return $texts[$status] ?? $status;
}

/**
 * Obtiene los días de la semana
 */
function getDaysOfWeek() {
    return [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];
}

/**
 * Verifica si una fecha es feriado
 */
function isHoliday($date, $branchId = null) {
    if (!class_exists('Database')) return false;
    
    try {
        $db = Database::getInstance();
        $sql = "SELECT id FROM dias_feriados WHERE fecha = ? AND (sucursal_id IS NULL OR sucursal_id = ?)";
        $result = $db->fetch($sql, [$date, $branchId]);
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Tiempo relativo (hace X minutos, etc.)
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Hace un momento';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return "Hace $mins minuto" . ($mins > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "Hace $hours hora" . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "Hace $days día" . ($days > 1 ? 's' : '');
    } else {
        return formatDate($datetime);
    }
}
