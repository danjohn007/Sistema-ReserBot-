<?php
/**
 * ReserBot - Controlador de API
 */

require_once __DIR__ . '/BaseController.php';

class ApiController extends BaseController {
    
    /**
     * Obtener disponibilidad de horarios
     */
    public function availability() {
        $especialista_id = $this->get('especialista_id');
        $servicio_id = $this->get('servicio_id');
        $fecha = $this->get('fecha');
        $sucursal_id = $this->get('sucursal_id');
        
        if (!$especialista_id || !$servicio_id || !$fecha) {
            $this->json(['error' => 'Parámetros incompletos'], 400);
        }
        
        // Obtener duración del servicio
        $service = $this->db->fetch("SELECT duracion_minutos FROM servicios WHERE id = ?", [$servicio_id]);
        if (!$service) {
            $this->json(['error' => 'Servicio no encontrado'], 404);
        }
        
        $duracion = $service['duracion_minutos'];
        
        // Obtener día de la semana
        $dayOfWeek = date('N', strtotime($fecha));
        
        // Obtener horario del especialista para la sucursal específica
        // Nota: El especialista_id que recibimos ya es específico de la sucursal
        // porque cada registro en 'especialistas' combina usuario_id + sucursal_id
        $query = "SELECT he.* FROM horarios_especialistas he
                  JOIN especialistas e ON he.especialista_id = e.id
                  WHERE he.especialista_id = ? AND he.dia_semana = ? AND he.activo = 1";
        $params = [$especialista_id, $dayOfWeek];
        
        // Si se proporciona sucursal_id adicional, verificar que coincida
        if ($sucursal_id) {
            $query .= " AND e.sucursal_id = ?";
            $params[] = $sucursal_id;
        }
        
        $schedule = $this->db->fetch($query, $params);
        
        if (!$schedule) {
            $this->json(['slots' => [], 'message' => 'El especialista no trabaja este día']);
        }
        
        // Verificar bloqueos de fecha completa
        $block = $this->db->fetch(
            "SELECT id FROM bloqueos_horario 
             WHERE especialista_id = ? AND ? BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)",
            [$especialista_id, $fecha]
        );
        
        if ($block) {
            $this->json(['slots' => [], 'message' => 'El especialista no está disponible este día']);
        }
        
        // Verificar feriado
        if (isHoliday($fecha)) {
            $this->json(['slots' => [], 'message' => 'Este día es feriado']);
        }
        
        // Obtener citas existentes del especialista en la sucursal específica
        $queryAppointments = "SELECT hora_inicio, hora_fin FROM reservaciones 
             WHERE especialista_id = ? AND fecha_cita = ? AND estado NOT IN ('cancelada')";
        $paramsAppointments = [$especialista_id, $fecha];
        
        // Filtrar por sucursal si se proporciona
        if ($sucursal_id) {
            $queryAppointments .= " AND sucursal_id = ?";
            $paramsAppointments[] = $sucursal_id;
        }
        
        $existingAppointments = $this->db->fetchAll($queryAppointments, $paramsAppointments);
        
        // Función auxiliar para verificar si un slot está disponible
        $isSlotAvailable = function($slotStart, $slotEnd) use ($existingAppointments, $fecha) {
            // Verificar si está en el pasado
            if ($fecha == date('Y-m-d') && $slotStart < time()) {
                return false;
            }
            
            // Verificar conflictos con citas existentes
            foreach ($existingAppointments as $appt) {
                $apptStart = strtotime($appt['hora_inicio']);
                $apptEnd = strtotime($appt['hora_fin']);
                
                if (($slotStart >= $apptStart && $slotStart < $apptEnd) ||
                    ($slotEnd > $apptStart && $slotEnd <= $apptEnd) ||
                    ($slotStart <= $apptStart && $slotEnd >= $apptEnd)) {
                    return false;
                }
            }
            
            return true;
        };
        
        // Generar slots disponibles
        $slots = [];
        
        // 1. Slots del horario normal (excluyendo horario de bloqueo)
        $currentTime = strtotime($schedule['hora_inicio']);
        $endTime = strtotime($schedule['hora_fin']);
        
