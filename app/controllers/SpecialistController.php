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
                "SELECT e.*, u.nombre, u.apellidos, u.email, u.telefono, 
                 GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR '|') as sucursales_nombres
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 JOIN sucursales s ON e.sucursal_id = s.id
                 WHERE e.activo = 1
                 GROUP BY u.id
                 ORDER BY u.nombre, u.apellidos"
            );
        } else {
            $specialists = $this->db->fetchAll(
                "SELECT e.*, u.nombre, u.apellidos, u.email, u.telefono,
                 GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR '|') as sucursales_nombres
                 FROM especialistas e
                 JOIN usuarios u ON e.usuario_id = u.id
                 JOIN sucursales s ON e.sucursal_id = s.id
                 WHERE e.sucursal_id = ? AND e.activo = 1
                 GROUP BY u.id
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
        
        // Obtener categorías para el modal de servicios
        $categories = $this->db->fetchAll("SELECT id, nombre FROM categorias_servicios WHERE activo = 1 ORDER BY nombre");
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $password = $this->post('password');
            $sucursales = isset($_POST['sucursales']) ? $_POST['sucursales'] : [];
            $profesion = $this->post('profesion');
            $especialidad = $this->post('especialidad');
            $descripcion = $this->post('descripcion');
            $experiencia_anos = $this->post('experiencia_anos');
            $tarifa_base = $this->post('tarifa_base');
            $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];
            
            // Validaciones
            if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
                $error = 'Los campos nombre, apellidos, email y contraseña son obligatorios.';
            } elseif (empty($sucursales)) {
                $error = 'Debes seleccionar al menos una sucursal.';
            } elseif (!validateEmail($email)) {
                $error = 'Ingrese un correo electrónico válido.';
            } else {
                // Verificar si el email ya existe
                $exists = $this->db->fetch("SELECT id FROM usuarios WHERE email = ?", [$email]);
                
                if ($exists) {
                    $error = 'Ya existe un usuario con este correo electrónico.';
                } else {
                    // Usar la primera sucursal seleccionada como sucursal principal del usuario
                    $sucursal_principal = $sucursales[0];
                    
                    // Crear usuario
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $userId = $this->db->insert(
                        "INSERT INTO usuarios (nombre, apellidos, email, telefono, password, rol_id, sucursal_id, email_verificado, activo) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)",
                        [$nombre, $apellidos, $email, $telefono, $hashedPassword, ROLE_SPECIALIST, $sucursal_principal]
                    );
                    
                    if ($userId) {
                        // Crear un registro de especialista por cada sucursal seleccionada
                        $specialistIds = [];
                        foreach ($sucursales as $sucursal_id) {
                            $specialistId = $this->db->insert(
                                "INSERT INTO especialistas (usuario_id, sucursal_id, profesion, especialidad, descripcion, experiencia_anos, tarifa_base) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                                [$userId, $sucursal_id, $profesion, $especialidad, $descripcion, $experiencia_anos, $tarifa_base]
                            );
                            $specialistIds[] = $specialistId;
                        }
                        
                        // Asignar servicios a todos los registros de especialista creados
                        foreach ($specialistIds as $specialistId) {
                            foreach ($servicios as $servicioId) {
                                $this->db->insert(
                                    "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                                    [$specialistId, $servicioId]
                                );
                            }
                        }
                        
                        $sucursalesCount = count($sucursales);
                        logAction('specialist_create', "Especialista creado: $nombre $apellidos en $sucursalesCount sucursal(es)");
                        setFlashMessage('success', 'Especialista creado correctamente en ' . $sucursalesCount . ' sucursal(es).');
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
            'categories' => $categories,
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
        
        // Obtener todas las sucursales donde trabaja el especialista
        $specialistBranches = $this->db->fetchAll(
            "SELECT DISTINCT s.id, s.nombre 
             FROM especialistas e
             JOIN sucursales s ON e.sucursal_id = s.id
             WHERE e.usuario_id = ? AND s.activo = 1
             ORDER BY s.nombre",
            [$specialist['usuario_id']]
        );
        
        // Obtener categorías para el modal de servicios
        $categories = $this->db->fetchAll("SELECT id, nombre FROM categorias_servicios WHERE activo = 1 ORDER BY nombre");
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $apellidos = $this->post('apellidos');
            $email = $this->post('email');
            $telefono = $this->post('telefono');
            $sucursales = isset($_POST['sucursales']) ? $_POST['sucursales'] : [];
            $profesion = $this->post('profesion');
            $especialidad = $this->post('especialidad');
            $descripcion = $this->post('descripcion');
            $experiencia_anos = $this->post('experiencia_anos');
            $tarifa_base = $this->post('tarifa_base');
            $activo = $this->post('activo') ? 1 : 0;
            $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];
            
            if (empty($nombre) || empty($apellidos) || empty($email)) {
                $error = 'Los campos nombre, apellidos y email son obligatorios.';
            } elseif (empty($sucursales)) {
                $error = 'Debes seleccionar al menos una sucursal.';
            } else {
                // Usar la primera sucursal como sucursal principal del usuario
                $sucursal_principal = $sucursales[0];
                
                // Actualizar usuario
                $this->db->update(
                    "UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, telefono = ?, sucursal_id = ? 
                     WHERE id = ?",
                    [$nombre, $apellidos, $email, $telefono, $sucursal_principal, $specialist['usuario_id']]
                );
                
                // Eliminar todos los registros de especialista actuales de este usuario
                $this->db->delete("DELETE FROM especialistas WHERE usuario_id = ?", [$specialist['usuario_id']]);
                
                // Crear un registro de especialista por cada sucursal seleccionada
                $specialistIds = [];
                foreach ($sucursales as $sucursal_id) {
                    $specialistId = $this->db->insert(
                        "INSERT INTO especialistas (usuario_id, sucursal_id, profesion, especialidad, descripcion, experiencia_anos, tarifa_base, activo) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$specialist['usuario_id'], $sucursal_id, $profesion, $especialidad, $descripcion, $experiencia_anos, $tarifa_base, $activo]
                    );
                    $specialistIds[] = $specialistId;
                }
                
                // Asignar servicios a todos los registros de especialista creados
                foreach ($specialistIds as $specialistId) {
                    foreach ($servicios as $servicioId) {
                        $this->db->insert(
                            "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                            [$specialistId, $servicioId]
                        );
                    }
                }
                
                $sucursalesCount = count($sucursales);
                logAction('specialist_update', "Especialista actualizado: $nombre $apellidos en $sucursalesCount sucursal(es)");
                setFlashMessage('success', 'Especialista actualizado correctamente en ' . $sucursalesCount . ' sucursal(es).');
                redirect('/especialistas');
            }
        }
        
        $this->render('specialists/edit', [
            'title' => 'Editar Especialista',
            'specialist' => $specialist,
            'branches' => $branches,
            'specialistBranches' => $specialistBranches,
            'services' => $services,
            'currentServiceIds' => $currentServiceIds,
            'categories' => $categories,
            'error' => $error
        ]);
    }
    
    /**
     * Gestionar horarios del especialista
     */
    public function schedules() {
        $this->requireAuth();
        
        $user = currentUser();
        $usuario_id = null;
        
        // Si es especialista, usar su propio usuario_id
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            $usuario_id = $user['id'];
        } else {
            $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
            
            // Obtener el usuario_id del especialista seleccionado
            // Puede venir por 'id' (desde lista), 'specialist_id' (desde pestañas GET o POST)
            $id = $this->get('id');
            $specialist_id = $this->get('specialist_id') ?: $this->post('specialist_id');
            
            if ($specialist_id) {
                // Viene de cambio de pestaña o POST
                $temp = $this->db->fetch("SELECT usuario_id FROM especialistas WHERE id = ?", [$specialist_id]);
            } elseif ($id) {
                // Viene de la lista de especialistas
                $temp = $this->db->fetch("SELECT usuario_id FROM especialistas WHERE id = ?", [$id]);
            } else {
                $temp = null;
            }
            
            if (!$temp) {
                setFlashMessage('error', 'Especialista no encontrado.');
                redirect('/especialistas');
            }
            $usuario_id = $temp['usuario_id'];
        }
        
        // Obtener TODOS los registros de especialistas de este usuario (una por sucursal)
        $allSpecialists = $this->db->fetchAll(
            "SELECT e.*, s.nombre as sucursal_nombre, u.nombre, u.apellidos 
             FROM especialistas e 
             JOIN sucursales s ON e.sucursal_id = s.id 
             JOIN usuarios u ON e.usuario_id = u.id
             WHERE e.usuario_id = ? AND e.activo = 1
             ORDER BY s.nombre",
            [$usuario_id]
        );
        
        if (empty($allSpecialists)) {
            setFlashMessage('error', 'No tiene perfil de especialista.');
            redirect('/dashboard');
        }
        
        // Determinar qué especialista_id estamos editando
        $current_specialist_id = $this->get('specialist_id');
        if (!$current_specialist_id) {
            // Por defecto, el primero
            $current_specialist_id = $allSpecialists[0]['id'];
        }
        
        // Obtener el especialista actual
        $specialist = null;
        foreach ($allSpecialists as $spec) {
            if ($spec['id'] == $current_specialist_id) {
                $specialist = $spec;
                break;
            }
        }
        
        if (!$specialist) {
            $specialist = $allSpecialists[0];
            $current_specialist_id = $specialist['id'];
        }
        
        // Obtener horarios del especialista actual (sucursal específica)
        $schedules = $this->db->fetchAll(
            "SELECT * FROM horarios_especialistas WHERE especialista_id = ? ORDER BY dia_semana, hora_inicio",
            [$current_specialist_id]
        );
        
        // Obtener bloqueos del especialista actual
        $blocks = $this->db->fetchAll(
            "SELECT * FROM bloqueos_horario WHERE especialista_id = ? AND fecha_fin >= NOW() ORDER BY fecha_inicio",
            [$current_specialist_id]
        );
        
        if ($this->isPost()) {
            $action = $this->post('action');
            
            if ($action == 'save_schedule') {
                // Obtener el specialist_id del formulario
                $form_specialist_id = $this->post('specialist_id');
                
                // Validar que el specialist_id existe
                if (!$form_specialist_id) {
                    setFlashMessage('error', 'ID de especialista no proporcionado.');
                    redirect('/especialistas/horarios?specialist_id=' . $current_specialist_id);
                    return;
                }
                
                // Verificar que el especialista existe y pertenece al mismo usuario
                $validSpecialist = $this->db->fetch(
                    "SELECT id FROM especialistas WHERE id = ? AND usuario_id = ?",
                    [$form_specialist_id, $usuario_id]
                );
                
                if (!$validSpecialist) {
                    setFlashMessage('error', 'Especialista no encontrado o no autorizado.');
                    redirect('/especialistas/horarios?specialist_id=' . $current_specialist_id);
                    return;
                }
                
                // Eliminar horarios anteriores de esta sucursal específica
                $this->db->delete("DELETE FROM horarios_especialistas WHERE especialista_id = ?", [$form_specialist_id]);
                
                // Guardar nuevos horarios
                for ($day = 1; $day <= 7; $day++) {
                    $inicio = $this->post('hora_inicio_' . $day);
                    $fin = $this->post('hora_fin_' . $day);
                    $activo = $this->post('activo_' . $day) ? 1 : 0;
                    
                    // Bloqueo
                    $bloqueo_activo = $this->post('bloqueo_activo_' . $day) ? 1 : 0;
                    $hora_inicio_bloqueo = $this->post('hora_inicio_bloqueo_' . $day);
                    $hora_fin_bloqueo = $this->post('hora_fin_bloqueo_' . $day);
                    
                    // Emergencia
                    $emergencia_activa = $this->post('emergencia_activa_' . $day) ? 1 : 0;
                    $hora_inicio_emergencia = $this->post('hora_inicio_emergencia_' . $day);
                    $hora_fin_emergencia = $this->post('hora_fin_emergencia_' . $day);
                    
                    if ($activo && $inicio && $fin) {
                        // Validar que la hora de inicio sea menor que la hora de fin
                        if (strtotime($inicio) >= strtotime($fin)) {
                            setFlashMessage('error', 'La hora de inicio debe ser menor que la hora de fin.');
                            redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                            return;
                        }
                        
                        // Validar bloqueo si está activo
                        if ($bloqueo_activo && $hora_inicio_bloqueo && $hora_fin_bloqueo) {
                            // El bloqueo debe estar dentro del horario laboral
                            if (strtotime($hora_inicio_bloqueo) < strtotime($inicio) || 
                                strtotime($hora_fin_bloqueo) > strtotime($fin)) {
                                setFlashMessage('error', 'El horario de bloqueo debe estar dentro del horario laboral.');
                                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                                return;
                            }
                            
                            // La hora de inicio del bloqueo debe ser menor que la de fin
                            if (strtotime($hora_inicio_bloqueo) >= strtotime($hora_fin_bloqueo)) {
                                setFlashMessage('error', 'La hora de inicio del bloqueo debe ser menor que la hora de fin.');
                                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                                return;
                            }
                        }
                        
                        // Validar horario de emergencia si está activo
                        if ($emergencia_activa && $hora_inicio_emergencia && $hora_fin_emergencia) {
                            // La hora de inicio de emergencia debe ser menor que la de fin
                            if (strtotime($hora_inicio_emergencia) >= strtotime($hora_fin_emergencia)) {
                                setFlashMessage('error', 'La hora de inicio de emergencia debe ser menor que la hora de fin.');
                                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                                return;
                            }
                            
                            // El horario de emergencia NO puede estar dentro del horario normal
                            if ((strtotime($hora_inicio_emergencia) >= strtotime($inicio) && 
                                 strtotime($hora_inicio_emergencia) < strtotime($fin)) ||
                                (strtotime($hora_fin_emergencia) > strtotime($inicio) && 
                                 strtotime($hora_fin_emergencia) <= strtotime($fin)) ||
                                (strtotime($hora_inicio_emergencia) <= strtotime($inicio) && 
                                 strtotime($hora_fin_emergencia) >= strtotime($fin))) {
                                setFlashMessage('error', 'El horario de emergencia no puede estar dentro del horario laboral normal. Debe estar FUERA del horario regular.');
                                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                                return;
                            }
                            
                            // El horario de emergencia NO puede estar dentro del horario de bloqueo
                            if ($bloqueo_activo && $hora_inicio_bloqueo && $hora_fin_bloqueo) {
                                if ((strtotime($hora_inicio_emergencia) >= strtotime($hora_inicio_bloqueo) && 
                                     strtotime($hora_inicio_emergencia) < strtotime($hora_fin_bloqueo)) ||
                                    (strtotime($hora_fin_emergencia) > strtotime($hora_inicio_bloqueo) && 
                                     strtotime($hora_fin_emergencia) <= strtotime($hora_fin_bloqueo)) ||
                                    (strtotime($hora_inicio_emergencia) <= strtotime($hora_inicio_bloqueo) && 
                                     strtotime($hora_fin_emergencia) >= strtotime($hora_fin_bloqueo))) {
                                    setFlashMessage('error', 'El horario de emergencia no puede estar dentro del horario de bloqueo.');
                                    redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                                    return;
                                }
                            }
                        }
                        
                        $this->db->insert(
                            "INSERT INTO horarios_especialistas 
                             (especialista_id, dia_semana, hora_inicio, hora_fin, activo, 
                              hora_inicio_bloqueo, hora_fin_bloqueo, bloqueo_activo,
                              hora_inicio_emergencia, hora_fin_emergencia, emergencia_activa) 
                             VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)",
                            [$form_specialist_id, $day, $inicio, $fin, 
                             $bloqueo_activo ? $hora_inicio_bloqueo : null, 
                             $bloqueo_activo ? $hora_fin_bloqueo : null, 
                             $bloqueo_activo,
                             $emergencia_activa ? $hora_inicio_emergencia : null,
                             $emergencia_activa ? $hora_fin_emergencia : null,
                             $emergencia_activa]
                        );
                    }
                }
                
                setFlashMessage('success', 'Horarios actualizados correctamente.');
                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                return;
            } elseif ($action == 'add_block') {
                $form_specialist_id = $this->post('specialist_id');
                $fecha_inicio = $this->post('fecha_inicio');
                $fecha_fin = $this->post('fecha_fin');
                $motivo = $this->post('motivo');
                $tipo = $this->post('tipo');
                
                if (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
                    setFlashMessage('error', 'La fecha de inicio debe ser menor que la fecha de fin.');
                } else {
                    $this->db->insert(
                        "INSERT INTO bloqueos_horario (especialista_id, fecha_inicio, fecha_fin, motivo, tipo) VALUES (?, ?, ?, ?, ?)",
                        [$form_specialist_id, $fecha_inicio, $fecha_fin, $motivo, $tipo]
                    );
                    setFlashMessage('success', 'Bloqueo agregado correctamente.');
                }
                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                return;
            } elseif ($action == 'delete_block') {
                $form_specialist_id = $this->post('specialist_id');
                $block_id = $this->post('block_id');
                $this->db->delete("DELETE FROM bloqueos_horario WHERE id = ? AND especialista_id = ?", [$block_id, $form_specialist_id]);
                setFlashMessage('success', 'Bloqueo eliminado correctamente.');
                redirect('/especialistas/horarios?specialist_id=' . $form_specialist_id);
                return;
            }
            
            redirect('/especialistas/horarios?specialist_id=' . $current_specialist_id);
        }
        
        // Organizar horarios por día
        $schedulesByDay = [];
        foreach ($schedules as $schedule) {
            $schedulesByDay[$schedule['dia_semana']] = $schedule;
        }
        
        $this->render('specialists/schedules', [
            'title' => 'Horarios del Especialista',
            'specialist' => $specialist,
            'allSpecialists' => $allSpecialists,
            'currentSpecialistId' => $current_specialist_id,
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
        
        // Obtener el especialista
        $specialist = $this->db->fetch(
            "SELECT e.*, u.nombre, u.apellidos, u.id as usuario_id 
             FROM especialistas e 
             JOIN usuarios u ON e.usuario_id = u.id 
             WHERE e.id = ?",
            [$id]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'Especialista no encontrado.');
            redirect('/especialistas');
        }
        
        // Verificar si tiene reservaciones futuras o activas
        $hasActiveReservations = $this->db->fetch(
            "SELECT COUNT(*) as total 
             FROM reservaciones 
             WHERE especialista_id = ? 
             AND estado IN ('pendiente', 'confirmada', 'en_progreso') 
             AND (fecha_cita > CURDATE() OR (fecha_cita = CURDATE() AND hora_inicio > CURTIME()))",
            [$id]
        );
        
        if ($hasActiveReservations && $hasActiveReservations['total'] > 0) {
            setFlashMessage('error', 
                'No se puede eliminar al especialista porque tiene ' . 
                $hasActiveReservations['total'] . ' reservaciones activas o futuras. ' .
                'Por favor cancela o completa las reservaciones primero.');
            redirect('/especialistas');
        }
        
        try {
            $this->db->beginTransaction();
            
            // Obtener todos los registros de especialistas del mismo usuario
            $allSpecialistRecords = $this->db->fetchAll(
                "SELECT id FROM especialistas WHERE usuario_id = ?",
                [$specialist['usuario_id']]
            );
            
            // Eliminar todos los registros de especialistas del usuario
            // Las relaciones ON DELETE CASCADE se encargarán de:
            // - especialistas_servicios
            // - horarios_especialistas
            // - bloqueos_horario
            // - reservaciones (ya verificadas que no hay activas)
            foreach ($allSpecialistRecords as $record) {
                $this->db->delete("DELETE FROM especialistas WHERE id = ?", [$record['id']]);
            }
            
            // Eliminar el usuario asociado (esto eliminará todo por CASCADE)
            $this->db->delete("DELETE FROM usuarios WHERE id = ?", [$specialist['usuario_id']]);
            
            $this->db->commit();
            
            logAction('specialist_delete', 
                'Especialista eliminado permanentemente: ' . 
                $specialist['nombre'] . ' ' . $specialist['apellidos']);
            
            setFlashMessage('success', 
                'Especialista eliminado correctamente junto con todos sus datos asociados.');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar especialista: " . $e->getMessage());
            setFlashMessage('error', 
                'Error al eliminar el especialista. Por favor inténtalo de nuevo.');
        }
        
        redirect('/especialistas');
    }
    
    public function myServices()
    {
        $this->requireRole([ROLE_SPECIALIST]);
        
        // Obtener el especialista del usuario actual
        $specialist = $this->db->fetch(
            "SELECT id FROM especialistas WHERE usuario_id = ?",
            [$_SESSION['user']['id']]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'No se encontró el perfil del especialista.');
            redirect('/dashboard');
        }
        
        // Manejar actualización de precios
        if ($this->isPost()) {
            // Debug: verificar que entra aquí
            error_log("POST detectado en myServices");
            
            $services = isset($_POST['servicios']) ? $_POST['servicios'] : [];
            
            // Debug: ver qué servicios se reciben
            error_log("Servicios recibidos: " . print_r($services, true));
            
            if (!empty($services)) {
                foreach ($services as $serviceId => $data) {
                    $precio = !empty($data['precio']) ? floatval($data['precio']) : null;
                    $duracion = !empty($data['duracion']) ? intval($data['duracion']) : null;
                    $activo = isset($data['activo']) ? 1 : 0;
                    $es_emergencia = isset($data['es_emergencia']) ? 1 : 0;
                    
                    // Debug: ver valores
                    error_log("Actualizando servicio $serviceId - Precio: $precio, Duración: $duracion, Activo: $activo, Emergencia: $es_emergencia");
                    
                    $this->db->update(
                        "UPDATE especialistas_servicios 
                         SET precio_personalizado = ?, duracion_personalizada = ?, activo = ?, es_emergencia = ?
                         WHERE especialista_id = ? AND servicio_id = ?",
                        [$precio, $duracion, $activo, $es_emergencia, $specialist['id'], $serviceId]
                    );
                }
                
                logAction('specialist_services_update', 'Servicios actualizados (precios, duración y estado)');
                setFlashMessage('success', 'Servicios actualizados correctamente.');
            } else {
                error_log("Array de servicios vacío");
                setFlashMessage('info', 'No se enviaron cambios.');
            }
            
            redirect('/especialistas/mis-servicios');
        }
        
        // Obtener servicios del especialista
        $services = $this->db->fetchAll(
            "SELECT 
                s.id,
                s.nombre,
                s.descripcion,
                s.precio as precio_default,
                s.duracion_minutos as duracion_default,
                es.precio_personalizado,
                es.duracion_personalizada,
                es.activo,
                es.es_emergencia,
                c.nombre as categoria_nombre
            FROM especialistas_servicios es
            INNER JOIN servicios s ON es.servicio_id = s.id
            LEFT JOIN categorias_servicios c ON s.categoria_id = c.id
            WHERE es.especialista_id = ?
            ORDER BY c.nombre, s.nombre",
            [$specialist['id']]
        );
        
        // Obtener servicios disponibles (no asignados aún)
        $availableServices = $this->db->fetchAll(
            "SELECT s.id, s.nombre, s.precio, s.duracion_minutos, c.nombre as categoria_nombre
            FROM servicios s
            LEFT JOIN categorias_servicios c ON s.categoria_id = c.id
            WHERE s.activo = 1 
            AND s.id NOT IN (
                SELECT servicio_id FROM especialistas_servicios WHERE especialista_id = ?
            )
            ORDER BY c.nombre, s.nombre",
            [$specialist['id']]
        );
        
        // Obtener categorías para crear nuevos servicios
        $categories = $this->db->fetchAll(
            "SELECT id, nombre FROM categorias_servicios WHERE activo = 1 ORDER BY nombre"
        );
        
        $this->render('specialists/my-services', [
            'title' => 'Mis Servicios',
            'services' => $services,
            'availableServices' => $availableServices,
            'categories' => $categories,
            'specialistId' => $specialist['id']
        ]);
    }
    
    /**
     * Asignar servicio existente al especialista
     */
    public function assignService()
    {
        $this->requireRole([ROLE_SPECIALIST]);
        
        if (!$this->isPost()) {
            redirect('/especialistas/mis-servicios');
        }
        
        $specialist = $this->db->fetch(
            "SELECT id FROM especialistas WHERE usuario_id = ?",
            [$_SESSION['user']['id']]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'No se encontró el perfil del especialista.');
            redirect('/dashboard');
        }
        
        $servicio_id = $this->post('servicio_id');
        
        if (!$servicio_id) {
            setFlashMessage('error', 'Debe seleccionar un servicio.');
            redirect('/especialistas/mis-servicios');
        }
        
        // Verificar que el servicio existe y no está ya asignado
        $exists = $this->db->fetch(
            "SELECT id FROM servicios WHERE id = ? AND activo = 1",
            [$servicio_id]
        );
        
        $alreadyAssigned = $this->db->fetch(
            "SELECT id FROM especialistas_servicios WHERE especialista_id = ? AND servicio_id = ?",
            [$specialist['id'], $servicio_id]
        );
        
        if (!$exists) {
            setFlashMessage('error', 'El servicio seleccionado no existe.');
        } elseif ($alreadyAssigned) {
            setFlashMessage('error', 'Ya tienes este servicio asignado.');
        } else {
            $this->db->insert(
                "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                [$specialist['id'], $servicio_id]
            );
            
            logAction('specialist_service_assigned', 'Servicio asignado al especialista');
            setFlashMessage('success', 'Servicio agregado correctamente.');
        }
        
        redirect('/especialistas/mis-servicios');
    }
    
    /**
     * Crear nuevo servicio personal para el especialista
     */
    public function createPersonalService()
    {
        $this->requireRole([ROLE_SPECIALIST]);
        
        if (!$this->isPost()) {
            redirect('/especialistas/mis-servicios');
        }
        
        $specialist = $this->db->fetch(
            "SELECT id FROM especialistas WHERE usuario_id = ?",
            [$_SESSION['user']['id']]
        );
        
        if (!$specialist) {
            setFlashMessage('error', 'No se encontró el perfil del especialista.');
            redirect('/dashboard');
        }
        
        $nombre = $this->post('nombre');
        $descripcion = $this->post('descripcion');
        $categoria_id = $this->post('categoria_id');
        $precio = $this->post('precio');
        $duracion = $this->post('duracion');
        
        if (empty($nombre) || empty($precio) || empty($duracion) || empty($categoria_id)) {
            setFlashMessage('error', 'El nombre, categoría, precio y duración son obligatorios.');
            redirect('/especialistas/mis-servicios');
        }
        
        // Crear el servicio en la tabla principal
        $serviceId = $this->db->insert(
            "INSERT INTO servicios (categoria_id, nombre, descripcion, duracion_minutos, precio, activo) 
             VALUES (?, ?, ?, ?, ?, 1)",
            [$categoria_id, $nombre, $descripcion, $duracion, $precio]
        );
        
        if ($serviceId) {
            // Asignar el servicio al especialista
            $this->db->insert(
                "INSERT INTO especialistas_servicios (especialista_id, servicio_id) VALUES (?, ?)",
                [$specialist['id'], $serviceId]
            );
            
            logAction('specialist_service_created', 'Servicio personal creado: ' . $nombre);
            setFlashMessage('success', 'Servicio creado y agregado correctamente.');
        } else {
            setFlashMessage('error', 'Error al crear el servicio.');
        }
        
        redirect('/especialistas/mis-servicios');
    }
}
