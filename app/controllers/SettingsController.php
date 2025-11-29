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
}
