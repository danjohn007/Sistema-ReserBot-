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
                    'tipo' => 'reservacion',
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
        
        // Agregar bloqueos de horario
        $blockSql = "SELECT b.*, 
                            ue.nombre as especialista_nombre, 
                            ue.apellidos as especialista_apellidos,
                            suc.nombre as sucursal_nombre
                     FROM bloqueos_horario b
                     JOIN especialistas e ON b.especialista_id = e.id
                     JOIN usuarios ue ON e.usuario_id = ue.id
                     LEFT JOIN sucursales suc ON b.sucursal_id = suc.id
                     WHERE b.fecha_inicio BETWEEN ? AND ?";
        
        $blockFilters = [$start, $end];
        
        // Aplicar filtros según rol
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            $blockSql .= " AND e.usuario_id = ?";
            $blockFilters[] = $user['id'];
        } elseif (($user['rol_id'] == ROLE_BRANCH_ADMIN || $user['rol_id'] == ROLE_RECEPTIONIST) && $user['sucursal_id']) {
            $blockSql .= " AND (b.sucursal_id = ? OR b.sucursal_id IS NULL)";
            $blockFilters[] = $user['sucursal_id'];
        }
        
        // Aplicar filtros de usuario
        if ($especialista_id) {
            $blockSql .= " AND b.especialista_id = ?";
            $blockFilters[] = $especialista_id;
        }
        
        if ($sucursal_id) {
            $blockSql .= " AND (b.sucursal_id = ? OR b.sucursal_id IS NULL)";
            $blockFilters[] = $sucursal_id;
        }
        
        $blocks = $this->db->fetchAll($blockSql, $blockFilters);
        
        foreach ($blocks as $b) {
            $tipoLabel = [
                'vacaciones' => '🌴 Vacaciones',
                'pausa' => '☕ Pausa',
                'personal' => '👤 Personal',
                'puntual' => '🔒 Bloqueado',
                'otro' => '⛔ No disponible'
            ];
            
            $events[] = [
                'id' => 'block-' . $b['id'],
                'title' => ($tipoLabel[$b['tipo']] ?? '⛔ Bloqueado') . ($b['motivo'] ? ': ' . $b['motivo'] : ''),
                'start' => $b['fecha_inicio'],
                'end' => $b['fecha_fin'],
                'backgroundColor' => '#DC2626',
                'borderColor' => '#991B1B',
                'display' => 'block',
                'extendedProps' => [
                    'tipo' => 'bloqueo',
                    'bloqueo_id' => $b['id'],
                    'especialista' => $b['especialista_nombre'] . ' ' . $b['especialista_apellidos'],
                    'sucursal' => $b['sucursal_nombre'] ?? 'Todas las sucursales',
                    'motivo' => $b['motivo'],
                    'tipo_bloqueo' => $b['tipo']
                ]
            ];
        }
        
        $this->json($events);
    }
    
    /**
     * Crear bloqueo de horario
     */
    public function bloquear() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        $user = currentUser();
        
        // Solo especialistas pueden bloquear sus propios horarios
        if ($user['rol_id'] != ROLE_SPECIALIST) {
            $this->json(['success' => false, 'message' => 'No tienes permisos para bloquear horarios'], 403);
            return;
        }
        
        $especialista_id = $this->post('especialista_id');
        $sucursal_id = $this->post('sucursal_id') ?: null;
        $fecha_inicio = $this->post('fecha_inicio');
        $fecha_fin = $this->post('fecha_fin');
        $tipo = $this->post('tipo');
        $motivo = $this->post('motivo') ?: null;
        
        // Validaciones
        if (!$especialista_id || !$fecha_inicio || !$fecha_fin || !$tipo) {
            $this->json(['success' => false, 'message' => 'Faltan datos obligatorios']);
            return;
        }
        
        // Verificar que el especialista pertenece al usuario actual
        $especialista = $this->db->fetch(
            "SELECT id FROM especialistas WHERE id = ? AND usuario_id = ?",
            [$especialista_id, $user['id']]
        );
        
        if (!$especialista) {
            $this->json(['success' => false, 'message' => 'Especialista no válido']);
            return;
        }
        
        // Validar que fecha_inicio < fecha_fin
        if (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
            $this->json(['success' => false, 'message' => 'La hora de inicio debe ser menor que la hora de fin']);
            return;
        }
        
        // Verificar que no hay reservaciones confirmadas en ese horario
        $existingReservations = $this->db->fetchAll(
            "SELECT id, codigo FROM reservaciones 
             WHERE especialista_id = ? 
             AND fecha_cita = DATE(?)
             AND estado IN ('confirmada', 'pendiente')
             AND (
                 (hora_inicio >= TIME(?) AND hora_inicio < TIME(?))
                 OR (hora_fin > TIME(?) AND hora_fin <= TIME(?))
                 OR (hora_inicio <= TIME(?) AND hora_fin >= TIME(?))
             )",
            [
                $especialista_id,
                $fecha_inicio,
                $fecha_inicio, $fecha_fin,
                $fecha_inicio, $fecha_fin,
                $fecha_inicio, $fecha_fin
            ]
        );
        
        if (!empty($existingReservations)) {
            $codigos = array_column($existingReservations, 'codigo');
            $this->json([
                'success' => false, 
                'message' => 'Ya tienes reservaciones en ese horario: ' . implode(', ', $codigos)
            ]);
            return;
        }
        
        // Insertar el bloqueo
        try {
            $this->db->insert(
                "INSERT INTO bloqueos_horario (especialista_id, sucursal_id, fecha_inicio, fecha_fin, tipo, motivo) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$especialista_id, $sucursal_id, $fecha_inicio, $fecha_fin, $tipo, $motivo]
            );
            
            $this->json(['success' => true, 'message' => 'Horario bloqueado exitosamente']);
        } catch (Exception $e) {
            error_log("Error al crear bloqueo: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error al bloquear horario']);
        }
    }
    
    /**
     * Eliminar bloqueo de horario
     */
    public function eliminarBloqueo() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        $user = currentUser();
        
        // Solo especialistas pueden eliminar sus propios bloqueos
        if ($user['rol_id'] != ROLE_SPECIALIST) {
            $this->json(['success' => false, 'message' => 'No tienes permisos para eliminar bloqueos'], 403);
            return;
        }
        
        $bloqueo_id = $this->post('bloqueo_id');
        
        if (!$bloqueo_id) {
            $this->json(['success' => false, 'message' => 'ID de bloqueo no proporcionado']);
            return;
        }
        
        // Verificar que el bloqueo pertenece al especialista actual
        $bloqueo = $this->db->fetch(
            "SELECT b.id FROM bloqueos_horario b
             JOIN especialistas e ON b.especialista_id = e.id
             WHERE b.id = ? AND e.usuario_id = ?",
            [$bloqueo_id, $user['id']]
        );
        
        if (!$bloqueo) {
            $this->json(['success' => false, 'message' => 'Bloqueo no encontrado o no tienes permisos']);
            return;
        }
        
        // Eliminar el bloqueo
        try {
            $this->db->delete("DELETE FROM bloqueos_horario WHERE id = ?", [$bloqueo_id]);
            $this->json(['success' => true, 'message' => 'Bloqueo eliminado exitosamente']);
        } catch (Exception $e) {
            error_log("Error al eliminar bloqueo: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error al eliminar el bloqueo']);
        }
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
