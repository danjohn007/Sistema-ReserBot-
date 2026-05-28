<?php
/**
 * =====================================================================
 * ReserBot - Cron de recordatorios autom&aacute;ticos por WhatsApp
 * =====================================================================
 *
 * Se ejecuta cada 15 minutos (cron de cPanel). Para cada especialista
 * con `reminder_configs.enabled = 1`:
 *
 *   1. Calcula `target_datetime = NOW() + hours_before horas`.
 *   2. Busca reservaciones de ese especialista cuya `fecha_cita + hora_inicio`
 *      caiga dentro de la ventana [target_datetime, target_datetime + 15min)
 *      y que estén en estado pendiente/confirmada, con tel&eacute;fono y sin
 *      recordatorio enviado.
 *   3. Por cada cita, hace POST al endpoint Firebase `sendRecordatorio`.
 *   4. Marca la cita como `recordatorio_enviado = 1`.
 *   5. Registra el resumen en `reminder_logs` (clave única por
 *      especialista_id + target_date).
 *
 * Comando cron sugerido (cPanel):
 *   *\/15 * * * * /usr/local/bin/php /home2/aidereservaciones/public_html/chatbot/cron/reminders_cron.php >> /home2/aidereservaciones/logs/reminders_cron.log 2>&1
 */

// Permitir CLI y HTTP (con clave secreta opcional ?key=... si se llama vía web)
$isCli = (php_sapi_name() === 'cli');

// Cargar configuración del sistema (constantes DB, WHATSAPP_REMINDER_URL, etc.)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/reminder_sender.php';

date_default_timezone_set(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'America/Mexico_City');

if (!$isCli) {
    header('Content-Type: text/plain; charset=UTF-8');
    // Protección mínima si se llama vía web
    $providedKey = $_GET['key'] ?? '';
    if ($providedKey !== WHATSAPP_REMINDER_API_KEY) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}

// Log interno a archivo para no depender solo del redireccionamiento del cron.
// En producción intenta /home2/.../logs y si no, usa /public/reminders_cron.log.
$logCandidates = [
    '/home2/aidereservaciones/logs/reminders_cron.log',
    dirname(dirname(ROOT_PATH)) . '/logs/reminders_cron.log',
    ROOT_PATH . '/public/reminders_cron.log',
];

$selectedLog = '';
foreach ($logCandidates as $candidate) {
    $candidateDir = dirname($candidate);
    if (is_dir($candidateDir) && (is_writable($candidateDir) || file_exists($candidate))) {
        $selectedLog = $candidate;
        break;
    }
}

if ($selectedLog === '') {
    // Fallback final (debe existir porque public está dentro del proyecto).
    $selectedLog = __DIR__ . '/../public/reminders_cron.log';
}

// Usar $GLOBALS para que funcione igual en CLI y cuando el script se incluye desde un método.
$GLOBALS['CRON_LOG_FILE'] = $selectedLog;

function logLine($msg) {
    $cronLogFile = $GLOBALS['CRON_LOG_FILE'] ?? '';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    echo $line;
    if ($cronLogFile !== '') {
        @file_put_contents($cronLogFile, $line, FILE_APPEND | LOCK_EX);
    }
}

// Funciones de normalización y envío están en helpers/reminder_sender.php

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    logLine('ERROR: no se pudo conectar a la BD: ' . $e->getMessage());
    exit(1);
}

logLine('=== Inicio de cron de recordatorios ===');
logLine('Log file: ' . ($GLOBALS['CRON_LOG_FILE'] ?? 'N/D'));

// Cargar configuraciones activas
$configs = $db->fetchAll(
    "SELECT rc.especialista_id, rc.hours_before, u.nombre AS especialista_nombre
     FROM reminder_configs rc
     INNER JOIN usuarios u ON u.id = rc.especialista_id AND u.activo = 1
     WHERE rc.enabled = 1"
);

if (empty($configs)) {
    logLine('No hay configuraciones activas. Fin.');
    exit(0);
}

