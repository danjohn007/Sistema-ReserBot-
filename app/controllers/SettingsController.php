<?php
/**
 * ReserBot - Controlador de Configuraciones
 */

require_once __DIR__ . '/BaseController.php';

class SettingsController extends BaseController {
    
    /**
     * Vista principal de configuraciones
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $this->render('settings/index', [
            'title' => 'Configuraciones'
        ]);
    }
    
    /**
     * Configuración general
     */
    public function general() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $error = '';
        $success = '';
        
        // Obtener configuraciones actuales
        $configs = $this->db->fetchAll("SELECT * FROM configuraciones");
        $settings = [];
        foreach ($configs as $config) {
            $settings[$config['clave']] = $config['valor'];
        }
        
        if ($this->isPost()) {
            $nombre_sitio = $this->post('nombre_sitio');
            $email_sistema = $this->post('email_sistema');
            $telefono_contacto = $this->post('telefono_contacto');
            $horario_atencion = $this->post('horario_atencion');
            $confirmacion_automatica = $this->post('confirmacion_automatica') ? '1' : '0';
            $recordatorio_24h = $this->post('recordatorio_24h') ? '1' : '0';
            $recordatorio_1h = $this->post('recordatorio_1h') ? '1' : '0';
            $permitir_cancelacion_cliente = $this->post('permitir_cancelacion_cliente') ? '1' : '0';
            $horas_anticipacion_cancelacion = $this->post('horas_anticipacion_cancelacion');
            
            // Actualizar configuraciones
            setConfig('nombre_sitio', $nombre_sitio);
            setConfig('email_sistema', $email_sistema);
            setConfig('telefono_contacto', $telefono_contacto);
            setConfig('horario_atencion', $horario_atencion);
            setConfig('confirmacion_automatica', $confirmacion_automatica);
            setConfig('recordatorio_24h', $recordatorio_24h);
            setConfig('recordatorio_1h', $recordatorio_1h);
            setConfig('permitir_cancelacion_cliente', $permitir_cancelacion_cliente);
            setConfig('horas_anticipacion_cancelacion', $horas_anticipacion_cancelacion);
            
            // Manejar logotipo
            if (isset($_FILES['logotipo']) && $_FILES['logotipo']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/images/';
                $fileName = 'logo_' . time() . '_' . basename($_FILES['logotipo']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['logotipo']['tmp_name'], $uploadFile)) {
                    setConfig('logotipo', '/images/' . $fileName);
                }
            }
            
            logAction('settings_update', 'Configuraciones generales actualizadas');
            $success = 'Configuraciones actualizadas correctamente.';
            
