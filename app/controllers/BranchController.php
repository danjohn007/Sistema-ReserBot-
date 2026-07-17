<?php
/**
 * ReserBot - Controlador de Sucursales
 */

require_once __DIR__ . '/BaseController.php';

class BranchController extends BaseController {
    
    /**
     * Lista de sucursales
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);
        
        $user = currentUser();
        $visibilityView = 'visibles';
        $visibilityCounts = ['visibles' => 0, 'ocultas' => 0];
        
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $visibilityView = $this->get('vista') === 'ocultas' ? 'ocultas' : 'visibles';
            $hidden = $visibilityView === 'ocultas' ? 1 : 0;
            $branches = $this->db->fetchAll(
                "SELECT *
                 FROM sucursales
                 WHERE autorizado = 1 AND oculta_superadmin = ?
                 ORDER BY nombre",
                [$hidden]
            );
            $counts = $this->db->fetch(
                "SELECT
                    SUM(CASE WHEN oculta_superadmin = 0 THEN 1 ELSE 0 END) AS visibles,
                    SUM(CASE WHEN oculta_superadmin = 1 THEN 1 ELSE 0 END) AS ocultas
                 FROM sucursales
                 WHERE autorizado = 1"
            );
            $visibilityCounts = [
                'visibles' => (int) ($counts['visibles'] ?? 0),
                'ocultas' => (int) ($counts['ocultas'] ?? 0)
            ];
        } elseif ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $branches = $this->db->fetchAll(
                "SELECT * FROM sucursales WHERE id = ?",
                [$user['sucursal_id']]
            );
        } else {
            $branches = $this->db->fetchAll(
                "SELECT DISTINCT s.*
                 FROM sucursales s
                 JOIN especialistas e ON e.sucursal_id = s.id
                 WHERE e.usuario_id = ?
                   AND e.autorizado = 1
                   AND s.autorizado = 1
                 ORDER BY s.nombre",
                [$user['id']]
            );
        }
        
        $this->render('branches/index', [
            'title' => $user['rol_id'] == ROLE_SPECIALIST ? 'Mis sucursales' : 'Sucursales',
            'branches' => $branches,
            'visibilityView' => $visibilityView,
            'visibilityCounts' => $visibilityCounts
        ]);
    }

    /**
     * Oculta o muestra una sucursal solo dentro del listado del Superadmin.
     */
    public function toggleVisibility() {
        $this->requireRole(ROLE_SUPERADMIN);

        if (!$this->isPost()) {
            redirect('/sucursales');
        }

        $id = (int) $this->post('id');
        $hidden = (int) $this->post('oculta') === 1 ? 1 : 0;
        $returnView = $this->post('vista') === 'ocultas' ? 'ocultas' : 'visibles';
        $branch = $this->db->fetch(
            "SELECT id, nombre FROM sucursales WHERE id = ? AND autorizado = 1",
            [$id]
        );

        if (!$branch) {
            setFlashMessage('error', 'Sucursal no encontrada.');
            redirect('/sucursales?vista=' . $returnView);
        }

        $this->db->update(
            "UPDATE sucursales SET oculta_superadmin = ? WHERE id = ?",
            [$hidden, $id]
        );

        $action = $hidden ? 'ocultada' : 'mostrada';
        logAction('branch_visibility_update', 'Sucursal ' . $action . ': ' . $branch['nombre']);
        setFlashMessage(
            'success',
            $hidden
                ? 'Sucursal ocultada del listado. Puedes recuperarla desde la vista de ocultas.'
                : 'Sucursal mostrada nuevamente en el listado.'
        );
        redirect('/sucursales?vista=' . $returnView);
    }
    
