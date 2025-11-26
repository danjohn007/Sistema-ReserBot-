<?php
/**
 * ReserBot - Controlador de Logs
 */

require_once __DIR__ . '/BaseController.php';

class LogController extends BaseController {
    
    /**
     * Lista de logs
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $page = $this->get('page') ?: 1;
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $accion = $this->get('accion');
        $usuario_id = $this->get('usuario_id');
        $fecha = $this->get('fecha');
        
        $filters = [];
        $sql = "SELECT l.*, u.nombre, u.apellidos, u.email
                FROM logs_seguridad l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                WHERE 1=1";
        
        if ($accion) {
            $sql .= " AND l.accion LIKE ?";
            $filters[] = '%' . $accion . '%';
        }
        
        if ($usuario_id) {
            $sql .= " AND l.usuario_id = ?";
            $filters[] = $usuario_id;
        }
        
        if ($fecha) {
            $sql .= " AND DATE(l.created_at) = ?";
            $filters[] = $fecha;
        }
        
        $sql .= " ORDER BY l.created_at DESC LIMIT $perPage OFFSET $offset";
        
        $logs = $this->db->fetchAll($sql, $filters);
        
        // Contar total
        $sqlCount = "SELECT COUNT(*) as total FROM logs_seguridad l WHERE 1=1";
        $filtersCount = [];
        
        if ($accion) {
            $sqlCount .= " AND l.accion LIKE ?";
            $filtersCount[] = '%' . $accion . '%';
        }
        
        if ($usuario_id) {
            $sqlCount .= " AND l.usuario_id = ?";
            $filtersCount[] = $usuario_id;
        }
        
        if ($fecha) {
            $sqlCount .= " AND DATE(l.created_at) = ?";
            $filtersCount[] = $fecha;
        }
        
        $total = $this->db->fetch($sqlCount, $filtersCount)['total'];
        $totalPages = ceil($total / $perPage);
        
        // Usuarios para filtro
        $users = $this->db->fetchAll("SELECT id, nombre, apellidos, email FROM usuarios ORDER BY nombre, apellidos");
        
        // Tipos de acciones
        $actions = $this->db->fetchAll("SELECT DISTINCT accion FROM logs_seguridad ORDER BY accion");
        
        $this->render('logs/index', [
            'title' => 'Logs de Seguridad',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'perPage' => $perPage,
                'totalRecords' => $total
            ],
            'filters' => [
                'accion' => $accion,
                'usuario_id' => $usuario_id,
                'fecha' => $fecha
            ]
        ]);
    }
}
