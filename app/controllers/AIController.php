<?php
/**
 * ReserBot - Controlador de Configuración de IA
 */

require_once __DIR__ . '/BaseController.php';

class AIController extends BaseController {

    /**
     * Vista principal: el especialista edita los suyos;
     * el superadmin selecciona un usuario y edita los de ese usuario.
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_SPECIALIST]);

        $user = currentUser();
        $isSuperAdmin = $user['rol_id'] == ROLE_SUPERADMIN;

        $specialists = [];
        $selectedId  = null;
        $target      = null;

        if ($isSuperAdmin) {
            $specialists = $this->db->fetchAll(
                "SELECT id, nombre, apellidos FROM usuarios WHERE rol_id = ? AND activo = 1 ORDER BY nombre, apellidos",
                [ROLE_SPECIALIST]
            );
            $selectedId = (int)($this->get('usuario_id') ?: ($specialists[0]['id'] ?? 0));

            if ($selectedId > 0) {
                $target = $this->db->fetch(
                    "SELECT id, nombre, apellidos, ai_enabled, ai_contexto FROM usuarios WHERE id = ?",
                    [$selectedId]
                );
            }
        } else {
            $selectedId = (int)$user['id'];
            $target = $this->db->fetch(
                "SELECT id, nombre, apellidos, ai_enabled, ai_contexto FROM usuarios WHERE id = ?",
                [$selectedId]
            );
        }

        $this->render('ai/index', [
            'title'        => 'Configuración de IA',
            'isSuperAdmin' => $isSuperAdmin,
            'specialists'  => $specialists,
            'selectedId'   => $selectedId,
            'target'       => $target,
        ]);
    }

    /**
     * Guardar cambios de ai_enabled y ai_contexto (POST AJAX).
     */
    public function save() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_SPECIALIST]);

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $user        = currentUser();
        $isSuperAdmin = $user['rol_id'] == ROLE_SUPERADMIN;

        $targetId  = (int)$this->post('usuario_id');
        $aiEnabled = $this->post('ai_enabled') ? 1 : 0;
        $aiContexto = trim($this->post('ai_contexto') ?? '');

        if ($targetId <= 0) {
            $this->json(['success' => false, 'message' => 'Usuario inválido'], 400);
        }

        // Un especialista solo puede modificar sus propios datos
        if (!$isSuperAdmin && $targetId !== (int)$user['id']) {
            $this->json(['success' => false, 'message' => 'Permiso denegado'], 403);
        }

        if (mb_strlen($aiContexto) > 5000) {
            $this->json(['success' => false, 'message' => 'El contexto no puede superar 5000 caracteres'], 400);
        }

        // Verificar que el usuario exista
        $exists = $this->db->fetch("SELECT id FROM usuarios WHERE id = ?", [$targetId]);
        if (!$exists) {
            $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $this->db->query(
            "UPDATE usuarios SET ai_enabled = ?, ai_contexto = ? WHERE id = ?",
            [$aiEnabled, $aiContexto ?: null, $targetId]
        );

        logAction('ai_config_update', "Configuración de IA actualizada para usuario #{$targetId} (enabled={$aiEnabled})");

        $this->json(['success' => true, 'message' => 'Configuración guardada correctamente']);
    }
}
