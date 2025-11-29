<?php
/**
 * ReserBot - Controlador de Clientes
 */

require_once __DIR__ . '/BaseController.php';

class ClientController extends BaseController {
    
    /**
     * Lista de clientes
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST]);
        
        $search = $this->get('search');
        
        $filters = [ROLE_CLIENT];
        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM reservaciones r WHERE r.cliente_id = u.id) as total_citas,
                       (SELECT MAX(r.fecha_cita) FROM reservaciones r WHERE r.cliente_id = u.id) as ultima_cita
                FROM usuarios u
                WHERE u.rol_id = ?";
        
        if ($search) {
            $sql .= " AND (u.nombre LIKE ? OR u.apellidos LIKE ? OR u.email LIKE ? OR u.telefono LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $filters[] = $searchTerm;
            $filters[] = $searchTerm;
            $filters[] = $searchTerm;
            $filters[] = $searchTerm;
        }
        
        $sql .= " ORDER BY u.nombre, u.apellidos";
        
        $clients = $this->db->fetchAll($sql, $filters);
        
        $this->render('clients/index', [
            'title' => 'Clientes',
            'clients' => $clients,
            'search' => $search
        ]);
    }
    
    /**
     * Ver cliente
     */
    public function view() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST]);
        
        $id = $this->get('id');
        
        $client = $this->db->fetch(
            "SELECT * FROM usuarios WHERE id = ? AND rol_id = ?",
            [$id, ROLE_CLIENT]
        );
        
        if (!$client) {
            setFlashMessage('error', 'Cliente no encontrado.');
            redirect('/clientes');
        }
        
        // Historial de citas
        $appointments = $this->db->fetchAll(
            "SELECT r.*, s.nombre as servicio_nombre, suc.nombre as sucursal_nombre,
                    ue.nombre as especialista_nombre, ue.apellidos as especialista_apellidos
             FROM reservaciones r
             JOIN servicios s ON r.servicio_id = s.id
             JOIN sucursales suc ON r.sucursal_id = suc.id
             JOIN especialistas e ON r.especialista_id = e.id
             JOIN usuarios ue ON e.usuario_id = ue.id
             WHERE r.cliente_id = ?
             ORDER BY r.fecha_cita DESC, r.hora_inicio DESC",
            [$id]
        );
        
        // Estadísticas
        $stats = $this->db->fetch(
            "SELECT COUNT(*) as total_citas,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(CASE WHEN estado = 'completada' THEN precio_total ELSE 0 END) as total_gastado
             FROM reservaciones
             WHERE cliente_id = ?",
            [$id]
        );
        
        $this->render('clients/view', [
            'title' => $client['nombre'] . ' ' . $client['apellidos'],
            'client' => $client,
            'appointments' => $appointments,
            'stats' => $stats
        ]);
    }
    
    /**
     * Editar cliente
     */
    public function edit() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST]);
        
        $id = $this->get('id');
        
        $client = $this->db->fetch(
            "SELECT * FROM usuarios WHERE id = ? AND rol_id = ?",
            [$id, ROLE_CLIENT]
        );
        
        if (!$client) {
            setFlashMessage('error', 'Cliente no encontrado.');
            redirect('/clientes');
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $activo = $this->post('activo') ? 1 : 0;
            
            if (empty($nombre) || empty($apellidos) || empty($email)) {
                $error = 'Los campos nombre, apellidos y email son obligatorios.';
            } else {
                // Verificar email único
                $exists = $this->db->fetch(
                    "SELECT id FROM usuarios WHERE email = ? AND id != ?",
                    [$email, $id]
                );
                
                if ($exists) {
                    $error = 'Ya existe un usuario con este correo electrónico.';
                } else {
                    $this->db->update(
                        "UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, telefono = ?, activo = ? WHERE id = ?",
                        [$nombre, $apellidos, $email, $telefono, $activo, $id]
                    );
                    
                    logAction('client_update', 'Cliente actualizado: ' . $nombre . ' ' . $apellidos);
                    setFlashMessage('success', 'Cliente actualizado correctamente.');
                    redirect('/clientes/ver?id=' . $id);
                }
            }
        }
        
        $this->render('clients/edit', [
            'title' => 'Editar Cliente',
            'client' => $client,
            'error' => $error
        ]);
    }
}