    /**
     * Crear sucursal
     */
    public function create() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_SPECIALIST]);

        $user = currentUser();
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $color = $this->post('color') ?: '#3B82F6';
            $direccion = $this->post('direccion');
            $ciudad = $this->post('ciudad');
            $estado = $this->post('estado');
            $codigo_postal = $this->post('codigo_postal');
            $telefono = $this->post('telefono');
            $email = $this->post('email');
            $horario_apertura = $this->post('horario_apertura');
            $horario_cierre = $this->post('horario_cierre');
            
            if (empty($nombre)) {
                $error = 'El nombre de la sucursal es obligatorio.';
            } else {
                try {
                    $this->db->beginTransaction();

                    $branchId = $this->db->insert(
                        "INSERT INTO sucursales
                         (nombre, color, direccion, ciudad, estado, codigo_postal, telefono, email,
                          horario_apertura, horario_cierre, activo, autorizado)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)",
                        [$nombre, $color, $direccion, $ciudad, $estado, $codigo_postal, $telefono, $email, $horario_apertura, $horario_cierre]
                    );

                    if ($user['rol_id'] == ROLE_SPECIALIST) {
                        $profile = $this->db->fetch(
                            "SELECT profesion, especialidad, descripcion, experiencia_anos, tarifa_base,
                                    duracion_cita_default, foto, nombre_liga1, nombre_liga2, nombre_liga3
                             FROM especialistas
                             WHERE usuario_id = ? AND autorizado = 1
                             ORDER BY activo DESC, id ASC
                             LIMIT 1",
                            [$user['id']]
                        );

                        if (!$profile) {
                            throw new Exception('No se encontro un perfil profesional autorizado.');
                        }

                        $this->db->insert(
                            "INSERT INTO especialistas
                             (usuario_id, sucursal_id, profesion, especialidad, descripcion,
                              experiencia_anos, tarifa_base, duracion_cita_default, foto, activo,
                              autorizado, nombre_liga1, nombre_liga2, nombre_liga3)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, ?, ?, ?)",
                            [
                                $user['id'], $branchId, $profile['profesion'], $profile['especialidad'],
                                $profile['descripcion'], $profile['experiencia_anos'], $profile['tarifa_base'],
                                $profile['duracion_cita_default'], $profile['foto'], $profile['nombre_liga1'],
                                $profile['nombre_liga2'], $profile['nombre_liga3']
                            ]
                        );
                    }

                    $this->db->commit();
                    logAction('branch_create', 'Sucursal creada: ' . $nombre);
                    setFlashMessage('success', $user['rol_id'] == ROLE_SPECIALIST
                        ? 'Sucursal agregada a tu perfil correctamente. Ya puedes configurar sus servicios y horarios.'
                        : 'Sucursal creada correctamente.');
                    redirect('/sucursales');
                } catch (Exception $e) {
                    $this->db->rollBack();
                    error_log('Error al crear sucursal: ' . $e->getMessage());
                    $error = 'No se pudo crear la sucursal. Intenta nuevamente.';
                }
            }
        }
        
        $this->render('branches/create', [
            'title' => 'Nueva sucursal',
            'error' => $error
        ]);
    }
    
    /**
     * Editar sucursal
     */
    public function edit() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);
        
        $id = $this->get('id');
        $user = currentUser();
        
        // Verificar permisos
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN && $user['sucursal_id'] != $id) {
            setFlashMessage('error', 'No tiene permisos para editar esta sucursal.');
            redirect('/sucursales');
        }

        if ($user['rol_id'] == ROLE_SPECIALIST && !$this->specialistOwnsBranch($user['id'], $id)) {
            setFlashMessage('error', 'No tienes permisos para editar esta sucursal.');
            redirect('/sucursales');
        }
        
        $branch = $this->db->fetch("SELECT * FROM sucursales WHERE id = ?", [$id]);
        
        if (!$branch) {
            setFlashMessage('error', 'Sucursal no encontrada.');
            redirect('/sucursales');
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $color = $this->post('color') ?: '#3B82F6';
            $direccion = $this->post('direccion');
            $ciudad = $this->post('ciudad');
            $estado = $this->post('estado');
            $codigo_postal = $this->post('codigo_postal');
            $telefono = $this->post('telefono');
            $email = $this->post('email');
            $horario_apertura = $this->post('horario_apertura');
            $horario_cierre = $this->post('horario_cierre');
            $activo = $this->post('activo') ? 1 : 0;
            
            if (empty($nombre)) {
                $error = 'El nombre de la sucursal es obligatorio.';
            } else {
                $this->db->update(
                    "UPDATE sucursales SET nombre = ?, color = ?, direccion = ?, ciudad = ?, estado = ?, 
                     codigo_postal = ?, telefono = ?, email = ?, horario_apertura = ?, 
                     horario_cierre = ?, activo = ? WHERE id = ?",
                    [$nombre, $color, $direccion, $ciudad, $estado, $codigo_postal, $telefono, $email, 
                     $horario_apertura, $horario_cierre, $activo, $id]
                );
                
                logAction('branch_update', 'Sucursal actualizada: ' . $nombre);
                setFlashMessage('success', 'Sucursal actualizada correctamente.');
                redirect('/sucursales');
            }
        }
        
        $this->render('branches/edit', [
            'title' => 'Editar sucursal',
            'branch' => $branch,
            'error' => $error
        ]);
    }
    
    /**
     * Eliminar sucursal
     */
    public function delete() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $id = $this->get('id');
        
        $branch = $this->db->fetch("SELECT nombre FROM sucursales WHERE id = ?", [$id]);
        
        if ($branch) {
            $this->db->update("UPDATE sucursales SET activo = 0 WHERE id = ?", [$id]);
            logAction('branch_delete', 'Sucursal desactivada: ' . $branch['nombre']);
            setFlashMessage('success', 'Sucursal desactivada correctamente.');
        }
        
        redirect('/sucursales');
    }
    
    /**
     * Actualizar solo el color de una sucursal (AJAX)
     */
    public function updateColor() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);
        header('Content-Type: application/json');
        
        $id = $this->post('id');
        $color = $this->post('color');
        
        // Validar formato de color hexadecimal
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            echo json_encode(['success' => false, 'message' => 'Formato de color inválido']);
            return;
        }
        
        $user = currentUser();
        
        // Verificar permisos
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN && $user['sucursal_id'] != $id) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para modificar esta sucursal']);
            return;
        }

        if ($user['rol_id'] == ROLE_SPECIALIST && !$this->specialistOwnsBranch($user['id'], $id)) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar esta sucursal']);
            return;
        }
        
        $branch = $this->db->fetch("SELECT nombre FROM sucursales WHERE id = ?", [$id]);
        
        if (!$branch) {
            echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
            return;
        }
        
        try {
            $this->db->update("UPDATE sucursales SET color = ? WHERE id = ?", [$color, $id]);
            logAction('branch_color_update', 'Color actualizado para sucursal: ' . $branch['nombre'] . ' a ' . $color);
            echo json_encode(['success' => true, 'message' => 'Color actualizado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el color']);
        }
    }

    private function specialistOwnsBranch($userId, $branchId) {
        return (bool) $this->db->fetch(
            "SELECT id
             FROM especialistas
             WHERE usuario_id = ? AND sucursal_id = ?
             LIMIT 1",
            [(int) $userId, (int) $branchId]
        );
    }
}
