<?php
/**
 * ReserBot - Controlador de Autenticación
 */

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    
    /**
     * Muestra el formulario de login
     */
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $email = $this->post('email');
            $password = $this->post('password');
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor ingrese su correo y contraseña.';
            } else {
                // Buscar usuario
                $user = $this->db->fetch(
                    "SELECT * FROM usuarios WHERE email = ? AND activo = 1",
                    [$email]
                );
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login exitoso
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nombre' => $user['nombre'],
                        'apellidos' => $user['apellidos'],
                        'email' => $user['email'],
                        'rol_id' => $user['rol_id'],
                        'sucursal_id' => $user['sucursal_id'],
                        'avatar' => $user['avatar']
                    ];
                    
                    // Actualizar último acceso
                    $this->db->update(
                        "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?",
                        [$user['id']]
                    );
                    
                    // Registrar en log
                    logAction('login', 'Inicio de sesión exitoso', ['email' => $email]);
                    
                    setFlashMessage('success', '¡Bienvenido(a), ' . $user['nombre'] . '!');
                    redirect('/dashboard');
                } else {
                    $error = 'Credenciales incorrectas. Verifique su correo y contraseña.';
                    logAction('login_failed', 'Intento de inicio de sesión fallido', ['email' => $email]);
                }
            }
        }
        
        $this->renderAuth('auth/login', [
            'title' => 'Iniciar Sesión',
            'error' => $error
        ]);
    }
    
    /**
     * Muestra el formulario de registro
     */
    public function register() {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        $error = '';
        $success = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $password = $this->post('password');
            $password_confirm = $this->post('password_confirm');
            
            // Validaciones
            if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
                $error = 'Todos los campos marcados con * son obligatorios.';
            } elseif (!validateEmail($email)) {
                $error = 'Por favor ingrese un correo electrónico válido.';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($password !== $password_confirm) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                // Verificar si el email ya existe
                $exists = $this->db->fetch(
                    "SELECT id FROM usuarios WHERE email = ?",
                    [$email]
                );
                
                if ($exists) {
                    $error = 'Ya existe una cuenta con este correo electrónico.';
                } else {
                    // Crear usuario
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $token = generateToken();
                    
                    $userId = $this->db->insert(
                        "INSERT INTO usuarios (nombre, apellidos, email, telefono, password, rol_id, token_verificacion) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [$nombre, $apellidos, $email, $telefono, $hashedPassword, ROLE_CLIENT, $token]
                    );
                    
                    if ($userId) {
                        logAction('register', 'Nuevo registro de usuario', ['user_id' => $userId, 'email' => $email]);
                        
                        setFlashMessage('success', '¡Cuenta creada exitosamente! Ahora puede iniciar sesión.');
                        redirect('/login');
                    } else {
                        $error = 'Error al crear la cuenta. Intente nuevamente.';
                    }
                }
            }
        }
        
        $this->renderAuth('auth/register', [
            'title' => 'Crear Cuenta',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Cierra la sesión
     */
    public function logout() {
        if (isLoggedIn()) {
            logAction('logout', 'Cierre de sesión');
        }
        
        session_destroy();
        redirect('/login');
    }
    
    /**
     * Formulario de recuperación de contraseña
     */
    public function forgotPassword() {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        $error = '';
        $success = '';
        
        if ($this->isPost()) {
            $email = $this->post('email');
            
            if (empty($email)) {
                $error = 'Por favor ingrese su correo electrónico.';
            } elseif (!validateEmail($email)) {
                $error = 'Por favor ingrese un correo electrónico válido.';
            } else {
                $user = $this->db->fetch(
                    "SELECT id, nombre FROM usuarios WHERE email = ? AND activo = 1",
                    [$email]
                );
                
                if ($user) {
                    $token = generateToken();
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    $this->db->update(
                        "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id = ?",
                        [$token, $expiry, $user['id']]
                    );
                    
                    // En producción, aquí se enviaría el correo
                    logAction('password_reset_request', 'Solicitud de recuperación de contraseña', ['email' => $email]);
                }
                
                // Siempre mostrar el mismo mensaje por seguridad
                $success = 'Si el correo está registrado, recibirá instrucciones para restablecer su contraseña.';
            }
        }
        
        $this->renderAuth('auth/forgot-password', [
            'title' => 'Recuperar Contraseña',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Restablecer contraseña
     */
    public function resetPassword() {
        $token = $this->get('token');
        $error = '';
        $success = '';
        
        if (empty($token)) {
            setFlashMessage('error', 'Token inválido o expirado.');
            redirect('/login');
        }
        
        // Verificar token
        $user = $this->db->fetch(
            "SELECT id FROM usuarios WHERE token_recuperacion = ? AND token_expira > NOW()",
            [$token]
        );
        
        if (!$user) {
            setFlashMessage('error', 'Token inválido o expirado. Solicite un nuevo enlace.');
            redirect('/recuperar-password');
        }
        
        if ($this->isPost()) {
            $password = $this->post('password');
            $password_confirm = $this->post('password_confirm');
            
            if (empty($password)) {
                $error = 'Por favor ingrese una nueva contraseña.';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($password !== $password_confirm) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $this->db->update(
                    "UPDATE usuarios SET password = ?, token_recuperacion = NULL, token_expira = NULL WHERE id = ?",
                    [$hashedPassword, $user['id']]
                );
                
                logAction('password_reset', 'Contraseña restablecida', ['user_id' => $user['id']]);
                
                setFlashMessage('success', 'Contraseña actualizada correctamente. Ahora puede iniciar sesión.');
                redirect('/login');
            }
        }
        
        $this->renderAuth('auth/reset-password', [
            'title' => 'Restablecer Contraseña',
            'token' => $token,
            'error' => $error
        ]);
    }
}
