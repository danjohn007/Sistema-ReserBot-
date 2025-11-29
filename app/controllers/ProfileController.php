<?php
/**
 * ReserBot - Controlador de Perfil de Usuario
 */

require_once __DIR__ . '/BaseController.php';

class ProfileController extends BaseController {
    
    /**
     * Ver perfil
     */
    public function index() {
        $this->requireAuth();
        
        $user = currentUser();
        
        $userData = $this->db->fetch("SELECT * FROM usuarios WHERE id = ?", [$user['id']]);
        
        $this->render('profile/index', [
            'title' => 'Mi Perfil',
            'userData' => $userData
        ]);
    }
    
    /**
     * Editar perfil
     */
    public function edit() {
        $this->requireAuth();
        
        $user = currentUser();
        $error = '';
        $success = '';
        
        $userData = $this->db->fetch("SELECT * FROM usuarios WHERE id = ?", [$user['id']]);
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $telefono = $this->post('telefono');
            
            if (empty($nombre) || empty($apellidos)) {
                $error = 'Los campos nombre y apellidos son obligatorios.';
            } else {
                $this->db->update(
                    "UPDATE usuarios SET nombre = ?, apellidos = ?, telefono = ? WHERE id = ?",
                    [$nombre, $apellidos, $telefono, $user['id']]
                );
                
                // Actualizar sesión
                $_SESSION['user']['nombre'] = $nombre;
                $_SESSION['user']['apellidos'] = $apellidos;
                
                // Manejar avatar
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                    $uploadDir = PUBLIC_PATH . '/images/avatars/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                        $this->db->update(
                            "UPDATE usuarios SET avatar = ? WHERE id = ?",
                            ['/images/avatars/' . $fileName, $user['id']]
                        );
                        $_SESSION['user']['avatar'] = '/images/avatars/' . $fileName;
                    }
                }
                
                logAction('profile_update', 'Perfil actualizado');
                $success = 'Perfil actualizado correctamente.';
                
                // Recargar datos
                $userData = $this->db->fetch("SELECT * FROM usuarios WHERE id = ?", [$user['id']]);
            }
        }
        
        $this->render('profile/edit', [
            'title' => 'Editar Perfil',
            'userData' => $userData,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword() {
        $this->requireAuth();
        
        $user = currentUser();
        $error = '';
        $success = '';
        
        if ($this->isPost()) {
            $current_password = $this->post('current_password');
            $new_password = $this->post('new_password');
            $confirm_password = $this->post('confirm_password');
            
            // Verificar contraseña actual
            $userData = $this->db->fetch("SELECT password FROM usuarios WHERE id = ?", [$user['id']]);
            
            if (!password_verify($current_password, $userData['password'])) {
                $error = 'La contraseña actual es incorrecta.';
            } elseif (strlen($new_password) < 6) {
                $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Las contraseñas nuevas no coinciden.';
            } else {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                
                $this->db->update(
                    "UPDATE usuarios SET password = ? WHERE id = ?",
                    [$hashedPassword, $user['id']]
                );
                
                logAction('password_change', 'Contraseña cambiada');
                $success = 'Contraseña actualizada correctamente.';
            }
        }
        
        $this->render('profile/change-password', [
            'title' => 'Cambiar Contraseña',
            'error' => $error,
            'success' => $success
        ]);
    }
}
