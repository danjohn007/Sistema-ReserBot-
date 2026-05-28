<?php
/**
 * Helper para envío de recordatorios por WhatsApp via Firebase Function.
 * Usado tanto por el cron (cron/reminders_cron.php) como por el controlador
 * de configuración (botón "Enviar prueba").
 */

if (!function_exists('reminderNormalizePhone')) {
    function reminderNormalizePhone($phone) {
        $digits = preg_replace('/\D+/', '', (string)$phone);
        if ($digits === '') return null;
        if (strlen($digits) === 10) return '521' . $digits;
        if (strlen($digits) === 12 && substr($digits, 0, 2) === '52') return '521' . substr($digits, 2);
        if (strlen($digits) === 13 && substr($digits, 0, 3) === '521') return $digits;
        return $digits;
    }
}

if (!function_exists('reminderSendWhatsapp')) {
    /**
     * Envía un recordatorio para una reservación.
     *
     * @param array $reservation Debe contener: nombre_cliente, telefono,
     *   fecha_cita, hora_inicio, nombre_especialista, sucursal_nombre.
     * @return array { success: bool, message: string, http_code: int, raw: string }
     */
    function reminderSendWhatsapp(array $reservation): array {
        $to = reminderNormalizePhone($reservation['telefono'] ?? '');
        if (!$to) {
            return ['success' => false, 'message' => 'Teléfono inválido o vacío', 'http_code' => 0, 'raw' => ''];
        }

        $payload = [
            'to'                  => $to,
            'nombre_usuario'      => $reservation['nombre_cliente'] ?: 'Paciente',
            'nombre_especialista' => $reservation['nombre_especialista'] ?: 'Especialista',
            'fecha'               => date('d/m/Y', strtotime($reservation['fecha_cita'])),
            'hora'                => date('H:i', strtotime($reservation['hora_inicio'])),
            'ubicacion'           => $reservation['sucursal_nombre'] ?: 'Consultorio',
            'template'            => defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : 'recordatorio_reserva_cita',
        ];

        // Log para debug (puedes comentar después)
        error_log('Payload recordatorio: ' . json_encode($payload));

        $ch = curl_init(WHATSAPP_REMINDER_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . WHATSAPP_REMINDER_API_KEY,
            ],
            CURLOPT_TIMEOUT        => 20,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        // Log detallado en archivo específico accesible
        $logFile = __DIR__ . '/../public/reminder_debug.log';
        $logEntry = "\n=== " . date('Y-m-d H:i:s') . " ===\n";
        $logEntry .= "URL: " . WHATSAPP_REMINDER_URL . "\n";
        $logEntry .= "Payload enviado: " . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        $logEntry .= "HTTP Code: " . $code . "\n";
        $logEntry .= "Response body: " . $body . "\n";
        $logEntry .= "cURL error: " . ($err ?: 'ninguno') . "\n";
        $logEntry .= "====================\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        $ok = ($code >= 200 && $code < 300);
        return [
            'success'   => $ok,
            'message'   => $ok ? "Enviado a {$to}" : "Error HTTP {$code}" . ($err ? " ({$err})" : ''),
            'http_code' => $code,
            'raw'       => (string)$body,
            'to'        => $to,
        ];
    }
}