$totalSent = 0;
$totalAttempted = 0;

foreach ($configs as $cfg) {
    $usuarioId = (int)$cfg['especialista_id']; // Este es el usuario_id, no especialista_id de la tabla especialistas
    $hoursBefore   = (int)$cfg['hours_before'];
    $nowLocal = date('Y-m-d H:i:s');

    logLine("Usuario #{$usuarioId} ({$cfg['especialista_nombre']}) hours_before={$hoursBefore}h now={$nowLocal}");

    // Obtener IDs de especialistas (tabla especialistas) asociados a este usuario
    $especialistas = $db->fetchAll(
        "SELECT id FROM especialistas WHERE usuario_id = ? AND activo = 1",
        [$usuarioId]
    );
    
    if (empty($especialistas)) {
        logLine("  -> Sin registros de especialistas activos para este usuario.");
        continue;
    }
    
    $especialistaIds = array_column($especialistas, 'id');
    $placeholders = implode(',', array_fill(0, count($especialistaIds), '?'));
    // Lógica robusta: ya llegó el momento de enviar (cita - hours_before <= ahora)
    // y la cita aún no ha ocurrido. Así no se pierden citas si el cron se retrasa.
    // Se usa la hora local de PHP para evitar desfases con la zona horaria de MySQL.
    $params = array_merge($especialistaIds, [$hoursBefore, $nowLocal, $nowLocal]);

    $reservas = $db->fetchAll(
        "SELECT r.id, r.codigo, r.nombre_cliente, r.telefono,
                r.fecha_cita, r.hora_inicio,
                u.nombre AS nombre_especialista,
                s.nombre AS sucursal_nombre,
                srv.nombre AS servicio_nombre
         FROM reservaciones r
         LEFT JOIN especialistas e ON e.id = r.especialista_id
         LEFT JOIN usuarios u ON u.id = e.usuario_id
         LEFT JOIN sucursales s ON s.id = r.sucursal_id
         LEFT JOIN servicios srv ON srv.id = r.servicio_id
         WHERE r.especialista_id IN ($placeholders)
           AND (r.estado = 'pendiente' OR r.estado = 'confirmada')
           AND r.recordatorio_enviado = 0
           AND r.telefono IS NOT NULL AND r.telefono <> ''
                     AND TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) <= ?
                     AND CONCAT(r.fecha_cita, ' ', r.hora_inicio) > ?",
        $params
    );

    if (empty($reservas)) {
        logLine("  -> 0 citas listas para recordatorio.");
        continue;
    }

    $sentForThisSpecialist = 0;
    $errorAcc = '';
    $targetDate = date('Y-m-d');

    foreach ($reservas as $r) {
        $totalAttempted++;
        $res = reminderSendWhatsapp($r);

        if ($res['success']) {
            $db->query(
                "UPDATE reservaciones SET recordatorio_enviado = 1 WHERE id = ?",
                [$r['id']]
            );
            $sentForThisSpecialist++;
            $totalSent++;
            logLine("  OK Cita #{$r['id']} -> {$res['to']} (" . date('d/m/Y H:i', strtotime($r['fecha_cita'].' '.$r['hora_inicio'])) . ")");
        } else {
            $errLine = "Cita #{$r['id']} {$res['message']} body=" . substr($res['raw'], 0, 200);
            $errorAcc .= $errLine . "\n";
            logLine("  ERR {$errLine}");
        }
    }

    // Registrar resumen del lote
    $db->query(
        "INSERT INTO reminder_logs (especialista_id, target_date, sent_count, total_count, error_message)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            sent_count  = sent_count + VALUES(sent_count),
            total_count = total_count + VALUES(total_count),
            error_message = VALUES(error_message),
            sent_at = CURRENT_TIMESTAMP",
        [$usuarioId, $targetDate, $sentForThisSpecialist, count($reservas), $errorAcc ?: null]
    );
}

logLine("=== Fin. Enviados: {$totalSent} / Intentados: {$totalAttempted} ===");
exit(0);
