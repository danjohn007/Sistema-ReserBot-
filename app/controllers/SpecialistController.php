<?php
/**
 * ReserBot - Controlador de Especialistas
 */

require_once __DIR__ . '/BaseController.php';

class SpecialistController extends BaseController {
    
    /**
     * Lista de especialistas
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST]);
        
        $user = currentUser();
        
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $specialists = $this->db->fetchAll(
                "SELECT e.*, u.nombre, u.apellidos, u.email, u.telefono, s.nombre as sucursal_nombre
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 JOIN sucursales s ON e.sucursal_id = s.id
                 WHERE e.activo = 1
                 ORDER BY u.nombre, u.apellidos"
            );
        } else {
            $specialists = $this->db->fetchAll(
                "SELECT e.*, u.nombre, u.apellidos, u.email, u.telefono, s.nombre as sucursal_nombre
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 JOIN sucursales s ON e.sucursal_id = s.id
                 WHERE e.sucursal_id = ? AND e.activo = 1
                 ORDER BY u.nombre, u.apellidos",
                [$user['sucursal_id']]
            );
        }
        
        $this->render('specialists/index', [
            'title' => 'Especialistas',
            'specialists' => $specialists
        ]);
    }
    
    /**
     * Crear especialista
     */
    public function create() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        $error = '';
        
