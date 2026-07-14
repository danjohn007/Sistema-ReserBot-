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

        if (hasRole(ROLE_SUPERADMIN)) {
            $this->registrationRequestsIndex();
            return;
        }
        
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
     * Detalle de una solicitud conjunta para Superadmin.
     */
    public function requestView() {
        $this->requireRole(ROLE_SUPERADMIN);

        $id = (int) $this->get('id');
        $request = $this->getRegistrationRequest($id);

        if (!$request) {
            setFlashMessage('error', 'Solicitud no encontrada.');
            redirect('/clientes');
        }

        $branches = $this->getRequestBranches($id);
        $professionals = $this->getRequestProfessionals($id);

        $this->render('clients/request-view', [
            'title' => 'Solicitud #' . $request['id'],
            'request' => $request,
            'branches' => $branches,
            'professionals' => $professionals
        ]);
    }

    /**
     * Autoriza la sucursal, sus profesionistas y todos sus accesos juntos.
     */
    public function approveRequest() {
        $this->requireRole(ROLE_SUPERADMIN);

        if (!$this->isPost()) {
            redirect('/clientes');
        }

        $id = (int) $this->post('id');
        $reviewer = currentUser();
        $this->db->beginTransaction();

        try {
            $request = $this->db->fetch(
                "SELECT * FROM solicitudes_registro WHERE id = ? FOR UPDATE",
                [$id]
            );

            if (!$request || $request['estado'] !== 'pendiente') {
                throw new Exception('La solicitud ya fue revisada o no existe.');
            }

            $branches = $this->getRequestBranches($id);
            $branchIds = array_column($branches, 'id');
            $branchPlaceholders = implode(',', array_fill(0, count($branchIds), '?'));
            $professionalUsers = empty($branchIds) ? [] : $this->db->fetchAll(
                "SELECT DISTINCT usuario_id
                 FROM especialistas
                 WHERE sucursal_id IN ($branchPlaceholders)",
                $branchIds
            );

            if (empty($branches) || empty($professionalUsers)) {
                throw new Exception('La solicitud no contiene sucursales y profesionistas validos.');
            }

            $this->db->update(
                "UPDATE sucursales SET autorizado = 1, activo = 1 WHERE id IN ($branchPlaceholders)",
                $branchIds
            );
            $this->db->update(
                "UPDATE especialistas SET autorizado = 1, activo = 1 WHERE sucursal_id IN ($branchPlaceholders)",
                $branchIds
            );

            $userIds = array_column($professionalUsers, 'usuario_id');
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $this->db->update(
                "UPDATE usuarios SET activo = 1 WHERE id IN ($placeholders)",
                $userIds
            );

            $this->db->update(
                "UPDATE solicitudes_registro
                 SET estado = 'aprobada', motivo_rechazo = NULL, revisado_por = ?, fecha_revision = NOW()
                 WHERE id = ?",
                [$reviewer['id'], $id]
            );

            $this->db->commit();
            logAction('registration_request_approve', 'Solicitud aprobada: #' . $id . ' - ' . count($branches) . ' sucursales');
            setFlashMessage('success', 'Solicitud aprobada. Todas las sucursales y sus profesionistas ya estan activos.');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error al aprobar solicitud: ' . $e->getMessage());
            setFlashMessage('error', 'No se pudo aprobar la solicitud completa. Verifica sus datos e intenta de nuevo.');
        }

        redirect('/clientes/solicitud?id=' . $id);
    }

    /**
     * Rechaza la solicitud completa; los registros permanecen inactivos.
     */
    public function rejectRequest() {
        $this->requireRole(ROLE_SUPERADMIN);

        if (!$this->isPost()) {
            redirect('/clientes');
        }

        $id = (int) $this->post('id');
        $reason = trim(strip_tags((string) ($_POST['motivo_rechazo'] ?? '')));

        if ($reason === '') {
            setFlashMessage('error', 'Escribe el motivo del rechazo.');
            redirect('/clientes/solicitud?id=' . $id);
        }

        $reviewer = currentUser();
        $this->db->beginTransaction();

        try {
            $request = $this->db->fetch(
                "SELECT * FROM solicitudes_registro WHERE id = ? FOR UPDATE",
                [$id]
            );

            if (!$request || $request['estado'] !== 'pendiente') {
                throw new Exception('La solicitud ya fue revisada o no existe.');
            }

            $branches = $this->getRequestBranches($id);
            $branchIds = array_column($branches, 'id');
            if (empty($branchIds)) {
                throw new Exception('La solicitud no contiene sucursales validas.');
            }

            $branchPlaceholders = implode(',', array_fill(0, count($branchIds), '?'));
            $this->db->update(
                "UPDATE sucursales SET autorizado = 0, activo = 0 WHERE id IN ($branchPlaceholders)",
                $branchIds
            );
            $this->db->update(
                "UPDATE especialistas SET autorizado = 0, activo = 0 WHERE sucursal_id IN ($branchPlaceholders)",
                $branchIds
            );
            $professionalUsers = $this->db->fetchAll(
                "SELECT DISTINCT usuario_id
                 FROM especialistas
                 WHERE sucursal_id IN ($branchPlaceholders)",
                $branchIds
            );
            if (!empty($professionalUsers)) {
                $userIds = array_column($professionalUsers, 'usuario_id');
                $placeholders = implode(',', array_fill(0, count($userIds), '?'));
                $this->db->update(
                    "UPDATE usuarios SET activo = 0 WHERE id IN ($placeholders)",
                    $userIds
                );
            }
            $this->db->update(
                "UPDATE solicitudes_registro
                 SET estado = 'rechazada', motivo_rechazo = ?, revisado_por = ?, fecha_revision = NOW()
                 WHERE id = ?",
                [mb_substr($reason, 0, 2000, 'UTF-8'), $reviewer['id'], $id]
            );

            $this->db->commit();
            logAction('registration_request_reject', 'Solicitud rechazada: #' . $id);
            setFlashMessage('success', 'Solicitud rechazada. Todos sus registros permanecen inactivos.');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error al rechazar solicitud: ' . $e->getMessage());
            setFlashMessage('error', 'No se pudo rechazar la solicitud. Intenta de nuevo.');
        }

        redirect('/clientes/solicitud?id=' . $id);
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

    private function registrationRequestsIndex() {
        $status = $this->get('estado', 'pendiente');
        $search = $this->get('search', '');
        $allowedStatuses = ['todos', 'pendiente', 'aprobada', 'rechazada'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pendiente';
        }

        $sql = "SELECT sr.*,
                       CONCAT(creador.nombre, ' ', creador.apellidos) AS capturado_por,
                       CONCAT(revisor.nombre, ' ', revisor.apellidos) AS revisado_por_nombre,
                       (SELECT GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '|||')
                        FROM solicitudes_registro_sucursales srs
                        JOIN sucursales s ON s.id = srs.sucursal_id
                        WHERE srs.solicitud_id = sr.id) AS sucursales_nombres,
                       (SELECT COUNT(*)
                        FROM solicitudes_registro_sucursales srs
                        WHERE srs.solicitud_id = sr.id) AS total_sucursales,
                       (SELECT COUNT(DISTINCT e.usuario_id)
                        FROM solicitudes_registro_sucursales srs
                        JOIN especialistas e ON e.sucursal_id = srs.sucursal_id
                        WHERE srs.solicitud_id = sr.id) AS total_profesionistas
                FROM solicitudes_registro sr
                JOIN usuarios creador ON creador.id = sr.creado_por
                LEFT JOIN usuarios revisor ON revisor.id = sr.revisado_por
                WHERE 1 = 1";
        $params = [];

        if ($status !== 'todos') {
            $sql .= " AND sr.estado = ?";
            $params[] = $status;
        }

        if ($search !== '') {
            $term = '%' . $search . '%';
            $sql .= " AND (EXISTS (
                           SELECT 1
                           FROM solicitudes_registro_sucursales srs2
                           JOIN sucursales s2 ON s2.id = srs2.sucursal_id
                           WHERE srs2.solicitud_id = sr.id
                           AND (s2.nombre LIKE ? OR s2.email LIKE ?)
                       ) OR
                       EXISTS (
                           SELECT 1
                           FROM solicitudes_registro_sucursales srs3
                           JOIN especialistas e2 ON e2.sucursal_id = srs3.sucursal_id
                           JOIN usuarios u2 ON u2.id = e2.usuario_id
                           WHERE srs3.solicitud_id = sr.id
                           AND (u2.nombre LIKE ? OR u2.apellidos LIKE ? OR u2.email LIKE ?)
                       ))";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        $sql .= " ORDER BY CASE sr.estado WHEN 'pendiente' THEN 0 WHEN 'rechazada' THEN 1 ELSE 2 END,
                         sr.created_at DESC";
        $requests = $this->db->fetchAll($sql, $params);

        $countsRows = $this->db->fetchAll(
            "SELECT estado, COUNT(*) AS total FROM solicitudes_registro GROUP BY estado"
        );
        $counts = ['pendiente' => 0, 'aprobada' => 0, 'rechazada' => 0];
        foreach ($countsRows as $row) {
            $counts[$row['estado']] = (int) $row['total'];
        }

        $this->render('clients/requests', [
            'title' => 'Solicitudes de registro',
            'requests' => $requests,
            'status' => $status,
            'search' => $search,
            'counts' => $counts
        ]);
    }

    private function getRegistrationRequest($id) {
        return $this->db->fetch(
            "SELECT sr.*,
                    CONCAT(creador.nombre, ' ', creador.apellidos) AS capturado_por,
                    CONCAT(revisor.nombre, ' ', revisor.apellidos) AS revisado_por_nombre
             FROM solicitudes_registro sr
             JOIN usuarios creador ON creador.id = sr.creado_por
             LEFT JOIN usuarios revisor ON revisor.id = sr.revisado_por
             WHERE sr.id = ?",
            [$id]
        );
    }

    private function getRequestBranches($requestId) {
        return $this->db->fetchAll(
            "SELECT s.*
             FROM solicitudes_registro_sucursales srs
             JOIN sucursales s ON s.id = srs.sucursal_id
             WHERE srs.solicitud_id = ?
             ORDER BY srs.created_at, s.id",
            [$requestId]
        );
    }

    private function getRequestProfessionals($requestId) {
        return $this->db->fetchAll(
            "SELECT e.usuario_id, e.profesion, e.especialidad, e.descripcion,
                    e.experiencia_anos, e.tarifa_base, MIN(e.autorizado) AS autorizado,
                    u.nombre, u.apellidos, u.email, u.telefono, u.activo AS usuario_activo,
                    GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR '|||') AS sucursales_nombres
             FROM solicitudes_registro_sucursales srs
             JOIN sucursales s ON s.id = srs.sucursal_id
             JOIN especialistas e ON e.sucursal_id = s.id
             JOIN usuarios u ON u.id = e.usuario_id
             WHERE srs.solicitud_id = ?
             GROUP BY e.usuario_id, e.profesion, e.especialidad, e.descripcion,
                      e.experiencia_anos, e.tarifa_base, u.nombre, u.apellidos,
                      u.email, u.telefono, u.activo
             ORDER BY u.nombre, u.apellidos",
            [$requestId]
        );
    }
}
