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
        $excluir_reservacion = $this->get('excluir_reservacion'); // Nueva línea
        
        if (!$especialista_id || !$servicio_id || !$fecha) {
            $this->json(['error' => 'Parámetros incompletos'], 400);
        }
        
        // Verificar si el servicio es de emergencia
        $servicioEspecialista = $this->db->fetch(
            "SELECT es.es_emergencia FROM especialistas_servicios es 
             WHERE es.especialista_id = ? AND es.servicio_id = ?",
            [$especialista_id, $servicio_id]
        );
        
        if (!$servicioEspecialista) {
            $this->json(['error' => 'El especialista no ofrece este servicio'], 404);
        }
        
        $esServicioEmergencia = $servicioEspecialista['es_emergencia'];
        
        // DEBUG: Log para verificar el tipo de servicio
        error_log("[AVAILABILITY DEBUG] Especialista: $especialista_id, Servicio: $servicio_id, es_emergencia DB: " . ($esServicioEmergencia ? '1' : '0'));
        
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
        
        // Excluir la reservación que estamos reagendando
        if ($excluir_reservacion) {
            $queryAppointments .= " AND id != ?";
            $paramsAppointments[] = $excluir_reservacion;
        }
        
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
        
        // Determinar qué tipo de slots generar según el tipo de servicio
        $generarSlotsNormales = !$esServicioEmergencia; // Servicios normales usan horario normal
        $generarSlotsEmergencia = $esServicioEmergencia; // Servicios de emergencia usan horario de emergencia
        
        // DEBUG: Log para verificar qué tipo de slots se generarán
        error_log("[AVAILABILITY DEBUG] Horario - Normal: " . ($schedule['hora_inicio'] ?? 'N/A') . "-" . ($schedule['hora_fin'] ?? 'N/A') . 
                  ", Emergencia: " . ($schedule['hora_inicio_emergencia'] ?? 'N/A') . "-" . ($schedule['hora_fin_emergencia'] ?? 'N/A') . 
                  ", Emergencia Activa: " . ($schedule['emergencia_activa'] ? 'SI' : 'NO'));
        error_log("[AVAILABILITY DEBUG] Generar Normales: " . ($generarSlotsNormales ? 'SI' : 'NO') . 
                  ", Generar Emergencia: " . ($generarSlotsEmergencia ? 'SI' : 'NO'));
        
        // 1. Slots del horario normal (excluyendo horario de bloqueo)
        if ($generarSlotsNormales) {
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
        } // Fin de generarSlotsNormales
        
        // 2. Slots del horario de emergencia (si está activo y el servicio es de emergencia)
        if ($generarSlotsEmergencia && $schedule['emergencia_activa'] && $schedule['hora_inicio_emergencia'] && $schedule['hora_fin_emergencia']) {
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
        
        // DEBUG: Log de resultados
        error_log("[AVAILABILITY DEBUG] Total slots generados: " . count($slots));
        
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
        $fecha = $this->get('fecha'); // Opcional: para filtrar por horario de emergencia
        $hora = $this->get('hora'); // Opcional: para filtrar por horario de emergencia
        
        if ($especialista_id) {
            // Determinar si estamos en horario de emergencia
            $es_horario_emergencia = false;
            
            if ($fecha && $hora) {
                $dia_semana = date('N', strtotime($fecha)); // 1=Lunes, 7=Domingo
                $hora_consulta = strtotime($hora);
                
                // Verificar si el especialista tiene horario de emergencia activo para ese día
                $horario = $this->db->fetch(
                    "SELECT hora_inicio, hora_fin, 
                            hora_inicio_emergencia, hora_fin_emergencia, emergencia_activa
                     FROM horarios_especialistas
                     WHERE especialista_id = ? AND dia_semana = ? AND activo = 1",
                    [$especialista_id, $dia_semana]
                );
                
                if ($horario && $horario['emergencia_activa']) {
                    $hora_inicio_normal = strtotime($horario['hora_inicio']);
                    $hora_fin_normal = strtotime($horario['hora_fin']);
                    $hora_inicio_emergencia = $horario['hora_inicio_emergencia'] ? strtotime($horario['hora_inicio_emergencia']) : null;
                    $hora_fin_emergencia = $horario['hora_fin_emergencia'] ? strtotime($horario['hora_fin_emergencia']) : null;
                    
                    // Si la hora está fuera del horario normal pero dentro del horario de emergencia
                    if ($hora_inicio_emergencia && $hora_fin_emergencia) {
                        if (($hora_consulta < $hora_inicio_normal || $hora_consulta >= $hora_fin_normal) &&
                            ($hora_consulta >= $hora_inicio_emergencia && $hora_consulta < $hora_fin_emergencia)) {
                            $es_horario_emergencia = true;
                        }
                    }
                }
            }
            
            // Filtrar servicios según si es horario de emergencia o normal
            // Si no se proporciona fecha/hora, mostrar TODOS los servicios disponibles
            if ($fecha && $hora) {
                // Filtrar por tipo de horario
                $services = $this->db->fetchAll(
                    "SELECT s.id, s.nombre, s.descripcion, s.duracion_minutos, s.precio,
                            COALESCE(es.precio_personalizado, s.precio) as precio,
                            COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos,
                            c.nombre as categoria_nombre,
                            es.es_emergencia
                     FROM servicios s
                     JOIN especialistas_servicios es ON s.id = es.servicio_id
                     JOIN categorias_servicios c ON s.categoria_id = c.id
                     WHERE es.especialista_id = ? AND s.activo = 1 AND es.activo = 1 AND es.es_emergencia = ?
                     ORDER BY c.nombre, s.nombre",
                    [$especialista_id, $es_horario_emergencia ? 1 : 0]
                );
            } else {
                // Mostrar todos los servicios (normal y emergencia)
                $services = $this->db->fetchAll(
                    "SELECT s.id, s.nombre, s.descripcion, s.duracion_minutos, s.precio,
                            COALESCE(es.precio_personalizado, s.precio) as precio,
                            COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos,
                            c.nombre as categoria_nombre,
                            es.es_emergencia
                     FROM servicios s
                     JOIN especialistas_servicios es ON s.id = es.servicio_id
                     JOIN categorias_servicios c ON s.categoria_id = c.id
                     WHERE es.especialista_id = ? AND s.activo = 1 AND es.activo = 1
                     ORDER BY es.es_emergencia DESC, c.nombre, s.nombre",
                    [$especialista_id]
                );
            }
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
     * Obtener sucursal del especialista según el día de la semana
     */
    public function getSpecialistBranchByDay() {
        $usuario_id = $this->get('usuario_id');
        $dia_semana = $this->get('dia_semana'); // 1-7 (lunes-domingo)
        
        if (!$usuario_id || !$dia_semana) {
            $this->json(['error' => 'Parámetros incompletos'], 400);
        }
        
        // Buscar en qué sucursal trabaja el especialista ese día
        $result = $this->db->fetch(
            "SELECT e.id as especialista_id, e.sucursal_id, s.nombre as sucursal_nombre
             FROM especialistas e
             INNER JOIN horarios_especialistas he ON he.especialista_id = e.id
             INNER JOIN sucursales s ON s.id = e.sucursal_id
             WHERE e.usuario_id = ? AND he.dia_semana = ? AND e.activo = 1
             LIMIT 1",
            [$usuario_id, $dia_semana]
        );
        
        if ($result) {
            $this->json([
                'especialista_id' => $result['especialista_id'],
                'sucursal_id' => $result['sucursal_id'],
                'sucursal_nombre' => $result['sucursal_nombre']
            ]);
        } else {
            $this->json(['error' => 'No se encontró horario para este día'], 404);
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
