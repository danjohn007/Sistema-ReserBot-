<?php
/**
 * ReserBot - Controlador de Calendario
 */

require_once __DIR__ . '/BaseController.php';

class CalendarController extends BaseController {
    
    /**
     * Vista del calendario
     */
    public function index() {
        $this->requireAuth();
        
        $user = currentUser();
        
        // Obtener sucursales y especialistas para filtros
        $branches = [];
        $specialists = [];
        
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
            $specialists = $this->db->fetchAll(
                "SELECT e.id, u.nombre, u.apellidos, s.nombre as sucursal_nombre
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 JOIN sucursales s ON e.sucursal_id = s.id
                 WHERE e.activo = 1
                 ORDER BY u.nombre, u.apellidos"
            );
        } elseif ($user['rol_id'] == ROLE_BRANCH_ADMIN || $user['rol_id'] == ROLE_RECEPTIONIST) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE id = ?", [$user['sucursal_id']]);
            $specialists = $this->db->fetchAll(
                "SELECT e.id, u.nombre, u.apellidos
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 WHERE e.sucursal_id = ? AND e.activo = 1
                 ORDER BY u.nombre, u.apellidos",
                [$user['sucursal_id']]
            );
        } elseif ($user['rol_id'] == ROLE_SPECIALIST) {
            // Para especialistas: obtener sus sucursales y sus registros de especialista
            $branches = $this->db->fetchAll(
                "SELECT DISTINCT s.id, s.nombre 
                 FROM sucursales s
                 JOIN especialistas e ON s.id = e.sucursal_id
                 WHERE e.usuario_id = ? AND e.activo = 1 AND s.activo = 1
                 ORDER BY s.nombre",
                [$user['id']]
            );
        }
        
        // Para especialistas: obtener su usuario_id para crear reservas
        $currentSpecialistId = null;
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            $currentSpecialistId = $user['id'];
        }
        
        $this->render('calendar/index', [
            'title' => 'Calendario',
            'branches' => $branches,
            'specialists' => $specialists,
            'currentSpecialistId' => $currentSpecialistId,
            'user' => $user
        ]);
    }
    
    /**
     * API para obtener eventos del calendario
     */
    public function events() {
        $this->requireAuth();
        
        $user = currentUser();
        $start = $this->get('start');
        $end = $this->get('end');
        $especialista_id = $this->get('especialista_id');
        $sucursal_id = $this->get('sucursal_id');
        
        $filters = [];
        $sql = "SELECT r.*, 
                       COALESCE(CONCAT(u.nombre, ' ', u.apellidos), r.nombre_cliente, 'Cliente sin registro') as cliente_nombre_completo,
                       u.nombre as cliente_nombre, u.apellidos as cliente_apellidos,
                       s.nombre as servicio_nombre, ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos,
                       suc.nombre as sucursal_nombre, suc.id as sucursal_id,
                       r.especialista_id, r.servicio_id
                FROM reservaciones r
                LEFT JOIN usuarios u ON r.cliente_id = u.id
                JOIN servicios s ON r.servicio_id = s.id
                JOIN especialistas e ON r.especialista_id = e.id
                JOIN usuarios ue ON e.usuario_id = ue.id
                JOIN sucursales suc ON r.sucursal_id = suc.id
                WHERE r.fecha_cita BETWEEN ? AND ? AND r.estado NOT IN ('cancelada')";
        
        $filters[] = $start;
        $filters[] = $end;
        
        // Aplicar filtros según rol
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            // Para especialistas: mostrar todas sus citas de todas sus sucursales
            // A menos que filtren por una sucursal específica
            if ($sucursal_id) {
                // Filtrar por sucursal específica
                $specialist = $this->db->fetch(
                    "SELECT id FROM especialistas WHERE usuario_id = ? AND sucursal_id = ?",
                    [$user['id'], $sucursal_id]
                );
                if ($specialist) {
                    $sql .= " AND r.especialista_id = ?";
                    $filters[] = $specialist['id'];
                }
            } else {
                // Mostrar todas las citas del especialista en todas sus sucursales
                $specialistIds = $this->db->fetchAll(
                    "SELECT id FROM especialistas WHERE usuario_id = ? AND activo = 1",
                    [$user['id']]
                );
                if (!empty($specialistIds)) {
                    $ids = array_column($specialistIds, 'id');
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $sql .= " AND r.especialista_id IN ($placeholders)";
                    $filters = array_merge($filters, $ids);
                }
            }
        } elseif ($user['rol_id'] == ROLE_CLIENT) {
            $sql .= " AND r.cliente_id = ?";
            $filters[] = $user['id'];
        } elseif ($user['rol_id'] == ROLE_BRANCH_ADMIN || $user['rol_id'] == ROLE_RECEPTIONIST) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $user['sucursal_id'];
        }
        
        if ($especialista_id) {
            $sql .= " AND r.especialista_id = ?";
            $filters[] = $especialista_id;
        }
        
        if ($sucursal_id && $user['rol_id'] == ROLE_SUPERADMIN) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $sucursal_id;
        }
        
        $reservations = $this->db->fetchAll($sql, $filters);
        
        $events = [];
        foreach ($reservations as $r) {
            $color = $this->getEventColor($r['estado']);
            
            $events[] = [
                'id' => $r['id'],
                'title' => $r['servicio_nombre'] . ' - ' . $r['cliente_nombre_completo'],
                'start' => $r['fecha_cita'] . 'T' . $r['hora_inicio'],
                'end' => $r['fecha_cita'] . 'T' . $r['hora_fin'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'codigo' => $r['codigo'],
                    'estado' => $r['estado'],
                    'cliente' => $r['cliente_nombre_completo'],
                    'especialista' => $r['especialista_nombre'] . ' ' . $r['especialista_apellidos'],
                    'servicio' => $r['servicio_nombre'],
                    'precio' => formatMoney($r['precio_total']),
                    'sucursal_id' => $r['sucursal_id'],
                    'sucursal_nombre' => $r['sucursal_nombre'],
                    'especialista_id' => $r['especialista_id'],
                    'servicio_id' => $r['servicio_id']
                ]
            ];
        }
        
        $this->json($events);
    }
    
    /**
     * Obtiene el color según el estado
     */
    private function getEventColor($estado) {
        $colors = [
            'pendiente' => '#F59E0B',
            'confirmada' => '#10B981',
            'en_progreso' => '#3B82F6',
            'completada' => '#6B7280',
            'no_asistio' => '#EF4444'
        ];
        
        return $colors[$estado] ?? '#6B7280';
    }
}
