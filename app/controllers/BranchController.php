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
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT * FROM sucursales ORDER BY nombre");
        } else {
            $branches = $this->db->fetchAll(
                "SELECT * FROM sucursales WHERE id = ?",
                [$user['sucursal_id']]
            );
        }
        
        $this->render('branches/index', [
            'title' => 'Sucursales',
            'branches' => $branches
        ]);
    }
    
    /**
     * Crear sucursal
     */
    public function create() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
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
                $this->db->insert(
                    "INSERT INTO sucursales (nombre, color, direccion, ciudad, estado, codigo_postal, telefono, email, horario_apertura, horario_cierre) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$nombre, $color, $direccion, $ciudad, $estado, $codigo_postal, $telefono, $email, $horario_apertura, $horario_cierre]
                );
                
                logAction('branch_create', 'Sucursal creada: ' . $nombre);
                setFlashMessage('success', 'Sucursal creada correctamente.');
                redirect('/sucursales');
            }
        }
        
        $this->render('branches/create', [
            'title' => 'Nueva Sucursal',
            'error' => $error
        ]);
    }
    
    /**
     * Editar sucursal
     */
    public function edit() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        $user = currentUser();
        
        // Verificar permisos
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN && $user['sucursal_id'] != $id) {
            setFlashMessage('error', 'No tiene permisos para editar esta sucursal.');
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
            'title' => 'Editar Sucursal',
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
        $this->requireAuth();
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
}
