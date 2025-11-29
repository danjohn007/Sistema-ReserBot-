<?php
/**
 * ReserBot - Controlador de Notificaciones
 */

require_once __DIR__ . '/BaseController.php';

class NotificationController extends BaseController {
    
    /**
     * Lista de notificaciones
     */
    public function index() {
        $this->requireAuth();
        
        $user = currentUser();
        
        $notifications = $this->db->fetchAll(
            "SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY created_at DESC LIMIT 50",
            [$user['id']]
        );
        
        // Marcar como leídas
        $this->db->update(
            "UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0",
            [$user['id']]
        );
        
        $this->render('notifications/index', [
            'title' => 'Notificaciones',
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Marcar notificación como leída
     */
    public function markAsRead() {
        $this->requireAuth();
        
        $id = $this->get('id');
        $user = currentUser();
        
        if ($id) {
            $this->db->update(
                "UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?",
                [$id, $user['id']]
            );
        } else {
            // Marcar todas como leídas
            $this->db->update(
                "UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?",
                [$user['id']]
            );
        }
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }
        
        redirect('/notificaciones');
    }
    
    /**
     * Gestión de plantillas de notificaciones
     */
    public function templates() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        $success = '';
        
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $templates = $this->db->fetchAll(
                "SELECT p.*, s.nombre as sucursal_nombre 
                 FROM plantillas_notificaciones p 
                 LEFT JOIN sucursales s ON p.sucursal_id = s.id 
                 ORDER BY p.tipo, p.canal"
            );
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        } else {
            $templates = $this->db->fetchAll(
                "SELECT p.*, s.nombre as sucursal_nombre 
                 FROM plantillas_notificaciones p 
                 LEFT JOIN sucursales s ON p.sucursal_id = s.id 
                 WHERE p.sucursal_id IS NULL OR p.sucursal_id = ?
                 ORDER BY p.tipo, p.canal",
                [$user['sucursal_id']]
            );
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE id = ?", [$user['sucursal_id']]);
        }
        
        if ($this->isPost()) {
            $action = $this->post('action');
            
            if ($action == 'save') {
                $id = $this->post('id');
                $tipo = $this->post('tipo');
                $canal = $this->post('canal');
                $asunto = $this->post('asunto');
                $contenido = $this->post('contenido');
                $sucursal_id = $this->post('sucursal_id') ?: null;
                
                if ($id) {
                    $this->db->update(
                        "UPDATE plantillas_notificaciones SET tipo = ?, canal = ?, asunto = ?, contenido = ?, sucursal_id = ? WHERE id = ?",
                        [$tipo, $canal, $asunto, $contenido, $sucursal_id, $id]
                    );
                } else {
                    $this->db->insert(
                        "INSERT INTO plantillas_notificaciones (tipo, canal, asunto, contenido, sucursal_id) VALUES (?, ?, ?, ?, ?)",
                        [$tipo, $canal, $asunto, $contenido, $sucursal_id]
                    );
                }
                
                $success = 'Plantilla guardada correctamente.';
            }
            
            // Recargar
            redirect('/notificaciones/plantillas');
        }
        
        $this->render('notifications/templates', [
            'title' => 'Plantillas de Notificaciones',
            'templates' => $templates,
            'branches' => $branches,
            'success' => $success
        ]);
    }
}
