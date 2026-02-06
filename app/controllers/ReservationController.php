<?php
/**
 * ReserBot - Controlador de Reservaciones
 */

require_once __DIR__ . '/BaseController.php';

class ReservationController extends BaseController {
    
    /**
     * Lista de reservaciones
     */
    public function index() {
        $this->requireAuth();
        
        $user = currentUser();
        $filters = [];
        
        // Filtros
        $estado = $this->get('estado');
        $fecha = $this->get('fecha');
        $sucursal_id = $this->get('sucursal_id');
        
        $sql = "SELECT r.*, 
                       COALESCE(CONCAT(u.nombre, ' ', u.apellidos), r.nombre_cliente, 'Cliente sin registro') as cliente_nombre_completo,
                       u.nombre as cliente_nombre, u.apellidos as cliente_apellidos, u.email as cliente_email, u.telefono as cliente_telefono,
                       s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                       ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
                FROM reservaciones r
                LEFT JOIN usuarios u ON r.cliente_id = u.id
                JOIN servicios s ON r.servicio_id = s.id
                JOIN sucursales suc ON r.sucursal_id = suc.id
                JOIN especialistas e ON r.especialista_id = e.id
                JOIN usuarios ue ON e.usuario_id = ue.id
                WHERE 1=1";
        
        // Aplicar filtros según rol
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN || $user['rol_id'] == ROLE_RECEPTIONIST) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $user['sucursal_id'];
        } elseif ($user['rol_id'] == ROLE_SPECIALIST) {
            $specialist = $this->db->fetch("SELECT id FROM especialistas WHERE usuario_id = ?", [$user['id']]);
            if ($specialist) {
                $sql .= " AND r.especialista_id = ?";
                $filters[] = $specialist['id'];
            }
        } elseif ($user['rol_id'] == ROLE_CLIENT) {
            $sql .= " AND r.cliente_id = ?";
            $filters[] = $user['id'];
        }
        
        if ($estado) {
            $sql .= " AND r.estado = ?";
            $filters[] = $estado;
        }
        
        if ($fecha) {
            $sql .= " AND r.fecha_cita = ?";
            $filters[] = $fecha;
        }
        
        if ($sucursal_id && $user['rol_id'] == ROLE_SUPERADMIN) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $sucursal_id;
        }
        
        $sql .= " ORDER BY r.fecha_cita DESC, r.hora_inicio DESC";
        
        $reservations = $this->db->fetchAll($sql, $filters);
        
        // Obtener sucursales para filtro
        $branches = [];
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        }
        
        $this->render('reservations/index', [
            'title' => 'Reservaciones',
            'reservations' => $reservations,
            'branches' => $branches,
            'currentFilters' => [
                'estado' => $estado,
                'fecha' => $fecha,
                'sucursal_id' => $sucursal_id
            ]
        ]);
    }
    
    /**
     * Crear reservación
     */
    public function create() {
        $this->requireAuth();
        
        $user = currentUser();
        $error = '';
        
        // Obtener sucursales según rol
        $branches = [];
        $currentSpecialistId = null;
        $currentSpecialistBranchId = null;
        
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            // Para especialistas: obtener solo sus sucursales
            $specialistRecords = $this->db->fetchAll(
                "SELECT e.id, e.sucursal_id, s.nombre, s.direccion 
                 FROM especialistas e 
                 JOIN sucursales s ON e.sucursal_id = s.id 
                 WHERE e.usuario_id = ? AND e.activo = 1
                 ORDER BY s.nombre",
                [$user['id']]
            );
            
            foreach ($specialistRecords as $record) {
                $branches[] = [
                    'id' => $record['sucursal_id'],
                    'nombre' => $record['nombre'],
                    'direccion' => $record['direccion']
                ];
            }
            
            // Si tiene sucursales, usar la primera por defecto
            if (!empty($specialistRecords)) {
                $currentSpecialistBranchId = $specialistRecords[0]['sucursal_id'];
                $currentSpecialistId = $specialistRecords[0]['id'];
            }
            
            // Crear mapeo de sucursal_id a especialista_id
            $branchSpecialistMap = [];
            foreach ($specialistRecords as $record) {
                $branchSpecialistMap[$record['sucursal_id']] = $record['id'];
            }
        } else {
            // Para otros roles: obtener todas las sucursales
            $branches = $this->db->fetchAll("SELECT id, nombre, direccion FROM sucursales WHERE activo = 1 ORDER BY nombre");
        }
        
        // Obtener clientes (si no es cliente ni especialista)
        $clients = [];
        if ($user['rol_id'] != ROLE_CLIENT && $user['rol_id'] != ROLE_SPECIALIST) {
            $clients = $this->db->fetchAll(
                "SELECT id, nombre, apellidos, email, telefono FROM usuarios 
                 WHERE rol_id = ? AND activo = 1 
                 ORDER BY nombre, apellidos",
                [ROLE_CLIENT]
            );
        }
        
        // Paso actual del wizard
        $step = $this->get('step') ?: 1;
        $sucursal_id = $this->get('sucursal_id');
        $especialista_id = $this->get('especialista_id');
        $servicio_id = $this->get('servicio_id');
        $fecha = $this->get('fecha');
        $hora = $this->get('hora');
        
        $specialists = [];
        $services = [];
        $availableSlots = [];
        
        // Para especialistas: cargar servicios si ya tiene sucursal por defecto
        if ($user['rol_id'] == ROLE_SPECIALIST && $currentSpecialistId) {
            $especialista_id = $currentSpecialistId;
            // Obtener servicios del especialista
            $services = $this->db->fetchAll(
                "SELECT s.*, es.precio_personalizado, es.duracion_personalizada,
                 COALESCE(es.precio_personalizado, s.precio) as precio,
                 COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos
                 FROM servicios s
                 JOIN especialistas_servicios es ON s.id = es.servicio_id
                 WHERE es.especialista_id = ? AND s.activo = 1 AND es.activo = 1
                 ORDER BY s.nombre",
                [$especialista_id]
            );
        }
        
        if ($sucursal_id && $user['rol_id'] != ROLE_SPECIALIST) {
            // Obtener especialistas de la sucursal (solo para no-especialistas)
            $specialists = $this->db->fetchAll(
                "SELECT e.*, u.nombre, u.apellidos, e.profesion
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 WHERE e.sucursal_id = ? AND e.activo = 1
                 ORDER BY u.nombre, u.apellidos",
                [$sucursal_id]
            );
        }
        
        if ($especialista_id && $user['rol_id'] != ROLE_SPECIALIST) {
            // Obtener servicios del especialista
            $services = $this->db->fetchAll(
                "SELECT s.*, es.precio_personalizado, es.duracion_personalizada,
                 COALESCE(es.precio_personalizado, s.precio) as precio,
                 COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos
                 FROM servicios s
                 JOIN especialistas_servicios es ON s.id = es.servicio_id
                 WHERE es.especialista_id = ? AND s.activo = 1 AND es.activo = 1
                 ORDER BY s.nombre",
                [$especialista_id]
            );
        }
        
        if ($especialista_id && $servicio_id && $fecha) {
            // Obtener horarios disponibles
            $availableSlots = $this->getAvailableSlots($especialista_id, $servicio_id, $fecha);
        }
        
        if ($this->isPost()) {
            // Obtener datos del formulario
            $nombre_cliente = $this->post('nombre_cliente'); // Para especialistas
            $cliente_id = ($user['rol_id'] == ROLE_CLIENT) ? $user['id'] : $this->post('cliente_id');
            $sucursal_id = $this->post('sucursal_id');
            $especialista_id = $this->post('especialista_id');
            $servicio_id = $this->post('servicio_id');
            $fecha_cita = $this->post('fecha_cita');
            $hora_inicio = $this->post('hora_inicio');
            $notas_cliente = $this->post('notas_cliente');
            
            // Validar campos obligatorios
            if (empty($sucursal_id) || empty($especialista_id) || empty($servicio_id) || empty($fecha_cita) || empty($hora_inicio)) {
                $error = 'Todos los campos son obligatorios.';
            } else {
                // Si es especialista y proporcionó nombre_cliente, guardarlo
                if ($user['rol_id'] == ROLE_SPECIALIST && !empty($nombre_cliente)) {
                    // Cliente_id será NULL, pero guardaremos el nombre en nombre_cliente
                    $cliente_id = null;
                } elseif (empty($cliente_id) && empty($nombre_cliente)) {
                    $error = 'Debe proporcionar un cliente o nombre de cliente.';
                }
                
                if (!$error) {
                    // Obtener información del servicio
                    $service = $this->db->fetch(
                        "SELECT s.*, 
                         COALESCE(es.precio_personalizado, s.precio) as precio,
                         COALESCE(es.duracion_personalizada, s.duracion_minutos) as duracion_minutos
                         FROM servicios s
                         LEFT JOIN especialistas_servicios es ON s.id = es.servicio_id AND es.especialista_id = ?
                         WHERE s.id = ?",
                        [$especialista_id, $servicio_id]
                    );
                    
                    if ($service) {
                        $duracion = $service['duracion_minutos'];
                        $precio = $service['precio'];
                        $hora_fin = date('H:i:s', strtotime($hora_inicio) + ($duracion * 60));
                        
                        // Verificar disponibilidad
                        $conflict = $this->db->fetch(
                            "SELECT id FROM reservaciones 
                             WHERE especialista_id = ? AND fecha_cita = ? AND estado NOT IN ('cancelada')
                             AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))",
                            [$especialista_id, $fecha_cita, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin]
                        );
                        
                        if ($conflict) {
                            $error = 'El horario seleccionado ya no está disponible. Por favor seleccione otro.';
                        } else {
                            // Generar código único
                            $codigo = generateReservationCode();
                            
                            // Determinar estado inicial
                            $estado = (getConfig('confirmacion_automatica', '0') == '1') ? 'confirmada' : 'pendiente';
                            
                            // Crear reservación
                            $reservationId = $this->db->insert(
                                "INSERT INTO reservaciones (codigo, cliente_id, nombre_cliente, especialista_id, servicio_id, sucursal_id, 
                                 fecha_cita, hora_inicio, hora_fin, duracion_minutos, precio_total, estado, notas_cliente, creado_por) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                                [$codigo, $cliente_id, $nombre_cliente, $especialista_id, $servicio_id, $sucursal_id, 
                                 $fecha_cita, $hora_inicio, $hora_fin, $duracion, $precio, $estado, $notas_cliente, $user['id']]
                            );
                            
                            if ($reservationId) {
                                // Crear notificación solo si hay cliente_id
                                if ($cliente_id) {
                                    $this->createNotification($cliente_id, 'cita_nueva', 
                                        'Nueva cita programada', 
                                        "Se ha programado una cita para el " . formatDate($fecha_cita) . " a las " . formatTime($hora_inicio));
                                }
                                
                                logAction('reservation_create', 'Reservación creada: ' . $codigo);
                                setFlashMessage('success', 'Reservación creada exitosamente. Código: ' . $codigo);
                                redirect('/reservaciones');
                            } else {
                                $error = 'Error al crear la reservación.';
                            }
                        }
                    }
                }
            }
        }
        
        $this->render('reservations/create', [
            'title' => 'Nueva Reservación',
            'branches' => $branches,
            'clients' => $clients,
            'specialists' => $specialists,
            'services' => $services,
            'availableSlots' => $availableSlots,
            'step' => $step,
            'selectedBranch' => $sucursal_id ?: $currentSpecialistBranchId,
            'selectedSpecialist' => $especialista_id,
            'selectedService' => $servicio_id,
            'selectedDate' => $fecha,
            'currentSpecialistId' => $currentSpecialistId,
            'branchSpecialistMap' => $branchSpecialistMap ?? [],
            'user' => $user,
            'error' => $error
        ]);
    }
    
    /**
     * Ver detalle de reservación
     */
    public function view() {
        $this->requireAuth();
        
        $id = $this->get('id');
        
        $reservation = $this->db->fetch(
            "SELECT r.*, 
                    COALESCE(CONCAT(u.nombre, ' ', u.apellidos), r.nombre_cliente, 'Cliente sin registro') as cliente_nombre_completo,
                    u.nombre as cliente_nombre, u.apellidos as cliente_apellidos, u.email as cliente_email, u.telefono as cliente_telefono,
                    s.nombre as servicio_nombre, s.descripcion as servicio_descripcion,
                    suc.nombre as sucursal_nombre, suc.direccion as sucursal_direccion, suc.telefono as sucursal_telefono,
                    ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             LEFT JOIN usuarios u ON r.cliente_id = u.id
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.id = ?",
            [$id]
        );
        
        if (!$reservation) {
            setFlashMessage('error', 'Reservación no encontrada.');
            redirect('/reservaciones');
        }
        
        // Verificar permisos
        $user = currentUser();
        if ($user['rol_id'] == ROLE_CLIENT && $reservation['cliente_id'] != $user['id']) {
            setFlashMessage('error', 'No tiene permisos para ver esta reservación.');
            redirect('/reservaciones');
        }
        
        $this->render('reservations/view', [
            'title' => 'Reservación ' . $reservation['codigo'],
            'reservation' => $reservation
        ]);
    }
    
    /**
     * Confirmar reservación
     */
    public function confirm() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST, ROLE_RECEPTIONIST]);
        
        $id = $this->get('id');
        
        $reservation = $this->db->fetch("SELECT * FROM reservaciones WHERE id = ?", [$id]);
        
        if ($reservation && $reservation['estado'] == 'pendiente') {
            $this->db->update("UPDATE reservaciones SET estado = 'confirmada' WHERE id = ?", [$id]);
            
            // Notificar al cliente
            $this->createNotification($reservation['cliente_id'], 'cita_confirmada', 
                'Cita confirmada', 
                "Su cita del " . formatDate($reservation['fecha_cita']) . " ha sido confirmada.");
            
            logAction('reservation_confirm', 'Reservación confirmada: ' . $reservation['codigo']);
            setFlashMessage('success', 'Reservación confirmada.');
        }
        
        redirect('/reservaciones/ver?id=' . $id);
    }
    
    /**
     * Cancelar reservación
     */
    public function cancel() {
        $this->requireAuth();
        
        $id = $this->get('id');
        $user = currentUser();
        
        $reservation = $this->db->fetch("SELECT * FROM reservaciones WHERE id = ?", [$id]);
        
        if (!$reservation) {
            setFlashMessage('error', 'Reservación no encontrada.');
            redirect('/reservaciones');
        }
        
        // Verificar permisos
        if ($user['rol_id'] == ROLE_CLIENT) {
            if ($reservation['cliente_id'] != $user['id']) {
                setFlashMessage('error', 'No tiene permisos para cancelar esta reservación.');
                redirect('/reservaciones');
            }
            
            // Verificar anticipación mínima
            $horasAnticipacion = getConfig('horas_anticipacion_cancelacion', 24);
            $fechaCita = strtotime($reservation['fecha_cita'] . ' ' . $reservation['hora_inicio']);
            if ($fechaCita - time() < ($horasAnticipacion * 3600)) {
                setFlashMessage('error', 'No puede cancelar con menos de ' . $horasAnticipacion . ' horas de anticipación.');
                redirect('/reservaciones/ver?id=' . $id);
            }
        }
        
        $motivo = $this->get('motivo') ?: 'Cancelada por el usuario';
        
        if ($reservation['estado'] != 'cancelada' && $reservation['estado'] != 'completada') {
            $this->db->update(
                "UPDATE reservaciones SET estado = 'cancelada', cancelado_por = ?, motivo_cancelacion = ?, fecha_cancelacion = NOW() WHERE id = ?",
                [$user['id'], $motivo, $id]
            );
            
            // Notificar al cliente
            $this->createNotification($reservation['cliente_id'], 'cita_cancelada', 
                'Cita cancelada', 
                "Su cita del " . formatDate($reservation['fecha_cita']) . " ha sido cancelada.");
            
            logAction('reservation_cancel', 'Reservación cancelada: ' . $reservation['codigo']);
            setFlashMessage('success', 'Reservación cancelada.');
        }
        
        redirect('/reservaciones');
    }
    
    /**
     * Reagendar una reservación (cambiar fecha y hora)
     */
    public function reagendar() {
        $this->requireAuth();
        
        // Obtener datos del POST
        $id = $this->post('id');
        $fecha_cita = $this->post('fecha_cita');
        $hora_inicio = $this->post('hora_inicio');
        
        if (!$id || !$fecha_cita || !$hora_inicio) {
            $this->json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        $user = currentUser();
        
        // Obtener la reservación actual
        $reservation = $this->db->fetch(
            "SELECT r.*, e.usuario_id as especialista_usuario_id, s.duracion_minutos 
             FROM reservaciones r
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN servicios s ON r.servicio_id = s.id
             WHERE r.id = ?",
            [$id]
        );
        
        if (!$reservation) {
            $this->json(['success' => false, 'message' => 'Reservación no encontrada'], 404);
        }
        
        // Verificar permisos
        $tienePermiso = false;
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $tienePermiso = true;
        } elseif ($user['rol_id'] == ROLE_BRANCH_ADMIN || $user['rol_id'] == ROLE_RECEPTIONIST) {
            $tienePermiso = ($reservation['sucursal_id'] == $user['sucursal_id']);
        } elseif ($user['rol_id'] == ROLE_SPECIALIST) {
            $tienePermiso = ($reservation['especialista_usuario_id'] == $user['id']);
        } elseif ($user['rol_id'] == ROLE_CLIENT) {
            $tienePermiso = ($reservation['cliente_id'] == $user['id']);
        }
        
        if (!$tienePermiso) {
            $this->json(['success' => false, 'message' => 'No tiene permisos para reagendar esta reservación'], 403);
        }
        
        // Verificar que la reservación puede ser reagendada
        if ($reservation['estado'] == 'cancelada' || $reservation['estado'] == 'completada') {
            $this->json(['success' => false, 'message' => 'No se puede reagendar una cita cancelada o completada'], 400);
        }
        
        // Calcular hora_fin basada en la duración del servicio
        $duracion = $reservation['duracion_minutos'];
        $horaInicio = strtotime($hora_inicio);
        $horaFin = date('H:i:s', $horaInicio + ($duracion * 60));
        
        // Verificar que la nueva fecha/hora no esté en el pasado
        $nuevaFechaHora = strtotime($fecha_cita . ' ' . $hora_inicio);
        if ($nuevaFechaHora < time()) {
            $this->json(['success' => false, 'message' => 'No puede agendar una cita en el pasado'], 400);
        }
        
        // Actualizar la reservación
        try {
            $this->db->update(
                "UPDATE reservaciones 
                 SET fecha_cita = ?, hora_inicio = ?, hora_fin = ?
                 WHERE id = ?",
                [$fecha_cita, $hora_inicio, $horaFin, $id]
            );
            
            // Notificar al cliente
            if ($reservation['cliente_id']) {
                $this->createNotification(
                    $reservation['cliente_id'], 
                    'cita_reagendada', 
                    'Cita reagendada', 
                    "Su cita ha sido reagendada para el " . formatDate($fecha_cita) . " a las " . formatTime($hora_inicio)
                );
            }
            
            // Log
            logAction('reservation_reschedule', 'Reservación reagendada: ' . $reservation['codigo'] . 
                      ' - Nueva fecha: ' . $fecha_cita . ' ' . $hora_inicio);
            
            $this->json([
                'success' => true, 
                'message' => 'Cita reagendada exitosamente',
                'data' => [
                    'fecha_cita' => $fecha_cita,
                    'hora_inicio' => $hora_inicio,
                    'hora_fin' => $horaFin
                ]
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al reagendar: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Mis citas (para clientes)
     */
    public function myAppointments() {
        $this->requireRole([ROLE_CLIENT]);
        
        $user = currentUser();
        
        $upcomingAppointments = $this->db->fetchAll(
            "SELECT r.*, s.nombre as servicio_nombre, suc.nombre as sucursal_nombre, suc.direccion as sucursal_direccion,
                    ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.cliente_id = ? AND r.fecha_cita >= CURDATE() AND r.estado NOT IN ('cancelada', 'completada')
             ORDER BY r.fecha_cita, r.hora_inicio",
            [$user['id']]
        );
        
        $pastAppointments = $this->db->fetchAll(
            "SELECT r.*, s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                    ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.cliente_id = ? AND (r.fecha_cita < CURDATE() OR r.estado IN ('cancelada', 'completada'))
             ORDER BY r.fecha_cita DESC, r.hora_inicio DESC
             LIMIT 20",
            [$user['id']]
        );
        
        $this->render('reservations/my-appointments', [
            'title' => 'Mis Citas',
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments
        ]);
    }
    
    /**
     * API para verificar disponibilidad
     */
    public function availability() {
        $especialista_id = $this->get('especialista_id');
        $servicio_id = $this->get('servicio_id');
        $fecha = $this->get('fecha');
        
        if (!$especialista_id || !$servicio_id || !$fecha) {
            $this->json(['error' => 'Parámetros incompletos'], 400);
        }
        
        $slots = $this->getAvailableSlots($especialista_id, $servicio_id, $fecha);
        
        $this->json(['slots' => $slots]);
    }
    
    /**
     * Obtiene los horarios disponibles
     */
    private function getAvailableSlots($especialista_id, $servicio_id, $fecha) {
        // Obtener duración del servicio
        $service = $this->db->fetch("SELECT duracion_minutos FROM servicios WHERE id = ?", [$servicio_id]);
        if (!$service) return [];
        
        $duracion = $service['duracion_minutos'];
        
        // Obtener día de la semana (1=Lunes, 7=Domingo)
        $dayOfWeek = date('N', strtotime($fecha));
        
        // Obtener horario del especialista para ese día
        $schedule = $this->db->fetch(
            "SELECT * FROM horarios_especialistas WHERE especialista_id = ? AND dia_semana = ? AND activo = 1",
            [$especialista_id, $dayOfWeek]
        );
        
        if (!$schedule) return [];
        
        // Verificar bloqueos
        $block = $this->db->fetch(
            "SELECT id FROM bloqueos_horario 
             WHERE especialista_id = ? AND ? BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)",
            [$especialista_id, $fecha]
        );
        
        if ($block) return [];
        
        // Obtener citas existentes para ese día
        $existingAppointments = $this->db->fetchAll(
            "SELECT hora_inicio, hora_fin FROM reservaciones 
             WHERE especialista_id = ? AND fecha_cita = ? AND estado NOT IN ('cancelada')",
            [$especialista_id, $fecha]
        );
        
        // Generar slots disponibles
        $slots = [];
        $currentTime = strtotime($schedule['hora_inicio']);
        $endTime = strtotime($schedule['hora_fin']);
        
        while ($currentTime + ($duracion * 60) <= $endTime) {
            $slotStart = date('H:i:s', $currentTime);
            $slotEnd = date('H:i:s', $currentTime + ($duracion * 60));
            
            $available = true;
            
            foreach ($existingAppointments as $appt) {
                $apptStart = strtotime($appt['hora_inicio']);
                $apptEnd = strtotime($appt['hora_fin']);
                
                if (($currentTime >= $apptStart && $currentTime < $apptEnd) ||
                    ($currentTime + ($duracion * 60) > $apptStart && $currentTime + ($duracion * 60) <= $apptEnd)) {
                    $available = false;
                    break;
                }
            }
            
            if ($available) {
                // No mostrar horarios pasados si es hoy
                if ($fecha == date('Y-m-d') && $currentTime < time()) {
                    $available = false;
                }
            }
            
            if ($available) {
                $slots[] = [
                    'hora_inicio' => $slotStart,
                    'hora_fin' => $slotEnd,
                    'display' => formatTime($slotStart) . ' - ' . formatTime($slotEnd)
                ];
            }
            
            $currentTime += 30 * 60; // Incrementar en intervalos de 30 minutos
        }
        
        return $slots;
    }
    
    /**
     * Crea una notificación
     */
    private function createNotification($userId, $tipo, $titulo, $mensaje) {
        // No crear notificación si el usuario_id es NULL (reservas desde chatbot)
        if ($userId === null || empty($userId)) {
            return;
        }
        
        $this->db->insert(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje) VALUES (?, ?, ?, ?)",
            [$userId, $tipo, $titulo, $mensaje]
        );
    }
}