        // Si hay bloqueo activo, necesitamos dividir en dos rangos
        if ($schedule['bloqueo_activo'] && $schedule['hora_inicio_bloqueo'] && $schedule['hora_fin_bloqueo']) {
            $blockStart = strtotime($schedule['hora_inicio_bloqueo']);
            $blockEnd = strtotime($schedule['hora_fin_bloqueo']);
            
            // Rango 1: Desde inicio hasta inicio de bloqueo
            while ($currentTime + ($duracion * 60) <= $blockStart) {
                $slotStart = $currentTime;
                $slotEnd = $currentTime + ($duracion * 60);
                
                if ($isSlotAvailable($slotStart, $slotEnd)) {
                    $slots[] = [
                        'hora_inicio' => date('H:i:s', $slotStart),
                        'hora_fin' => date('H:i:s', $slotEnd),
                        'display' => formatTime(date('H:i:s', $slotStart)) . ' - ' . formatTime(date('H:i:s', $slotEnd)),
                        'tipo' => 'normal'
                    ];
                }
                
                $currentTime += 30 * 60; // Intervalo de 30 minutos
            }
            
            // Rango 2: Desde fin de bloqueo hasta fin del horario
            $currentTime = $blockEnd;
            while ($currentTime + ($duracion * 60) <= $endTime) {
                $slotStart = $currentTime;
                $slotEnd = $currentTime + ($duracion * 60);
                
                if ($isSlotAvailable($slotStart, $slotEnd)) {
                    $slots[] = [
                        'hora_inicio' => date('H:i:s', $slotStart),
                        'hora_fin' => date('H:i:s', $slotEnd),
                        'display' => formatTime(date('H:i:s', $slotStart)) . ' - ' . formatTime(date('H:i:s', $slotEnd)),
                        'tipo' => 'normal'
                    ];
                }
                
                $currentTime += 30 * 60;
            }
        } else {
            // Sin bloqueo: rango continuo
            while ($currentTime + ($duracion * 60) <= $endTime) {
                $slotStart = $currentTime;
                $slotEnd = $currentTime + ($duracion * 60);
                
                if ($isSlotAvailable($slotStart, $slotEnd)) {
                    $slots[] = [
                        'hora_inicio' => date('H:i:s', $slotStart),
                        'hora_fin' => date('H:i:s', $slotEnd),
                        'display' => formatTime(date('H:i:s', $slotStart)) . ' - ' . formatTime(date('H:i:s', $slotEnd)),
                        'tipo' => 'normal'
                    ];
                }
                
                $currentTime += 30 * 60;
            }
        }
        
        // 2. Slots del horario de emergencia (si está activo)
        if ($schedule['emergencia_activa'] && $schedule['hora_inicio_emergencia'] && $schedule['hora_fin_emergencia']) {
            $emergencyStart = strtotime($schedule['hora_inicio_emergencia']);
            $emergencyEnd = strtotime($schedule['hora_fin_emergencia']);
            
            $currentTime = $emergencyStart;
            while ($currentTime + ($duracion * 60) <= $emergencyEnd) {
                $slotStart = $currentTime;
                $slotEnd = $currentTime + ($duracion * 60);
                
                if ($isSlotAvailable($slotStart, $slotEnd)) {
                    $slots[] = [
                        'hora_inicio' => date('H:i:s', $slotStart),
                        'hora_fin' => date('H:i:s', $slotEnd),
                        'display' => '🚨 ' . formatTime(date('H:i:s', $slotStart)) . ' - ' . formatTime(date('H:i:s', $slotEnd)) . ' (Emergencia)',
                        'tipo' => 'emergencia'
                    ];
                }
                
                $currentTime += 30 * 60;
            }
        }
        
        // Ordenar slots por hora
        usort($slots, function($a, $b) {
            return strcmp($a['hora_inicio'], $b['hora_inicio']);
        });
        
