# Recordatorios automáticos por WhatsApp

Esta funcionalidad envía recordatorios automáticos por WhatsApp a los pacientes antes de su cita, usando un endpoint de Firebase Cloud Functions (`sendRecordatorio`) y la plantilla aprobada `recordatorio_cita` de Meta.

## 1. Base de datos

Ejecuta la migración:

```sh
mysql -u aiderese_reserbot -p aiderese_reserbot < sql/create_reminder_tables.sql
```

Esto crea:
- `reminder_configs` — toggle + horas antes, **por especialista** (UNIQUE `especialista_id`).
- `reminder_logs` — historial diario por especialista (UNIQUE `especialista_id` + `target_date`).

La columna `reservaciones.recordatorio_enviado` ya existe y se reutiliza como bandera por cita.

## 2. Configuración

Edita `config/config.php` y reemplaza el placeholder de la URL:

```php
define('WHATSAPP_REMINDER_URL', 'https://us-central1-TU-PROYECTO.cloudfunctions.net/sendRecordatorio');
define('WHATSAPP_REMINDER_API_KEY', '781830135017382'); // debe coincidir con functions_Chatbot/config.js
define('WHATSAPP_REMINDER_TEMPLATE', 'recordatorio_cita');
```

> Obtén la URL real desde Firebase Console → Functions → `sendRecordatorio` → *Trigger URL*.

## 3. Interfaz

Disponible en **Configuraciones → Recordatorios WhatsApp** (`/configuraciones/recordatorios`).

- SUPERADMIN / BRANCH_ADMIN: puede elegir cualquier especialista.
- ESPECIALISTA: solo ve y configura el suyo.

Controles:
- Toggle **activar/desactivar**.
- Slider **horas antes** (1–24). Se deshabilita automáticamente cuando el toggle está apagado.
- Botón **Guardar configuración** (AJAX).
- Panel **¿Cómo funciona?** y tabla de **historial reciente** (últimos 10 lotes).

## 4. Cron job

Configura un cron en cPanel cada 15 minutos:

```
*/15 * * * * /usr/local/bin/php /home/aiderese/public_html/chatbot/cron/reminders_cron.php >> /home/aiderese/logs/reminders_cron.log 2>&1
```

Ajusta las rutas si tu instalación está en otra carpeta.

### Disparo manual (para pruebas)

Por CLI:
```sh
php cron/reminders_cron.php
```

Por web (protegido por la API key):
```
https://aidereservaciones.com/chatbot/cron/reminders_cron.php?key=781830135017382
```

## 5. Lógica del envío

Cada ejecución:
1. Lee todas las `reminder_configs` con `enabled = 1`.
2. Para cada especialista calcula la ventana `NOW() + hours_before … +15 min`.
3. Selecciona reservaciones de ese especialista con:
   - `estado IN ('pendiente','confirmada')`
   - `recordatorio_enviado = 0`
   - `telefono` no vacío
4. Normaliza el teléfono a formato `521XXXXXXXXXX` (MX).
5. POST a `WHATSAPP_REMINDER_URL` con header `x-api-key` y body:
   ```json
   {
     "to": "521XXXXXXXXXX",
     "nombre_usuario": "Juan Pérez",
     "nombre_especialista": "Dra. Ana López",
     "fecha": "30/01/2026",
     "hora": "11:00",
     "ubicacion": "Sucursal Centro",
     "template": "recordatorio_cita"
   }
   ```
6. Si responde 2xx, marca `reservaciones.recordatorio_enviado = 1`.
7. Registra el resumen en `reminder_logs`.

## 6. Pendientes que debes confirmar

- [ ] **URL pública del endpoint `sendRecordatorio`** en Firebase (actualmente hay un placeholder en `config.php`).
- [ ] Ruta absoluta real del proyecto en cPanel para el cron.
