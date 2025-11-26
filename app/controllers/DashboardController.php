<?php
/**
 * ReserBot - Controlador del Dashboard
 */

require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    
    /**
     * Dashboard principal
     */
    public function index() {
        $this->requireAuth();
        
        $user = currentUser();
        $data = [
            'title' => 'Dashboard',
            'user' => $user
        ];
        
        // Obtener estadísticas según el rol
        switch ($user['rol_id']) {
            case ROLE_SUPERADMIN:
                $data = array_merge($data, $this->getSuperadminStats());
                break;
            case ROLE_BRANCH_ADMIN:
                $data = array_merge($data, $this->getBranchAdminStats($user['sucursal_id']));
                break;
            case ROLE_SPECIALIST:
                $data = array_merge($data, $this->getSpecialistStats($user['id']));
                break;
            case ROLE_RECEPTIONIST:
                $data = array_merge($data, $this->getReceptionistStats($user['sucursal_id']));
                break;
            case ROLE_CLIENT:
                $data = array_merge($data, $this->getClientStats($user['id']));
                break;
        }
        
        $this->render('dashboard/index', $data);
    }
    
    /**
     * Estadísticas para Superadmin
     */
    private function getSuperadminStats() {
        // Total de sucursales
        $totalBranches = $this->db->fetch("SELECT COUNT(*) as total FROM sucursales WHERE activo = 1")['total'];
        
        // Total de especialistas
        $totalSpecialists = $this->db->fetch("SELECT COUNT(*) as total FROM especialistas WHERE activo = 1")['total'];
        
        // Total de clientes
        $totalClients = $this->db->fetch("SELECT COUNT(*) as total FROM usuarios WHERE rol_id = ? AND activo = 1", [ROLE_CLIENT])['total'];
        
        // Citas de hoy
        $todayAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones WHERE fecha_cita = CURDATE()"
        )['total'];
        
        // Citas pendientes
        $pendingAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones WHERE estado = 'pendiente'"
        )['total'];
        
        // Ingresos del mes
        $monthlyIncome = $this->db->fetch(
            "SELECT COALESCE(SUM(precio_total), 0) as total FROM reservaciones 
             WHERE estado = 'completada' AND MONTH(fecha_cita) = MONTH(CURDATE()) AND YEAR(fecha_cita) = YEAR(CURDATE())"
        )['total'];
        
        // Próximas citas
        $upcomingAppointments = $this->db->fetchAll(
            "SELECT r.*, u.nombre as cliente_nombre, u.apellidos as cliente_apellidos,
                    s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                    e.id as esp_id, ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN usuarios u ON r.cliente_id = u.id
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.fecha_cita >= CURDATE() AND r.estado IN ('pendiente', 'confirmada')
             ORDER BY r.fecha_cita, r.hora_inicio
             LIMIT 10"
        );
        
        // Estadísticas por mes (últimos 6 meses)
        $monthlyStats = $this->db->fetchAll(
            "SELECT DATE_FORMAT(fecha_cita, '%Y-%m') as mes,
                    COUNT(*) as total_citas,
                    SUM(CASE WHEN estado = 'completada' THEN precio_total ELSE 0 END) as ingresos
             FROM reservaciones
             WHERE fecha_cita >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(fecha_cita, '%Y-%m')
             ORDER BY mes"
        );
        
        return [
            'totalBranches' => $totalBranches,
            'totalSpecialists' => $totalSpecialists,
            'totalClients' => $totalClients,
            'todayAppointments' => $todayAppointments,
            'pendingAppointments' => $pendingAppointments,
            'monthlyIncome' => $monthlyIncome,
            'upcomingAppointments' => $upcomingAppointments,
            'monthlyStats' => $monthlyStats
        ];
    }
    
    /**
     * Estadísticas para Admin de Sucursal
     */
    private function getBranchAdminStats($branchId) {
        // Total de especialistas en la sucursal
        $totalSpecialists = $this->db->fetch(
            "SELECT COUNT(*) as total FROM especialistas WHERE sucursal_id = ? AND activo = 1",
            [$branchId]
        )['total'];
        
        // Citas de hoy
        $todayAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones WHERE sucursal_id = ? AND fecha_cita = CURDATE()",
            [$branchId]
        )['total'];
        
        // Citas pendientes
        $pendingAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones WHERE sucursal_id = ? AND estado = 'pendiente'",
            [$branchId]
        )['total'];
        
        // Ingresos del mes
        $monthlyIncome = $this->db->fetch(
            "SELECT COALESCE(SUM(precio_total), 0) as total FROM reservaciones 
             WHERE sucursal_id = ? AND estado = 'completada' 
             AND MONTH(fecha_cita) = MONTH(CURDATE()) AND YEAR(fecha_cita) = YEAR(CURDATE())",
            [$branchId]
        )['total'];
        
        // Próximas citas
        $upcomingAppointments = $this->db->fetchAll(
            "SELECT r.*, u.nombre as cliente_nombre, u.apellidos as cliente_apellidos,
                    s.nombre as servicio_nombre,
                    e.id as esp_id, ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN usuarios u ON r.cliente_id = u.id
             JOIN servicios s ON r.servicio_id = s.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.sucursal_id = ? AND r.fecha_cita >= CURDATE() AND r.estado IN ('pendiente', 'confirmada')
             ORDER BY r.fecha_cita, r.hora_inicio
             LIMIT 10",
            [$branchId]
        );
        
        // Info de la sucursal
        $branch = $this->db->fetch("SELECT * FROM sucursales WHERE id = ?", [$branchId]);
        
        return [
            'branch' => $branch,
            'totalSpecialists' => $totalSpecialists,
            'todayAppointments' => $todayAppointments,
            'pendingAppointments' => $pendingAppointments,
            'monthlyIncome' => $monthlyIncome,
            'upcomingAppointments' => $upcomingAppointments
        ];
    }
    
    /**
     * Estadísticas para Especialista
     */
    private function getSpecialistStats($userId) {
        // Obtener el especialista
        $specialist = $this->db->fetch(
            "SELECT * FROM especialistas WHERE usuario_id = ?",
            [$userId]
        );
        
        if (!$specialist) {
            return ['specialist' => null, 'upcomingAppointments' => []];
        }
        
        // Citas de hoy
        $todayAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones 
             WHERE especialista_id = ? AND fecha_cita = CURDATE()",
            [$specialist['id']]
        )['total'];
        
        // Citas pendientes
        $pendingAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones 
             WHERE especialista_id = ? AND estado = 'pendiente'",
            [$specialist['id']]
        )['total'];
        
        // Próximas citas
        $upcomingAppointments = $this->db->fetchAll(
            "SELECT r.*, u.nombre as cliente_nombre, u.apellidos as cliente_apellidos,
                    u.telefono as cliente_telefono, s.nombre as servicio_nombre
             FROM reservaciones r
             JOIN usuarios u ON r.cliente_id = u.id
             JOIN servicios s ON r.servicio_id = s.id
             WHERE r.especialista_id = ? AND r.fecha_cita >= CURDATE() 
             AND r.estado IN ('pendiente', 'confirmada')
             ORDER BY r.fecha_cita, r.hora_inicio
             LIMIT 10",
            [$specialist['id']]
        );
        
        return [
            'specialist' => $specialist,
            'todayAppointments' => $todayAppointments,
            'pendingAppointments' => $pendingAppointments,
            'upcomingAppointments' => $upcomingAppointments
        ];
    }
    
    /**
     * Estadísticas para Recepcionista
     */
    private function getReceptionistStats($branchId) {
        return $this->getBranchAdminStats($branchId);
    }
    
    /**
     * Estadísticas para Cliente
     */
    private function getClientStats($userId) {
        // Próximas citas
        $upcomingAppointments = $this->db->fetchAll(
            "SELECT r.*, s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                    suc.direccion as sucursal_direccion, suc.telefono as sucursal_telefono,
                    e.id as esp_id, ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.cliente_id = ? AND r.fecha_cita >= CURDATE() 
             AND r.estado IN ('pendiente', 'confirmada')
             ORDER BY r.fecha_cita, r.hora_inicio",
            [$userId]
        );
        
        // Historial de citas
        $pastAppointments = $this->db->fetchAll(
            "SELECT r.*, s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                    e.id as esp_id, ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.cliente_id = ? AND (r.fecha_cita < CURDATE() OR r.estado IN ('completada', 'cancelada'))
             ORDER BY r.fecha_cita DESC, r.hora_inicio DESC
             LIMIT 5",
            [$userId]
        );
        
        // Total de citas
        $totalAppointments = $this->db->fetch(
            "SELECT COUNT(*) as total FROM reservaciones WHERE cliente_id = ?",
            [$userId]
        )['total'];
        
        return [
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
            'totalAppointments' => $totalAppointments
        ];
    }
}
