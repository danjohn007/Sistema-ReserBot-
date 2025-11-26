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
        
        // Obtener horario del especialista
        $schedule = $this->db->fetch(
            "SELECT * FROM horarios_especialistas WHERE especialista_id = ? AND dia_semana = ? AND activo = 1",
            [$especialista_id, $dayOfWeek]
        );
        
        if (!$schedule) {
            $this->json(['slots' => [], 'message' => 'El especialista no trabaja este día']);
        }
        
        // Verificar bloqueos
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
        
        // Obtener citas existentes
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
            
            if ($available && $fecha == date('Y-m-d') && $currentTime < time()) {
                $available = false;
            }
            
            if ($available) {
                $slots[] = [
                    'hora_inicio' => $slotStart,
                    'hora_fin' => $slotEnd,
                    'display' => formatTime($slotStart) . ' - ' . formatTime($slotEnd)
                ];
            }
            
            $currentTime += 30 * 60;
        }
        
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
                "SELECT s.*, es.precio_personalizado, es.duracion_personalizada,
                        c.nombre as categoria_nombre
                 FROM servicios s
                 JOIN especialistas_servicios es ON s.id = es.servicio_id
                 JOIN categorias_servicios c ON s.categoria_id = c.id
                 WHERE es.especialista_id = ? AND s.activo = 1
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
     * Obtener sucursales
     */
    public function branches() {
        $branches = $this->db->fetchAll(
            "SELECT id, nombre, direccion, ciudad, telefono, horario_apertura, horario_cierre
             FROM sucursales WHERE activo = 1 ORDER BY nombre"
        );
        
        $this->json(['branches' => $branches]);
    }
}