        $this->json(['slots' => $slots]);
    }
    
    /**
     * Obtener especialistas de una sucursal
     */
    public function specialists() {
        $sucursal_id = $this->get('sucursal_id');
        
        if (!$sucursal_id) {
            $this->json(['error' => 'Sucursal no especificada'], 400);
        }
        
        $specialists = $this->db->fetchAll(
            "SELECT e.id, e.profesion, e.especialidad, e.calificacion_promedio,
                    u.nombre, u.apellidos
             FROM especialistas e
             JOIN usuarios u ON e.usuario_id = u.id
             WHERE e.sucursal_id = ? AND e.activo = 1
             ORDER BY u.nombre, u.apellidos",
            [$sucursal_id]
        );
        
        $this->json(['specialists' => $specialists]);
    }
    
    /**
     * Obtener servicios de un especialista
     */
    public function services() {
        $especialista_id = $this->get('especialista_id');
        
        if ($especialista_id) {
            $services = $this->db->fetchAll(
                "SELECT s.id, s.nombre, s.descripcion, s.duracion_minutos, s.precio,
                        COALESCE(es.precio_personalizado, s.precio) as precio,
                        COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos,
                        c.nombre as categoria_nombre
                 FROM servicios s
                 JOIN especialistas_servicios es ON s.id = es.servicio_id
                 JOIN categorias_servicios c ON s.categoria_id = c.id
                 WHERE es.especialista_id = ? AND s.activo = 1 AND es.activo = 1
                 ORDER BY c.nombre, s.nombre",
                [$especialista_id]
            );
        } else {
            $services = $this->db->fetchAll(
                "SELECT s.*, c.nombre as categoria_nombre
                 FROM servicios s
                 JOIN categorias_servicios c ON s.categoria_id = c.id
                 WHERE s.activo = 1
                 ORDER BY c.nombre, s.nombre"
            );
        }
        
        $this->json(['services' => $services]);
    }
    
    /**
     * Obtener especialista_id según usuario_id y sucursal_id
     * Para especialistas que trabajan en múltiples sucursales
     */
    public function getSpecialistBranch() {
        $usuario_id = $this->get('usuario_id');
        $sucursal_id = $this->get('sucursal_id');
        
        if (!$usuario_id || !$sucursal_id) {
            $this->json(['error' => 'Parámetros incompletos'], 400);
        }
        
        $specialist = $this->db->fetch(
            "SELECT id FROM especialistas WHERE usuario_id = ? AND sucursal_id = ? AND activo = 1",
            [$usuario_id, $sucursal_id]
        );
        
        if ($specialist) {
            $this->json(['especialista_id' => $specialist['id']]);
        } else {
            $this->json(['error' => 'Especialista no encontrado para esta sucursal'], 404);
        }
    }
    
    /**
     * Obtener sucursales
     */
    public function branches() {
        $branches = $this->db->fetchAll(
            "SELECT id, nombre, direccion, ciudad, telefono, horario_apertura, horario_cierre
             FROM sucursales WHERE activo = 1 ORDER BY nombre"
        );
        
        $this->json(['branches' => $branches]);
    }
    
    /**
     * Crear sucursal vía API
     */
    public function createBranch() {
        $this->requireAuth();
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        // Leer datos JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            $this->json(['success' => false, 'message' => 'El nombre es obligatorio'], 400);
        }
        
        try {
            $id = $this->db->insert(
                "INSERT INTO sucursales (nombre, direccion, ciudad, estado, codigo_postal, telefono, email, horario_apertura, horario_cierre, activo) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)",
                [
                    $data['nombre'],
                    $data['direccion'] ?? null,
                    $data['ciudad'] ?? null,
                    $data['estado'] ?? null,
                    $data['codigo_postal'] ?? null,
                    $data['telefono'] ?? null,
                    $data['email'] ?? null,
                    $data['horario_apertura'] ?? '08:00',
                    $data['horario_cierre'] ?? '20:00'
                ]
            );
            
            logAction('branch_create', 'Sucursal creada vía API: ' . $data['nombre']);
            $this->json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al crear la sucursal'], 500);
        }
    }
    
    /**
     * Crear servicio vía API
     */
    public function createService() {
        $this->requireAuth();
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        // Leer datos JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            $this->json(['success' => false, 'message' => 'El nombre es obligatorio'], 400);
        }
        
        if (!isset($data['categoria_id']) || empty($data['categoria_id'])) {
            $this->json(['success' => false, 'message' => 'La categoría es obligatoria'], 400);
        }
        
        try {
            $id = $this->db->insert(
                "INSERT INTO servicios (categoria_id, nombre, descripcion, duracion_minutos, precio, precio_oferta, activo) 
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [
                    $data['categoria_id'],
                    $data['nombre'],
                    $data['descripcion'] ?? null,
                    $data['duracion_minutos'] ?? 30,
                    $data['precio'] ?? 0,
                    $data['precio_oferta'] ?? null
                ]
            );
            
            logAction('service_create', 'Servicio creado vía API: ' . $data['nombre']);
            $this->json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al crear el servicio'], 500);
        }
    }
}
