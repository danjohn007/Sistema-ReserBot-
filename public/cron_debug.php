<?php
/**
 * Diagnóstico del sistema de recordatorios - ReserBot
 * SOLO PARA DEBUG — eliminar este archivo una vez resuelto el problema
 * Acceder en: https://aidereservaciones.com/chatbot/public/cron_debug.php?key=debug2026
 */

if (($_GET['key'] ?? '') !== 'debug2026') {
    http_response_code(403);
    exit('Acceso denegado');
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO CRON RECORDATORIOS - ReserBot ===\n";
echo "Fecha/hora servidor: " . date('Y-m-d H:i:s') . "\n";
echo "PHP versión: " . PHP_VERSION . "\n\n";

$nowLocal = date('Y-m-d H:i:s');

// ─── Cargar bootstrap ─────────────────────────────────────────────────────────

$rootPath = dirname(__DIR__);
echo "Root path: {$rootPath}\n";
echo "config.php existe:        " . (file_exists($rootPath . '/config/config.php')        ? 'SÍ' : 'NO') . "\n";
echo "database.php existe:      " . (file_exists($rootPath . '/config/database.php')      ? 'SÍ' : 'NO') . "\n";
echo "reminder_sender.php existe: " . (file_exists($rootPath . '/helpers/reminder_sender.php') ? 'SÍ' : 'NO') . "\n";
echo "reminders_cron.php existe: " . (file_exists($rootPath . '/cron/reminders_cron.php')  ? 'SÍ' : 'NO') . "\n\n";

require_once $rootPath . '/config/config.php';
require_once $rootPath . '/config/database.php';
require_once $rootPath . '/helpers/reminder_sender.php';

// ─── Conexión BD ──────────────────────────────────────────────────────────────

echo "--- CONEXIÓN BD ---\n";
try {
    $db = Database::getInstance();
    $db->fetch("SELECT 1");
    echo "Conexión OK\n\n";
} catch (Exception $e) {
    echo "ERROR conexión: " . $e->getMessage() . "\n";
    exit;
}

echo "--- RELOJES (PHP vs MySQL) ---\n";
try {
    $mysqlNow = $db->fetch("SELECT NOW() AS now_db");
    echo "PHP now:   {$nowLocal}\n";
    echo "MySQL NOW: " . ($mysqlNow['now_db'] ?? 'N/D') . "\n\n";
} catch (Exception $e) {
    echo "ERROR reloj BD: " . $e->getMessage() . "\n\n";
}

// ─── Constantes WhatsApp ──────────────────────────────────────────────────────

echo "--- CONSTANTES WHATSAPP ---\n";
echo "WHATSAPP_REMINDER_URL:      " . (defined('WHATSAPP_REMINDER_URL') ? WHATSAPP_REMINDER_URL : 'NO DEFINIDA') . "\n";
echo "WHATSAPP_REMINDER_API_KEY:  " . (defined('WHATSAPP_REMINDER_API_KEY') ? WHATSAPP_REMINDER_API_KEY : 'NO DEFINIDA') . "\n";
echo "WHATSAPP_REMINDER_TEMPLATE: " . (defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : 'NO DEFINIDA') . "\n\n";

// ─── Verificar tablas ─────────────────────────────────────────────────────────

echo "--- TABLAS REMINDER ---\n";
try {
    $pdo    = $db->getConnection();
    $tables = $pdo->query("SHOW TABLES LIKE 'reminder%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tablas encontradas: " . (empty($tables) ? 'NINGUNA (¿ejecutaste la migración SQL?)' : implode(', ', $tables)) . "\n\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

// ─── Contenido reminder_configs ───────────────────────────────────────────────

echo "--- REMINDER_CONFIGS ---\n";
try {
    $configs = $db->fetchAll(
        "SELECT rc.*, u.nombre AS especialista_nombre
         FROM reminder_configs rc
         LEFT JOIN usuarios u ON u.id = rc.especialista_id"
    );
    if (empty($configs)) {
        echo "Sin registros (¿ningún especialista ha configurado recordatorios?)\n";
    } else {
        foreach ($configs as $c) {
            echo "  especialista_id={$c['especialista_id']} ({$c['especialista_nombre']}) enabled={$c['enabled']} hours_before={$c['hours_before']}\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// ─── Query principal del cron ─────────────────────────────────────────────────

echo "--- CONFIGS ACTIVAS (query principal cron) ---\n";
try {
    $activeConfigs = $db->fetchAll(
        "SELECT rc.especialista_id, rc.hours_before, u.nombre AS especialista_nombre
         FROM reminder_configs rc
         INNER JOIN usuarios u ON u.id = rc.especialista_id AND u.activo = 1
         WHERE rc.enabled = 1"
    );

    if (empty($activeConfigs)) {
        echo "Sin resultados. Posibles causas:\n";
        echo "  - enabled = 0 (nadie activó recordatorios)\n";
        echo "  - usuario inactivo (activo = 0 en tabla usuarios)\n\n";

        // Diagnóstico sin filtros
        $all = $db->fetchAll(
            "SELECT rc.especialista_id, rc.enabled, u.nombre, u.activo
             FROM reminder_configs rc
             LEFT JOIN usuarios u ON u.id = rc.especialista_id"
        );
        if (!empty($all)) {
            echo "Estado sin filtro:\n";
            foreach ($all as $row) {
                echo "  especialista_id={$row['especialista_id']} nombre={$row['nombre']} enabled={$row['enabled']} activo={$row['activo']}\n";
            }
        }
    } else {
        foreach ($activeConfigs as $cfg) {
            $usuarioId   = (int)$cfg['especialista_id'];
            $hoursBefore = (int)$cfg['hours_before'];

            echo "  [{$usuarioId}] {$cfg['especialista_nombre']} — hours_before={$hoursBefore} now={$nowLocal}\n";

            // Obtener especialista_ids de la tabla especialistas
            $especialistas = $db->fetchAll(
                "SELECT id FROM especialistas WHERE usuario_id = ? AND activo = 1",
                [$usuarioId]
            );
            $espIds = array_column($especialistas, 'id');
            echo "    Registros en tabla especialistas: " . (empty($espIds) ? 'NINGUNO (problema!)' : implode(', ', $espIds)) . "\n";

            if (empty($espIds)) continue;

            $placeholders = implode(',', array_fill(0, count($espIds), '?'));
            $params       = array_merge($espIds, [$hoursBefore]);

            // Citas listas para recordatorio (misma lógica que el cron corregido)
            $pendientes = $db->fetchAll(
                "SELECT r.id, r.nombre_cliente, r.telefono, r.fecha_cita, r.hora_inicio,
                        r.estado, r.recordatorio_enviado,
                        TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) AS send_at
                 FROM reservaciones r
                 WHERE r.especialista_id IN ($placeholders)
                   AND (r.estado = 'pendiente' OR r.estado = 'confirmada')
                   AND r.recordatorio_enviado = 0
                   AND r.telefono IS NOT NULL AND r.telefono <> ''
                                     AND TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) <= ?
                                     AND CONCAT(r.fecha_cita, ' ', r.hora_inicio) > ?
                 ORDER BY r.fecha_cita, r.hora_inicio",
                                array_merge($espIds, [$hoursBefore], [$hoursBefore, $nowLocal, $nowLocal])
            );

            echo "    Citas LISTAS para enviar ahora: " . count($pendientes) . "\n";
            foreach ($pendientes as $p) {
                echo "      #{$p['id']} {$p['nombre_cliente']} tel={$p['telefono']} cita={$p['fecha_cita']} {$p['hora_inicio']} send_at={$p['send_at']}\n";
            }

            // Citas futuras pendientes (aún no es hora de enviar)
            $futuras = $db->fetchAll(
                "SELECT r.id, r.nombre_cliente, r.telefono, r.fecha_cita, r.hora_inicio,
                        TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) AS send_at
                 FROM reservaciones r
                 WHERE r.especialista_id IN ($placeholders)
                   AND (r.estado = 'pendiente' OR r.estado = 'confirmada')
                   AND r.recordatorio_enviado = 0
                   AND r.telefono IS NOT NULL AND r.telefono <> ''
                                     AND TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) > ?
                 ORDER BY r.fecha_cita, r.hora_inicio
                 LIMIT 10",
                                array_merge($espIds, [$hoursBefore], [$hoursBefore, $nowLocal])
            );

            echo "    Próximas citas (aún no es hora de enviar, máx 10):\n";
            if (empty($futuras)) {
                echo "      Sin citas futuras pendientes.\n";
            }
            foreach ($futuras as $f) {
                echo "      #{$f['id']} {$f['nombre_cliente']} cita={$f['fecha_cita']} {$f['hora_inicio']} → recordatorio a las {$f['send_at']}\n";
            }

            // Últimos logs
            $lastLog = $db->fetch(
                "SELECT * FROM reminder_logs WHERE especialista_id = ? ORDER BY sent_at DESC LIMIT 1",
                [$usuarioId]
            );
            echo "    Último log: " . ($lastLog ? "sent_at={$lastLog['sent_at']} sent={$lastLog['sent_count']}/{$lastLog['total_count']}" : "Sin registros") . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// ─── Versión del cron en servidor ─────────────────────────────────────────────

echo "--- VERSIÓN CRON EN SERVIDOR ---\n";
$cronFile    = $rootPath . '/cron/reminders_cron.php';
$cronContent = file_get_contents($cronFile);
if (strpos($cronContent, 'TIMESTAMPADD') !== false) {
    echo "VERSIÓN CORRECTA (usa TIMESTAMPADD — lógica robusta)\n\n";
} elseif (strpos($cronContent, 'windowStart') !== false || strpos($cronContent, 'windowEnd') !== false) {
    echo "VERSIÓN VIEJA (usa ventana fija de 15 min — puede perder citas)\n\n";
} else {
    echo "No se pudo determinar la versión\n\n";
}

// ─── Ejecutar cron completo si se pasa &send=1 ───────────────────────────────

if (isset($_GET['send']) && $_GET['send'] === '1') {
    echo "--- EJECUTAR CRON AHORA ---\n";

    $activeConfigs = $db->fetchAll(
        "SELECT rc.especialista_id, rc.hours_before, u.nombre AS especialista_nombre
         FROM reminder_configs rc
         INNER JOIN usuarios u ON u.id = rc.especialista_id AND u.activo = 1
         WHERE rc.enabled = 1"
    );

    foreach ($activeConfigs as $cfg) {
        $usuarioId   = (int)$cfg['especialista_id'];
        $hoursBefore = (int)$cfg['hours_before'];

        $especialistas = $db->fetchAll(
            "SELECT id FROM especialistas WHERE usuario_id = ? AND activo = 1",
            [$usuarioId]
        );
        $espIds = array_column($especialistas, 'id');

        if (empty($espIds)) {
            echo "  [{$usuarioId}] {$cfg['especialista_nombre']} — Sin registros en tabla especialistas, saltando.\n";
            continue;
        }

        $placeholders = implode(',', array_fill(0, count($espIds), '?'));
        $params       = array_merge($espIds, [$hoursBefore, $hoursBefore, $nowLocal, $nowLocal]);

        $pendientes = $db->fetchAll(
            "SELECT r.id, r.nombre_cliente, r.telefono,
                    r.fecha_cita, r.hora_inicio,
                    u2.nombre AS nombre_especialista,
                    s.nombre  AS sucursal_nombre,
                    srv.nombre AS servicio_nombre
             FROM reservaciones r
             LEFT JOIN especialistas e  ON e.id  = r.especialista_id
             LEFT JOIN usuarios u2      ON u2.id = e.usuario_id
             LEFT JOIN sucursales s     ON s.id  = r.sucursal_id
             LEFT JOIN servicios srv    ON srv.id = r.servicio_id
             WHERE r.especialista_id IN ($placeholders)
               AND (r.estado = 'pendiente' OR r.estado = 'confirmada')
               AND r.recordatorio_enviado = 0
               AND r.telefono IS NOT NULL AND r.telefono <> ''
                             AND TIMESTAMPADD(HOUR, -?, CONCAT(r.fecha_cita, ' ', r.hora_inicio)) <= ?
                             AND CONCAT(r.fecha_cita, ' ', r.hora_inicio) > ?
             ORDER BY r.fecha_cita, r.hora_inicio",
            $params
        );

        if (empty($pendientes)) {
            echo "  [{$usuarioId}] {$cfg['especialista_nombre']} — Sin citas listas para enviar.\n";
            continue;
        }

        echo "  [{$usuarioId}] {$cfg['especialista_nombre']} — " . count($pendientes) . " cita(s) listas:\n";

        $sent  = 0;
        $errAcc = '';

        foreach ($pendientes as $r) {
            // Normalizar teléfono
            $phoneNorm = reminderNormalizePhone($r['telefono']);
            if ($phoneNorm && $phoneNorm !== $r['telefono']) {
                $db->query("UPDATE reservaciones SET telefono = ? WHERE id = ?", [$phoneNorm, $r['id']]);
                echo "    Tel normalizado: {$r['telefono']} → {$phoneNorm}\n";
                $r['telefono'] = $phoneNorm;
            }

            $res = reminderSendWhatsapp($r);

            if ($res['success']) {
                $db->query("UPDATE reservaciones SET recordatorio_enviado = 1 WHERE id = ?", [$r['id']]);
                echo "    ✓ #{$r['id']} {$r['nombre_cliente']} ({$res['to']}) — {$r['fecha_cita']} {$r['hora_inicio']}\n";
                $sent++;
            } else {
                $errLine = "#{$r['id']} {$r['nombre_cliente']} ({$r['telefono']}) — ERROR: {$res['message']}";
                $errAcc .= $errLine . "\n";
                echo "    ✗ {$errLine}\n";
                echo "      Raw: " . substr($res['raw'], 0, 300) . "\n";
            }
        }

        // Registrar en reminder_logs
        $db->query(
            "INSERT INTO reminder_logs (especialista_id, target_date, sent_count, total_count, error_message)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                sent_count    = sent_count + VALUES(sent_count),
                total_count   = total_count + VALUES(total_count),
                error_message = VALUES(error_message),
                sent_at       = CURRENT_TIMESTAMP",
            [$usuarioId, date('Y-m-d'), $sent, count($pendientes), $errAcc ?: null]
        );

        echo "  [{$usuarioId}] Total enviados: {$sent}/" . count($pendientes) . "\n";
    }
    echo "\n";
} else {
    echo "--- EJECUTAR CRON ---\n";
    echo "Agrega &send=1 a la URL para ejecutar el cron ahora mismo.\n\n";
}

// ─── Test de escritura en log ─────────────────────────────────────────────────

echo "--- TEST ESCRITURA LOG ---\n";
$logPath  = '/home2/aidereservaciones/logs/reminders_cron.log';
$testLine = date('Y-m-d H:i:s') . " - TEST desde cron_debug.php\n";
$result   = file_put_contents($logPath, $testLine, FILE_APPEND | LOCK_EX);
echo "Escribir en {$logPath}: " . ($result !== false ? "OK ({$result} bytes)" : "FALLÓ — revisar permisos") . "\n\n";

echo "--- ESTADO ARCHIVO LOG ---\n";
echo "Existe archivo: " . (file_exists($logPath) ? 'SÍ' : 'NO') . "\n";
echo "Existe carpeta logs: " . (is_dir(dirname($logPath)) ? 'SÍ' : 'NO') . "\n";
echo "Carpeta writable: " . (is_writable(dirname($logPath)) ? 'SÍ' : 'NO') . "\n\n";

echo "--- COMANDO CRON RECOMENDADO ---\n";
echo "*/15 * * * * /usr/local/bin/php /home2/aidereservaciones/public_html/chatbot/cron/reminders_cron.php >> /home2/aidereservaciones/logs/reminders_cron.log 2>&1\n\n";

// ─── Recordatorios ya enviados hoy ───────────────────────────────────────────

echo "--- REMINDER_LOGS (últimos 10) ---\n";
try {
    $logs = $db->fetchAll(
        "SELECT rl.*, u.nombre AS especialista_nombre
         FROM reminder_logs rl
         LEFT JOIN usuarios u ON u.id = rl.especialista_id
         ORDER BY rl.sent_at DESC
         LIMIT 10"
    );
    if (empty($logs)) {
        echo "Sin registros\n";
    } else {
        foreach ($logs as $l) {
            echo "  {$l['sent_at']} [{$l['especialista_nombre']}] {$l['sent_count']}/{$l['total_count']} enviados";
            if ($l['error_message']) echo " ERRORES: " . substr($l['error_message'], 0, 100);
            echo "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== FIN DIAGNÓSTICO ===\n";
echo "Para ejecutar el cron ahora: añade &send=1 a la URL\n";
