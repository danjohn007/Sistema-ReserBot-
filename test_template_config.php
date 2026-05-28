<?php
/**
 * Script de prueba temporal para verificar configuración de template
 */

require_once __DIR__ . '/config/config.php';

echo "=== VERIFICACIÓN DE CONFIGURACIÓN DE TEMPLATE ===\n\n";

echo "1. ¿Está definida WHATSAPP_REMINDER_TEMPLATE? ";
echo defined('WHATSAPP_REMINDER_TEMPLATE') ? "SÍ\n" : "NO\n";

echo "2. Valor de WHATSAPP_REMINDER_TEMPLATE: ";
echo defined('WHATSAPP_REMINDER_TEMPLATE') ? WHATSAPP_REMINDER_TEMPLATE : "NO DEFINIDA";
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