        // Obtener sucursales y servicios
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        } else {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE id = ?", [$user['sucursal_id']]);
        }
        
        $services = $this->db->fetchAll(
            "SELECT s.*, c.nombre as categoria_nombre 
             FROM servicios s 
             JOIN categorias_servicios c ON s.categoria_id = c.id 
             WHERE s.activo = 1 
             ORDER BY c.nombre, s.nombre"
        );
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $password = $this->post('password');
            $sucursal_id = $this->post('sucursal_id');
            $profesion = $this->post('profesion');
            $especialidad = $this->post('especialidad');
            $descripcion = $this->post('descripcion');
            $experiencia_anos = $this->post('experiencia_anos');
            $tarifa_base = $this->post('tarifa_base');
            $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];
            
            // Validaciones
            if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
                $error = 'Los campos nombre, apellidos, email y contraseña son obligatorios.';
            } elseif (!validateEmail($email)) {
                $error = 'Ingrese un correo electrónico válido.';
            } else {
                // Verificar si el email ya existe
                $exists = $this->db->fetch("SELECT id FROM usuarios WHERE email = ?", [$email]);
                
                if ($exists) {
                    $error = 'Ya existe un usuario con este correo electrónico.';
                } else {
                    // Crear usuario
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $userId = $this->db->insert(
                        "INSERT INTO usuarios (nombre, apellidos, email, telefono, password, rol_id, sucursal_id, email_verificado, activo) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)",
                        [$nombre, $apellidos, $email, $telefono, $hashedPassword, ROLE_SPECIALIST, $sucursal_id]
                    );
                    
                    if ($userId) {
                        // Crear especialista
                        $specialistId = $this->db->insert(
                            "INSERT INTO especialistas (usuario_id, sucursal_id, profesion, especialidad, descripcion, experiencia_anos, tarifa_base) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)",
                            [$userId, $sucursal_id, $profesion, $especialidad, $descripcion, $experiencia_anos, $tarifa_base]
                        );
                        
                        // Asignar servicios
                        foreach ($servicios as $servicioId) {
                            $this->db->insert(
                                "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                                [$specialistId, $servicioId]
                            );
                        }
                        
                        logAction('specialist_create', 'Especialista creado: ' . $nombre . ' ' . $apellidos);
                        setFlashMessage('success', 'Especialista creado correctamente.');
                        redirect('/especialistas');
                    } else {
                        $error = 'Error al crear el especialista.';
                    }
                }
            }
        }
        
        $this->render('specialists/create', [
            'title' => 'Nuevo Especialista',
            'branches' => $branches,
            'services' => $services,
            'error' => $error
        ]);
    }
    
    /**
     * Editar especialista
     */
    public function edit() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        $user = currentUser();
        
        $specialist = $this->db->fetch(
            "SELECT e.*, u.nombre, u.apellidos, u.email, u.telefono
             FROM especialistas e
             JOIN usuarios u ON e.usuario_id = u.id
             WHERE e.id = ?",
            [$id]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'Especialista no encontrado.');
            redirect('/especialistas');
        }
        
        // Verificar permisos
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN && $specialist['sucursal_id'] != $user['sucursal_id']) {
            setFlashMessage('error', 'No tiene permisos para editar este especialista.');
            redirect('/especialistas');
        }
        
        // Obtener sucursales y servicios
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        } else {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE id = ?", [$user['sucursal_id']]);
        }
        
        $services = $this->db->fetchAll(
            "SELECT s.*, c.nombre as categoria_nombre 
             FROM servicios s 
             JOIN categorias_servicios c ON s.categoria_id = c.id 
             WHERE s.activo = 1 
             ORDER BY c.nombre, s.nombre"
        );
        
        // Servicios actuales del especialista
        $currentServices = $this->db->fetchAll(
            "SELECT servicio_id FROM especialistas_servicios WHERE especialista_id = ?",
            [$id]
        );
        $currentServiceIds = array_column($currentServices, 'servicio_id');
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $sucursal_id = $this->post('sucursal_id');
            $profesion = $this->post('profesion');
            $especialidad = $this->post('especialidad');
            $descripcion = $this->post('descripcion');
            $experiencia_anos = $this->post('experiencia_anos');
            $tarifa_base = $this->post('tarifa_base');
            $activo = $this->post('activo') ? 1 : 0;
            $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];
            
            if (empty($nombre) || empty($apellidos) || empty($email)) {
                $error = 'Los campos nombre, apellidos y email son obligatorios.';
            } else {
                // Actualizar usuario
                $this->db->update(
                    "UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, telefono = ?, sucursal_id = ? 
                     WHERE id = ?",
                    [$nombre, $apellidos, $email, $telefono, $sucursal_id, $specialist['usuario_id']]
                );
                
                // Actualizar especialista
                $this->db->update(
                    "UPDATE especialistas SET sucursal_id = ?, profesion = ?, especialidad = ?, 
                     descripcion = ?, experiencia_anos = ?, tarifa_base = ?, activo = ? WHERE id = ?",
                    [$sucursal_id, $profesion, $especialidad, $descripcion, $experiencia_anos, $tarifa_base, $activo, $id]
                );
                
                // Actualizar servicios
                $this->db->delete("DELETE FROM especialistas_servicios WHERE especialista_id = ?", [$id]);
                foreach ($servicios as $servicioId) {
                    $this->db->insert(
                        "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                        [$id, $servicioId]
                    );
                }
                
                logAction('specialist_update', 'Especialista actualizado: ' . $nombre . ' ' . $apellidos);
                setFlashMessage('success', 'Especialista actualizado correctamente.');
                redirect('/especialistas');
            }
        }
        
        $this->render('specialists/edit', [
            'title' => 'Editar Especialista',
            'specialist' => $specialist,
            'branches' => $branches,
            'services' => $services,
            'currentServiceIds' => $currentServiceIds,
            'error' => $error
        ]);
    }
    
    /**
     * Gestionar horarios del especialista
     */
    public function schedules() {
        $this->requireAuth();
        
        $user = currentUser();
        
        // Si es especialista, usar su propio ID
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            $specialist = $this->db->fetch(
                "SELECT * FROM especialistas WHERE usuario_id = ?",
                [$user['id']]
            );
            
            if (!$specialist) {
                setFlashMessage('error', 'No tiene perfil de especialista.');
                redirect('/dashboard');
            }
            $id = $specialist['id'];
        } else {
            $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
            $id = $this->get('id');
            
            $specialist = $this->db->fetch("SELECT * FROM especialistas WHERE id = ?", [$id]);
            
            if (!$specialist) {
                setFlashMessage('error', 'Especialista no encontrado.');
                redirect('/especialistas');
            }
        }
        
        // Obtener horarios actuales
        $schedules = $this->db->fetchAll(
            "SELECT * FROM horarios_especialistas WHERE especialista_id = ? ORDER BY dia_semana, hora_inicio",
            [$id]
        );
        
        // Obtener bloqueos
        $blocks = $this->db->fetchAll(
            "SELECT * FROM bloqueos_horario WHERE especialista_id = ? AND fecha_fin >= NOW() ORDER BY fecha_inicio",
            [$id]
        );
        
        if ($this->isPost()) {
            $action = $this->post('action');
            
            if ($action == 'save_schedule') {
                // Obtener intervalo de espacios
                $intervalo_espacios = (int)$this->post('intervalo_espacios', 60);
                
                // Validar que el intervalo sea 30 o 60
                if (!in_array($intervalo_espacios, [30, 60])) {
                    $intervalo_espacios = 60;
                }
                
                // Eliminar horarios anteriores
                $this->db->delete("DELETE FROM horarios_especialistas WHERE especialista_id = ?", [$id]);
                
                // Guardar nuevos horarios
                for ($day = 1; $day <= 7; $day++) {
                    $inicio = $this->post('hora_inicio_' . $day);
                    $fin = $this->post('hora_fin_' . $day);
                    $activo = $this->post('activo_' . $day) ? 1 : 0;
                    
                    // Bloqueo
                    $bloqueo_activo = $this->post('bloqueo_activo_' . $day) ? 1 : 0;
                    $hora_inicio_bloqueo = $this->post('hora_inicio_bloqueo_' . $day);
                    $hora_fin_bloqueo = $this->post('hora_fin_bloqueo_' . $day);
                    
                    if ($activo && $inicio && $fin) {
                        // Validar que la hora de inicio sea menor que la hora de fin
                        if (strtotime($inicio) >= strtotime($fin)) {
                            setFlashMessage('error', 'La hora de inicio debe ser menor que la hora de fin.');
                            redirect('/especialistas/horarios?id=' . $id);
                            return;
                        }
                        
                        // Validar bloqueo si está activo
                        if ($bloqueo_activo && $hora_inicio_bloqueo && $hora_fin_bloqueo) {
                            // El bloqueo debe estar dentro del horario laboral
                            if (strtotime($hora_inicio_bloqueo) < strtotime($inicio) || 
                                strtotime($hora_fin_bloqueo) > strtotime($fin)) {
                                setFlashMessage('error', 'El horario de bloqueo debe estar dentro del horario laboral.');
                                redirect('/especialistas/horarios?id=' . $id);
                                return;
                            }
                            
                            // La hora de inicio del bloqueo debe ser menor que la de fin
                            if (strtotime($hora_inicio_bloqueo) >= strtotime($hora_fin_bloqueo)) {
                                setFlashMessage('error', 'La hora de inicio del bloqueo debe ser menor que la hora de fin.');
                                redirect('/especialistas/horarios?id=' . $id);
                                return;
                            }
                        }
                        
                        $this->db->insert(
                            "INSERT INTO horarios_especialistas 
                             (especialista_id, dia_semana, hora_inicio, hora_fin, activo, intervalo_espacios, 
                              hora_inicio_bloqueo, hora_fin_bloqueo, bloqueo_activo) 
                             VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?)",
                            [$id, $day, $inicio, $fin, $intervalo_espacios, 
                             $bloqueo_activo ? $hora_inicio_bloqueo : null, 
                             $bloqueo_activo ? $hora_fin_bloqueo : null, 
                             $bloqueo_activo]
                        );
                    }
                }
                
                setFlashMessage('success', 'Horarios actualizados correctamente.');
            } elseif ($action == 'add_block') {
                $fecha_inicio = $this->post('fecha_inicio');
                $fecha_fin = $this->post('fecha_fin');
                $motivo = $this->post('motivo');
                $tipo = $this->post('tipo');
                
                $this->db->insert(
                    "INSERT INTO bloqueos_horario (especialista_id, fecha_inicio, fecha_fin, motivo, tipo) 
                     VALUES (?, ?, ?, ?, ?)",
                    [$id, $fecha_inicio, $fecha_fin, $motivo, $tipo]
                );
                
                setFlashMessage('success', 'Bloqueo de horario agregado.');
            } elseif ($action == 'delete_block') {
                $blockId = $this->post('block_id');
                $this->db->delete("DELETE FROM bloqueos_horario WHERE id = ? AND especialista_id = ?", [$blockId, $id]);
                setFlashMessage('success', 'Bloqueo eliminado.');
            }
            
            redirect('/especialistas/horarios?id=' . $id);
        }
        
        // Organizar horarios por día
        $schedulesByDay = [];
        foreach ($schedules as $schedule) {
            $schedulesByDay[$schedule['dia_semana']] = $schedule;
        }
        
        $this->render('specialists/schedules', [
            'title' => 'Horarios del Especialista',
            'specialist' => $specialist,
            'schedules' => $schedulesByDay,
            'blocks' => $blocks,
            'daysOfWeek' => getDaysOfWeek()
        ]);
    }
    
    /**
     * Perfil público del especialista
     */
    public function profile() {
        $id = $this->get('id');
        
        $specialist = $this->db->fetch(
            "SELECT e.*, u.nombre, u.apellidos, s.nombre as sucursal_nombre, s.direccion as sucursal_direccion
             FROM especialistas e
             JOIN usuarios u ON e.usuario_id = u.id
             JOIN sucursales s ON e.sucursal_id = s.id
             WHERE e.id = ? AND e.activo = 1",
            [$id]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'Especialista no encontrado.');
            redirect('/dashboard');
        }
        
        // Servicios del especialista
        $services = $this->db->fetchAll(
            "SELECT s.*, es.precio_personalizado, es.duracion_personalizada
             FROM servicios s
             JOIN especialistas_servicios es ON s.id = es.servicio_id
             WHERE es.especialista_id = ? AND s.activo = 1",
            [$id]
        );
        
        // Horarios
        $schedules = $this->db->fetchAll(
            "SELECT * FROM horarios_especialistas WHERE especialista_id = ? AND activo = 1 ORDER BY dia_semana",
            [$id]
        );
        
        $this->render('specialists/profile', [
            'title' => $specialist['nombre'] . ' ' . $specialist['apellidos'],
            'specialist' => $specialist,
            'services' => $services,
            'schedules' => $schedules,
            'daysOfWeek' => getDaysOfWeek()
        ]);
    }
    
    /**
     * Eliminar especialista
     */
    public function delete() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        
        $specialist = $this->db->fetch(
            "SELECT e.*, u.nombre, u.apellidos FROM especialistas e JOIN usuarios u ON e.usuario_id = u.id WHERE e.id = ?",
            [$id]
        );
        
        if ($specialist) {
            $this->db->update("UPDATE especialistas SET activo = 0 WHERE id = ?", [$id]);
            logAction('specialist_delete', 'Especialista desactivado: ' . $specialist['nombre'] . ' ' . $specialist['apellidos']);
            setFlashMessage('success', 'Especialista desactivado correctamente.');
        }
        
        redirect('/especialistas');
    }
}
