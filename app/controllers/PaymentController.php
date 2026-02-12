<?php
/**
 * ReserBot - Controlador de Pagos
 */

require_once __DIR__ . '/BaseController.php';

class PaymentController extends BaseController {
    
    /**
     * Lista de pagos de reservaciones completadas
     * Solo para especialistas
     */
    public function index() {
        $this->requireAuth();
        $this->requireRole([ROLE_SPECIALIST]);
        
        $user = currentUser();
        
        // Obtener TODOS los registros de especialistas de este usuario (uno por sucursal)
        $allSpecialists = $this->db->fetchAll(
            "SELECT e.*, s.nombre as sucursal_nombre 
             FROM especialistas e 
             JOIN sucursales s ON e.sucursal_id = s.id 
             WHERE e.usuario_id = ? AND e.activo = 1
             ORDER BY s.nombre",
            [$user['id']]
        );
        
        if (empty($allSpecialists)) {
            setFlashMessage('error', 'No se encontró información de especialista.');
            redirect('/dashboard');
            return;
        }
        
        // Obtener IDs de todos los especialistas
        $specialistIds = array_column($allSpecialists, 'id');
        $placeholders = implode(',', array_fill(0, count($specialistIds), '?'));
        
        // Filtros
        $sucursal_id = $this->get('sucursal_id');
        $fecha_desde = $this->get('fecha_desde');
        $fecha_hasta = $this->get('fecha_hasta');
        $metodo_pago = $this->get('metodo_pago');
        
        // Query base
        $sql = "SELECT p.*, r.codigo, r.fecha_cita, r.hora_inicio, r.precio_total,
                       COALESCE(CONCAT(u.nombre, ' ', u.apellidos), r.nombre_cliente, 'Cliente sin registro') as cliente_nombre,
                       s.nombre as servicio_nombre,
                       suc.nombre as sucursal_nombre,
                       suc.id as sucursal_id,
                       ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
                FROM pagos p
                JOIN reservaciones r ON p.reservacion_id = r.id
                LEFT JOIN usuarios u ON r.cliente_id = u.id
                JOIN servicios s ON r.servicio_id = s.id
                JOIN sucursales suc ON r.sucursal_id = suc.id
                JOIN especialistas e ON r.especialista_id = e.id
                JOIN usuarios ue ON e.usuario_id = ue.id
                WHERE r.estado = 'completada' 
                AND r.especialista_id IN ($placeholders)";
        
        $filters = $specialistIds;
        
        // Aplicar filtros adicionales
        if ($sucursal_id) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $sucursal_id;
        }
        
        if ($fecha_desde) {
            $sql .= " AND r.fecha_cita >= ?";
            $filters[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND r.fecha_cita <= ?";
            $filters[] = $fecha_hasta;
        }
        
        if ($metodo_pago) {
            $sql .= " AND p.metodo_pago = ?";
            $filters[] = $metodo_pago;
        }
        
        $sql .= " ORDER BY r.fecha_cita DESC, r.hora_inicio DESC";
        
        $payments = $this->db->fetchAll($sql, $filters);
        
        // Calcular totales
        $totalGeneral = 0;
        $totalPorMetodo = [
            'efectivo' => 0,
            'tarjeta' => 0,
            'transferencia' => 0,
            'paypal' => 0,
            'sin_definir' => 0
        ];
        
        foreach ($payments as $payment) {
            $totalGeneral += $payment['monto'];
            $metodo = $payment['metodo_pago'] ?? 'sin_definir';
            $totalPorMetodo[$metodo] += $payment['monto'];
        }
        
        // Obtener sucursales del especialista para filtros
        $branches = $this->db->fetchAll(
            "SELECT DISTINCT s.id, s.nombre 
             FROM sucursales s
             JOIN especialistas e ON s.id = e.sucursal_id
             WHERE e.usuario_id = ? AND s.activo = 1
             ORDER BY s.nombre",
            [$user['id']]
        );
        
        $this->render('payments/index', [
            'title' => 'Mis Pagos',
            'payments' => $payments,
            'totalGeneral' => $totalGeneral,
            'totalPorMetodo' => $totalPorMetodo,
            'branches' => $branches,
            'filters' => [
                'sucursal_id' => $sucursal_id,
                'fecha_desde' => $fecha_desde,
                'fecha_hasta' => $fecha_hasta,
                'metodo_pago' => $metodo_pago
            ]
        ]);
    }
    
    /**
     * Actualizar información del pago
     */
    public function update() {
        $this->requireAuth();
        $this->requireRole([ROLE_SPECIALIST]);
        
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        $user = currentUser();
        $pago_id = $this->post('pago_id');
        $metodo_pago = $this->post('metodo_pago');
        $referencia_pago = $this->post('referencia_pago');
        $notas = $this->post('notas');
        
        if (!$pago_id) {
            $this->json(['success' => false, 'message' => 'ID de pago no proporcionado']);
            return;
        }
        
        // Verificar que el pago pertenece al especialista actual
        $payment = $this->db->fetch(
            "SELECT p.*, r.especialista_id 
             FROM pagos p
             JOIN reservaciones r ON p.reservacion_id = r.id
             JOIN especialistas e ON r.especialista_id = e.id
             WHERE p.id = ? AND e.usuario_id = ?",
            [$pago_id, $user['id']]
        );
        
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Pago no encontrado o no tienes permisos']);
            return;
        }
        
        // Actualizar el pago
        try {
            $this->db->update(
                "UPDATE pagos 
                 SET metodo_pago = ?, referencia_pago = ?, notas = ?
                 WHERE id = ?",
                [$metodo_pago ?: null, $referencia_pago ?: null, $notas ?: null, $pago_id]
            );
            
            logAction('payment_update', "Pago actualizado: ID $pago_id");
            
            $this->json(['success' => true, 'message' => 'Pago actualizado exitosamente']);
        } catch (Exception $e) {
            error_log("Error al actualizar pago: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error al actualizar el pago']);
        }
    }
}