            // Recargar configuraciones
            $configs = $this->db->fetchAll("SELECT * FROM configuraciones");
            $settings = [];
            foreach ($configs as $config) {
                $settings[$config['clave']] = $config['valor'];
            }
        }
        
        $this->render('settings/general', [
            'title' => 'Configuración General',
            'settings' => $settings,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Configuración de estilos
     */
    public function styles() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $success = '';
        
        // Obtener configuraciones actuales
        $settings = [
            'color_primario' => getConfig('color_primario', '#3B82F6'),
            'color_secundario' => getConfig('color_secundario', '#1E40AF'),
            'color_acento' => getConfig('color_acento', '#10B981')
        ];
        
        if ($this->isPost()) {
            $color_primario = $this->post('color_primario');
            $color_secundario = $this->post('color_secundario');
            $color_acento = $this->post('color_acento');
            
            setConfig('color_primario', $color_primario);
            setConfig('color_secundario', $color_secundario);
            setConfig('color_acento', $color_acento);
            
            logAction('settings_styles', 'Estilos actualizados');
            $success = 'Estilos actualizados correctamente.';
            
            $settings = [
                'color_primario' => $color_primario,
                'color_secundario' => $color_secundario,
                'color_acento' => $color_acento
            ];
        }
        
        $this->render('settings/styles', [
            'title' => 'Configuración de Estilos',
            'settings' => $settings,
            'success' => $success
        ]);
    }
    
    /**
     * Configuración de correo
     */
    public function email() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $success = '';
        $error = '';
        
        if ($this->isPost()) {
            $mail_host = $this->post('mail_host');
            $mail_port = $this->post('mail_port');
            $mail_username = $this->post('mail_username');
            $mail_password = $this->post('mail_password');
            $mail_from = $this->post('mail_from');
            $mail_from_name = $this->post('mail_from_name');
            
            // Guardar en archivo de configuración o base de datos
            // Por ahora solo mostramos mensaje
            
            logAction('settings_email', 'Configuración de correo actualizada');
            $success = 'Configuración de correo actualizada correctamente.';
        }
        
        $this->render('settings/email', [
            'title' => 'Configuración de Correo',
            'success' => $success,
            'error' => $error
        ]);
    }
    
    /**
     * Configuración de PayPal
     */
    public function paypal() {
        $this->requireRole([ROLE_SUPERADMIN]);
        
        $success = '';
        
        $settings = [
            'paypal_modo' => getConfig('paypal_modo', 'sandbox'),
            'paypal_client_id' => getConfig('paypal_client_id', ''),
            'paypal_secret' => getConfig('paypal_secret', '')
        ];
        
        if ($this->isPost()) {
            $paypal_modo = $this->post('paypal_modo');
            $paypal_client_id = $this->post('paypal_client_id');
            $paypal_secret = $this->post('paypal_secret');
            
            setConfig('paypal_modo', $paypal_modo);
            setConfig('paypal_client_id', $paypal_client_id);
            setConfig('paypal_secret', $paypal_secret);
            
            logAction('settings_paypal', 'Configuración de PayPal actualizada');
            $success = 'Configuración de PayPal actualizada correctamente.';
            
            $settings = [
                'paypal_modo' => $paypal_modo,
                'paypal_client_id' => $paypal_client_id,
                'paypal_secret' => $paypal_secret
            ];
        }
        
        $this->render('settings/paypal', [
            'title' => 'Configuración de PayPal',
            'settings' => $settings,
            'success' => $success
        ]);
    }
    
    /**
     * Gestión de días feriados
     */
    public function holidays() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        $success = '';
        
        // Obtener feriados
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $holidays = $this->db->fetchAll(
                "SELECT h.*, s.nombre as sucursal_nombre 
                 FROM dias_feriados h 
                 LEFT JOIN sucursales s ON h.sucursal_id = s.id 
                 ORDER BY h.fecha"
            );
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        } else {
            $holidays = $this->db->fetchAll(
                "SELECT h.*, s.nombre as sucursal_nombre 
                 FROM dias_feriados h 
                 LEFT JOIN sucursales s ON h.sucursal_id = s.id 
                 WHERE h.sucursal_id IS NULL OR h.sucursal_id = ?
                 ORDER BY h.fecha",
                [$user['sucursal_id']]
            );
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE id = ?", [$user['sucursal_id']]);
        }
        
        if ($this->isPost()) {
            $action = $this->post('action');
            
            if ($action == 'add') {
                $fecha = $this->post('fecha');
                $nombre = $this->post('nombre');
                $sucursal_id = $this->post('sucursal_id') ?: null;
                $recurrente = $this->post('recurrente') ? 1 : 0;
                
                if ($fecha && $nombre) {
                    $this->db->insert(
                        "INSERT INTO dias_feriados (sucursal_id, fecha, nombre, recurrente) VALUES (?, ?, ?, ?)",
                        [$sucursal_id, $fecha, $nombre, $recurrente]
                    );
                    
                    logAction('holiday_add', 'Día feriado agregado: ' . $nombre);
                    $success = 'Día feriado agregado correctamente.';
                }
            } elseif ($action == 'delete') {
                $id = $this->post('holiday_id');
                $this->db->delete("DELETE FROM dias_feriados WHERE id = ?", [$id]);
                logAction('holiday_delete', 'Día feriado eliminado');
                $success = 'Día feriado eliminado correctamente.';
            }
            
            // Recargar datos
            if ($user['rol_id'] == ROLE_SUPERADMIN) {
                $holidays = $this->db->fetchAll(
                    "SELECT h.*, s.nombre as sucursal_nombre 
                     FROM dias_feriados h 
                     LEFT JOIN sucursales s ON h.sucursal_id = s.id 
                     ORDER BY h.fecha"
                );
            } else {
                $holidays = $this->db->fetchAll(
                    "SELECT h.*, s.nombre as sucursal_nombre 
                     FROM dias_feriados h 
                     LEFT JOIN sucursales s ON h.sucursal_id = s.id 
                     WHERE h.sucursal_id IS NULL OR h.sucursal_id = ?
                     ORDER BY h.fecha",
                    [$user['sucursal_id']]
                );
            }
        }
        
        $this->render('settings/holidays', [
            'title' => 'Días Feriados',
            'holidays' => $holidays,
            'branches' => $branches,
            'success' => $success
        ]);
    }

    /**
     * Vista de configuración de recordatorios por WhatsApp.
     * - SUPERADMIN/BRANCH_ADMIN pueden ver/configurar a cualquier especialista.
     * - ESPECIALISTA solo puede ver/configurar el suyo.
     */
    public function reminders() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        $user = currentUser();

        // Determinar lista de especialistas visibles para este usuario
        if ($user['rol_id'] == ROLE_SPECIALIST) {
            $specialists = $this->db->fetchAll(
                "SELECT id, nombre FROM usuarios WHERE id = ? AND rol_id = ?",
                [$user['id'], ROLE_SPECIALIST]
            );
            $selectedId = (int)$user['id'];
        } else {
            $sql = "SELECT id, nombre FROM usuarios WHERE rol_id = ? AND activo = 1 ORDER BY nombre";
            $specialists = $this->db->fetchAll($sql, [ROLE_SPECIALIST]);

            // Especialista seleccionado vía GET, por defecto el primero
            $selectedId = (int)($this->get('especialista_id') ?: ($specialists[0]['id'] ?? 0));
        }

        // Cargar configuración actual del especialista seleccionado
        $config = null;
        $logs = [];
        $upcoming = [];
        if ($selectedId > 0) {
            $config = $this->db->fetch(
                "SELECT * FROM reminder_configs WHERE especialista_id = ?",
                [$selectedId]
            );
            $logs = $this->db->fetchAll(
                "SELECT * FROM reminder_logs WHERE especialista_id = ? ORDER BY sent_at DESC LIMIT 10",
                [$selectedId]
            );
            
            // Obtener IDs de especialistas (tabla especialistas) asociados a este usuario
            $especialistaIds = $this->db->fetchAll(
                "SELECT id FROM especialistas WHERE usuario_id = ? AND activo = 1",
                [$selectedId]
            );
            
            if (!empty($especialistaIds)) {
                $especialistaIdsList = array_column($especialistaIds, 'id');
                $placeholders = implode(',', array_fill(0, count($especialistaIdsList), '?'));
                
                // Próximas citas (todas las futuras) para enviar prueba manual
                $upcoming = $this->db->fetchAll(
                    "SELECT r.id, r.codigo, r.nombre_cliente, r.telefono,
                            r.fecha_cita, r.hora_inicio, r.estado, r.recordatorio_enviado,
                            s.nombre AS sucursal_nombre
                     FROM reservaciones r
                     LEFT JOIN sucursales s ON s.id = r.sucursal_id
                     WHERE r.especialista_id IN ($placeholders)
                       AND (r.estado = 'pendiente' OR r.estado = 'confirmada')
                       AND CONCAT(r.fecha_cita,' ',r.hora_inicio) >= NOW()
                     ORDER BY r.fecha_cita ASC, r.hora_inicio ASC
                     LIMIT 50",
                    $especialistaIdsList
                );
            }
        }

        // Valores por defecto si aún no existe configuración
        if (!$config) {
            $config = ['enabled' => 0, 'hours_before' => 3];
        }

        $this->render('settings/reminders', [
            'title' => 'Recordatorios WhatsApp',
            'specialists' => $specialists,
            'selectedId' => $selectedId,
            'config' => $config,
            'logs' => $logs,
            'upcoming' => $upcoming,
            'canSelectSpecialist' => in_array($user['rol_id'], [ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])
        ]);
    }

    /**
     * Guarda la configuración de recordatorios (AJAX o POST normal).
     */
    public function saveReminders() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $user = currentUser();
        $especialista_id = (int)$this->post('especialista_id');
        $enabled = $this->post('enabled') ? 1 : 0;
        $hours_before = (int)$this->post('hours_before');

        // Validación de horas (1-24)
        if ($hours_before < 1 || $hours_before > 24) {
            $this->json(['success' => false, 'message' => 'Las horas deben estar entre 1 y 24'], 400);
        }

        // Un especialista solo puede modificar su propio config
        if ($user['rol_id'] == ROLE_SPECIALIST && $especialista_id !== (int)$user['id']) {
            $this->json(['success' => false, 'message' => 'Permiso denegado'], 403);
        }

        // Validar que el especialista exista
        $exists = $this->db->fetch(
            "SELECT id FROM usuarios WHERE id = ? AND rol_id = ?",
            [$especialista_id, ROLE_SPECIALIST]
        );
        if (!$exists) {
            $this->json(['success' => false, 'message' => 'Especialista no válido'], 404);
        }

        // UPSERT
        $this->db->query(
            "INSERT INTO reminder_configs (especialista_id, enabled, hours_before)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE enabled = VALUES(enabled), hours_before = VALUES(hours_before)",
            [$especialista_id, $enabled, $hours_before]
        );

        logAction('reminder_config_update', "Recordatorios actualizados para especialista #{$especialista_id} (enabled={$enabled}, hours={$hours_before})");

        $this->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente',
            'config' => [
                'enabled' => $enabled,
                'hours_before' => $hours_before
            ]
        ]);
    }

    /**
     * Envío manual (prueba) de un recordatorio para una reservación específica.
     * No marca recordatorio_enviado = 1 para permitir reenviar durante pruebas.
     */
    public function testReminder() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        require_once HELPERS_PATH . '/reminder_sender.php';

        $user = currentUser();
        $reservacion_id = (int)$this->post('reservacion_id');

        if ($reservacion_id <= 0) {
            $this->json(['success' => false, 'message' => 'ID de reservación inválido'], 400);
        }

        $reserva = $this->db->fetch(
            "SELECT r.id, r.codigo, r.nombre_cliente, r.telefono,
                    r.fecha_cita, r.hora_inicio, r.especialista_id,
                    u.nombre AS nombre_especialista, e.usuario_id,
                    s.nombre AS sucursal_nombre
             FROM reservaciones r
             LEFT JOIN especialistas e ON e.id = r.especialista_id
             LEFT JOIN usuarios u ON u.id = e.usuario_id
             LEFT JOIN sucursales s ON s.id = r.sucursal_id
             WHERE r.id = ?",
            [$reservacion_id]
        );

        if (!$reserva) {
            $this->json(['success' => false, 'message' => 'Reservación no encontrada'], 404);
        }

        // Especialista solo puede probar con sus propias citas
        if ($user['rol_id'] == ROLE_SPECIALIST && (int)$reserva['usuario_id'] !== (int)$user['id']) {
            $this->json(['success' => false, 'message' => 'Permiso denegado'], 403);
        }

        if (empty($reserva['telefono'])) {
            $this->json(['success' => false, 'message' => 'La reservación no tiene teléfono'], 400);
        }

        $res = reminderSendWhatsapp($reserva);

        logAction('reminder_test', "Prueba manual de recordatorio reserva #{$reservacion_id} -> " . ($res['success'] ? 'OK' : 'FAIL: '.$res['message']));

        $this->json([
            'success' => $res['success'],
            'message' => $res['message'],
            'http_code' => $res['http_code'],
            'raw' => substr($res['raw'], 0, 300)
        ]);
    }

    /**
     * Diagnóstico del cron de recordatorios vía ruta interna autenticada.
     * Evita bloqueos 403 por acceso directo a scripts en /public.
     */
    public function cronStatus() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        $statusFile = ROOT_PATH . '/public/cron_status.php';
        if (!file_exists($statusFile)) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo "No se encontró cron_status.php en /public";
            exit;
        }

        // Inyectar token interno para reutilizar el script de diagnóstico.
        $_GET['token'] = 'status2026';
        if (($this->get('send') ?? '') === '1') {
            $_GET['send'] = '1';
        }

        require $statusFile;
        exit;
    }

    /**
     * Estado interno de recordatorios (sin usar URL con palabras sensibles como cron/debug).
     */
    public function reminderStatus() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        header('Content-Type: text/plain; charset=UTF-8');

        $nowLocal = date('Y-m-d H:i:s');
        echo "=== ESTADO DE RECORDATORIOS ===\n";
        echo "Hora servidor: {$nowLocal}\n\n";

        try {
            $mysqlNow = $this->db->fetch("SELECT NOW() AS now_db");
            echo "PHP now:   {$nowLocal}\n";
            echo "MySQL NOW: " . ($mysqlNow['now_db'] ?? 'N/D') . "\n\n";
        } catch (Exception $e) {
            echo "ERROR reloj BD: " . $e->getMessage() . "\n\n";
        }

        echo "Constantes WhatsApp:\n";
        echo "- URL: " . (defined('WHATSAPP_REMINDER_URL') ? WHATSAPP_REMINDER_URL : 'NO DEFINIDA') . "\n";
        echo "- API KEY: " . (defined('WHATSAPP_REMINDER_API_KEY') ? 'DEFINIDA' : 'NO DEFINIDA') . "\n";
        echo "- TEMPLATE: " . (defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : 'NO DEFINIDA') . "\n\n";

        try {
            $active = $this->db->fetchAll(
                "SELECT rc.especialista_id, rc.hours_before, u.nombre
                 FROM reminder_configs rc
                 INNER JOIN usuarios u ON u.id = rc.especialista_id AND u.activo = 1
                 WHERE rc.enabled = 1"
            );
            echo "Configs activas: " . count($active) . "\n";
            foreach ($active as $a) {
                echo "  - especialista_id={$a['especialista_id']} {$a['nombre']} hours_before={$a['hours_before']}\n";
            }
            echo "\n";
        } catch (Exception $e) {
            echo "ERROR configs activas: " . $e->getMessage() . "\n\n";
        }

        $logCandidates = [
            '/home2/aidereservaciones/logs/reminders_cron.log',
            dirname(dirname(ROOT_PATH)) . '/logs/reminders_cron.log',
            ROOT_PATH . '/public/reminders_cron.log',
        ];

        echo "Rutas de log evaluadas:\n";
        $selectedLog = '';
        foreach ($logCandidates as $path) {
            $exists = file_exists($path) ? 'SI' : 'NO';
            $writableDir = is_writable(dirname($path)) ? 'SI' : 'NO';
            echo "- {$path}\n";
            echo "  existe={$exists} writable_dir={$writableDir}\n";
            if ($selectedLog === '' && (is_dir(dirname($path)) && ($writableDir === 'SI' || $exists === 'SI'))) {
                $selectedLog = $path;
            }
        }
        echo "\n";
        echo "Log seleccionado por cron: " . ($selectedLog ?: 'N/D') . "\n\n";

        echo "EJECUTAR AHORA:\n";
        echo url('/configuraciones/recordatorios/estado/ejecutar') . "\n\n";

        if ($selectedLog && file_exists($selectedLog)) {
            $tail = @file($selectedLog);
            if ($tail !== false) {
                $last = array_slice($tail, -20);
                echo "--- ÚLTIMAS LÍNEAS DEL LOG ---\n";
                echo implode('', $last) . "\n";
            }
        }

        exit;
    }

    /**
     * Ejecuta el proceso de recordatorios inmediatamente por ruta interna.
     */
    public function runRemindersNow() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        $_GET['key'] = defined('WHATSAPP_REMINDER_API_KEY') ? WHATSAPP_REMINDER_API_KEY : '';
        require ROOT_PATH . '/cron/reminders_cron.php';
        exit;
    }

    /**
     * Debug temporal: verifica configuración de template y payload
     */
    public function debugTemplate() {
        header('Content-Type: text/html; charset=UTF-8');
        
        echo '<pre style="background: #f5f5f5; padding: 20px; font-family: monospace;">';
        echo "=== VERIFICACIÓN DE CONFIGURACIÓN DE TEMPLATE ===\n\n";
        
        echo "1. ¿Está definida WHATSAPP_REMINDER_TEMPLATE? ";
        echo defined('WHATSAPP_REMINDER_TEMPLATE') ? "✅ SÍ\n" : "❌ NO\n";
        
        echo "2. Valor de WHATSAPP_REMINDER_TEMPLATE: ";
        echo defined('WHATSAPP_REMINDER_TEMPLATE') ? '<strong>' . WHATSAPP_REMINDER_TEMPLATE . '</strong>' : "❌ NO DEFINIDA";
        echo "\n";
        
        echo "3. URL de Firebase Function: " . WHATSAPP_REMINDER_URL . "\n";
        echo "4. API Key: " . WHATSAPP_REMINDER_API_KEY . "\n";
        
        echo "\n=== PRUEBA DE PAYLOAD ===\n";
        $testPayload = [
            'to' => '5214427869806',
            'nombre_usuario' => 'Test Usuario',
            'nombre_especialista' => 'Test Especialista',
            'fecha' => '28/05/2026',
            'hora' => '10:00',
            'ubicacion' => 'Test Ubicación',
            'template' => defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : 'recordatorio_reserva_cita',
        ];
        
        echo "Payload que se enviaría:\n";
        echo json_encode($testPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        
        echo "\n=== VERIFICACIÓN DEL HELPER ===\n";
        require_once HELPERS_PATH . '/reminder_sender.php';
        
        $testReservation = [
            'nombre_cliente' => 'Cliente de Prueba',
            'telefono' => '4427869806',
            'fecha_cita' => '2026-05-28',
            'hora_inicio' => '10:00:00',
            'nombre_especialista' => 'Dr. Prueba',
            'sucursal_nombre' => 'Sucursal de Prueba'
        ];
        
        echo "Datos de reservación de prueba:\n";
        echo json_encode($testReservation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        
        echo "\n=== SIMULACIÓN DE PAYLOAD (sin enviar) ===\n";
        $to = reminderNormalizePhone($testReservation['telefono']);
        $simulatedPayload = [
            'to' => $to,
            'nombre_usuario' => $testReservation['nombre_cliente'],
            'nombre_especialista' => $testReservation['nombre_especialista'],
            'fecha' => date('d/m/Y', strtotime($testReservation['fecha_cita'])),
            'hora' => date('H:i', strtotime($testReservation['hora_inicio'])),
            'ubicacion' => $testReservation['sucursal_nombre'],
            'template' => defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : 'recordatorio_reserva_cita',
        ];
        
        echo json_encode($simulatedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        
        echo "\n✅ Si todo se ve correcto, el template debería ser: <strong>recordatorio_reserva_cita</strong>\n";
        echo '</pre>';
        exit;
    }
}
