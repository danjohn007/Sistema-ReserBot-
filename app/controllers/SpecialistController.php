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
                             (especialista_id, dia_semana, hora_inicio, hora_fin, activo, 
                              hora_inicio_bloqueo, hora_fin_bloqueo, bloqueo_activo) 
                             VALUES (?, ?, ?, ?, 1, ?, ?, ?)",
                            [$id, $day, $inicio, $fin, 
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
                    
                    // Debug: ver valores
                    error_log("Actualizando servicio $serviceId - Precio: $precio, Duración: $duracion, Activo: $activo");
                    
                    $this->db->update(
                        "UPDATE especialistas_servicios 
                         SET precio_personalizado = ?, duracion_personalizada = ?, activo = ?
                         WHERE especialista_id = ? AND servicio_id = ?",
                        [$precio, $duracion, $activo, $specialist['id'], $serviceId]
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
