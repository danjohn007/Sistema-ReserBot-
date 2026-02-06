# Implementación de Horarios de Emergencia

## Resumen de Cambios

Se ha implementado la funcionalidad para que los especialistas puedan configurar **horarios de emergencia** por día de la semana. Estos horarios permiten agregar disponibilidad FUERA del horario normal para atender casos urgentes.

---

## 1. Diferencia con Horarios de Bloqueo

| Característica | Horario de Bloqueo | Horario de Emergencia |
|----------------|-------------------|----------------------|
| **Propósito** | Quitar horas DENTRO del horario normal | Agregar horas FUERA del horario normal |
| **Ejemplo** | Almuerzo: 1pm-2pm (dentro de 9am-6pm) | Emergencias: 8pm-10pm (después de 6pm) |
| **Validación** | Debe estar dentro del horario normal | NO puede estar dentro del horario normal ni bloqueo |
| **En calendario** | Bloquea disponibilidad | Agrega disponibilidad |

---

## 2. Cambios en la Base de Datos

### Archivo: `sql/add_horarios_emergencia.sql`

Se agregaron 3 columnas a la tabla `horarios_especialistas`:

```sql
ALTER TABLE `horarios_especialistas`
ADD COLUMN `hora_inicio_emergencia` TIME NULL 
    COMMENT 'Hora de inicio del horario de emergencia (fuera del horario normal)',
ADD COLUMN `hora_fin_emergencia` TIME NULL 
    COMMENT 'Hora de fin del horario de emergencia (fuera del horario normal)',
ADD COLUMN `emergencia_activa` TINYINT(1) DEFAULT 0 
    COMMENT '1 si el horario de emergencia está activo para este día';
```

### Instrucciones para aplicar:

1. Accede a **phpMyAdmin**
2. Selecciona la base de datos
3. Ve a la pestaña **SQL**
4. Copia y ejecuta el contenido del archivo `sql/add_horarios_emergencia.sql`

---

## 3. Cambios en la Vista (Frontend)

### Archivo: `app/views/specialists/schedules.php`

#### Nuevas columnas agregadas:

1. **Columna "Emergencia"**: Checkbox para activar/desactivar horario de emergencia
2. **Columna "Horario Emergencia"**: Campos de hora inicio y fin (solo visible cuando está activo)

#### Características visuales:

- Fondo verde claro para distinguir de otros tipos de horario
- Placeholder "Sin emergencia" cuando no está activo
- JavaScript `toggleEmergency()` para mostrar/ocultar campos

#### Ejemplo visual:

```
┌────────────────────────────────────────────────────────────┐
│ Lunes                                                      │
│ ☑ Activo                                                   │
│ Hora Inicio: [09:00]  Hora Fin: [18:00]                  │
│                                                            │
│ ☑ Bloquear  →  [13:00] a [14:00]                         │
│                                                            │
│ ☑ Emergencia  →  🚨 [19:00] a [21:00] (Emergencia)       │
└────────────────────────────────────────────────────────────┘
```

---

## 4. Cambios en el Controlador (Backend)

### Archivo: `app/controllers/SpecialistController.php`

#### Método: `schedules()`

Se agregó la lógica para procesar horarios de emergencia con las siguientes **validaciones**:

### Validaciones Implementadas:

#### 1. **Hora inicio < Hora fin**
```php
if (strtotime($hora_inicio_emergencia) >= strtotime($hora_fin_emergencia)) {
    error: 'La hora de inicio de emergencia debe ser menor que la hora de fin.'
}
```

#### 2. **No puede estar dentro del horario normal**
```php
// Detecta cualquier tipo de traslape con el horario normal
if (hay_traslape_con_horario_normal) {
    error: 'El horario de emergencia no puede estar dentro del horario laboral normal.'
}
```

**Ejemplos:**
- ❌ Normal: 9am-6pm, Emergencia: 2pm-4pm (DENTRO)
- ✅ Normal: 9am-6pm, Emergencia: 7pm-9pm (FUERA)

#### 3. **No puede estar dentro del horario de bloqueo**
```php
// Si hay bloqueo activo, validar que no haya traslape
if (bloqueo_activo && hay_traslape_con_bloqueo) {
    error: 'El horario de emergencia no puede estar dentro del horario de bloqueo.'
}
```

#### Campos procesados:
```php
$emergencia_activa = $this->post('emergencia_activa_' . $day) ? 1 : 0;
$hora_inicio_emergencia = $this->post('hora_inicio_emergencia_' . $day);
$hora_fin_emergencia = $this->post('hora_fin_emergencia_' . $day);
```

#### Query de inserción actualizado:
```sql
INSERT INTO horarios_especialistas 
(especialista_id, dia_semana, hora_inicio, hora_fin, activo, 
 hora_inicio_bloqueo, hora_fin_bloqueo, bloqueo_activo,
 hora_inicio_emergencia, hora_fin_emergencia, emergencia_activa) 
VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)
```

---

## 5. Cambios en el API de Disponibilidad

### Archivo: `app/controllers/ApiController.php`

#### Método: `availability()`

Se actualizó completamente el algoritmo para generar slots disponibles:

### Lógica de Generación de Slots:

#### **Paso 1: Generar slots del horario normal**

Si hay bloqueo activo:
```
Rango 1: [hora_inicio → hora_inicio_bloqueo]
Rango 2: [hora_fin_bloqueo → hora_fin]
```

Si NO hay bloqueo:
```
Rango único: [hora_inicio → hora_fin]
```

#### **Paso 2: Agregar slots del horario de emergencia**

Si `emergencia_activa == 1`:
```
Rango emergencia: [hora_inicio_emergencia → hora_fin_emergencia]
```

Los slots de emergencia se marcan con:
- `tipo: 'emergencia'`
- Emoji 🚨 en el display
- Texto "(Emergencia)" al final

#### **Paso 3: Ordenar todos los slots por hora**

Los slots se ordenan cronológicamente, mezclando normales y de emergencia.

### Estructura de respuesta JSON:

```json
{
  "slots": [
    {
      "hora_inicio": "09:00:00",
      "hora_fin": "09:30:00",
      "display": "9:00 AM - 9:30 AM",
      "tipo": "normal"
    },
    {
      "hora_inicio": "19:00:00",
      "hora_fin": "19:30:00",
      "display": "🚨 7:00 PM - 7:30 PM (Emergencia)",
      "tipo": "emergencia"
    }
  ]
}
```

---

## 6. Casos de Uso

### Caso 1: Médico con horarios extendidos

**Configuración:**
- Horario normal: 9:00 AM - 5:00 PM
- Bloqueo (comida): 1:00 PM - 2:00 PM
- Emergencia: 6:00 PM - 9:00 PM

**Resultado:**
```
Slots disponibles:
- 9:00 - 9:30 (Normal)
- 9:30 - 10:00 (Normal)
... 
- 12:30 - 1:00 (Normal)
[BLOQUEADO 1:00 - 2:00]
- 2:00 - 2:30 (Normal)
...
- 4:30 - 5:00 (Normal)
[FIN HORARIO NORMAL]
- 🚨 6:00 - 6:30 (Emergencia)
- 🚨 6:30 - 7:00 (Emergencia)
...
- 🚨 8:30 - 9:00 (Emergencia)
```

### Caso 2: Especialista con madrugada disponible

**Configuración:**
- Horario normal: 9:00 AM - 3:00 PM
- Sin bloqueo
- Emergencia: 6:00 AM - 8:00 AM

**Resultado:**
```
- 🚨 6:00 - 6:30 (Emergencia - Madrugada)
- 🚨 6:30 - 7:00 (Emergencia - Madrugada)
...
- 9:00 - 9:30 (Normal)
...
- 2:30 - 3:00 (Normal)
```

---

## 7. Validaciones en UI

El formulario realiza validaciones en tiempo real:

1. **Checkbox de Emergencia**: Al activarse, muestra campos de hora
2. **JavaScript**: `toggleEmergency(dayNum)` controla visibilidad
3. **Backend**: Valida todos los rangos antes de guardar

---

## 8. Ejemplos de Validación

### ✅ **VÁLIDOS:**

| Horario Normal | Bloqueo | Emergencia | ¿Por qué? |
|----------------|---------|------------|-----------|
| 9am - 6pm | - | 7pm - 9pm | Emergencia DESPUÉS del horario |
| 9am - 6pm | 1pm-2pm | 7pm - 9pm | Emergencia fuera de ambos |
| 10am - 5pm | - | 7am - 9am | Emergencia ANTES del horario |

### ❌ **INVÁLIDOS:**

| Horario Normal | Bloqueo | Emergencia | Error |
|----------------|---------|------------|-------|
| 9am - 6pm | - | 2pm - 4pm | Emergencia DENTRO del horario normal |
| 9am - 6pm | 1pm-2pm | 1:30pm - 3pm | Emergencia traslapa con bloqueo |
| 9am - 6pm | - | 5pm - 7pm | Emergencia traslapa con horario normal |
| 9am - 6pm | 1pm-2pm | 12pm - 3pm | Emergencia traslapa con ambos |

---

## 9. Integración con el Sistema Existente

### Calendario (`CalendarController`)
Los horarios de emergencia se muestran como slots disponibles en el calendario con indicador visual 🚨.

### Reservaciones (`ReservationController`)
Las citas pueden crearse en horarios de emergencia sin restricciones adicionales.

### Dashboard
Las estadísticas incluyen citas de horarios normales y de emergencia sin distinción.

---

## 10. Recomendaciones de Uso

### Para Especialistas:

1. **Configurar emergencias solo cuando sea necesario**
   - Evitar tener emergencias todos los días
   - Usarlo para casos realmente urgentes

2. **Validar que el horario tenga sentido**
   - Ejemplo válido: Normal 9am-5pm, Emergencia 6pm-8pm
   - Ejemplo inválido: Normal 9am-5pm, Emergencia 3pm-7pm

3. **Considerar tiempo de descanso**
   - Si terminas a las 6pm, no ofrecer emergencias inmediatamente
   - Dar al menos 1 hora de buffer

### Para Administradores:

1. **Revisar configuraciones de especialistas**
2. **Validar que las emergencias sean coherentes**
3. **Monitorear uso de horarios de emergencia**

---

## 11. FAQ

**P: ¿El horario de emergencia puede ser ANTES del horario normal?**  
R: Sí, puede ser antes (ej: 7am-9am cuando el normal es 10am-6pm).

**P: ¿Puedo tener múltiples rangos de emergencia?**  
R: No, solo un rango por día. Si necesitas más, considera extender el horario normal.

**P: ¿Los clientes ven diferencia entre slots normales y de emergencia?**  
R: Sí, los slots de emergencia tienen el emoji 🚨 y el texto "(Emergencia)".

**P: ¿El precio cambia en horarios de emergencia?**  
R: No, el precio es el mismo. Esta funcionalidad solo afecta disponibilidad.

**P: ¿Qué pasa si configuro mal los horarios?**  
R: El sistema te mostrará un error específico indicando qué está mal.

---

## 12. Archivos Modificados

```
sql/add_horarios_emergencia.sql           [NUEVO]
app/views/specialists/schedules.php       [MODIFICADO]
app/controllers/SpecialistController.php  [MODIFICADO]
app/controllers/ApiController.php         [MODIFICADO]
```

---

## 13. Pruebas Recomendadas

1. ✅ Crear horario de emergencia ANTES del normal
2. ✅ Crear horario de emergencia DESPUÉS del normal
3. ✅ Intentar crear emergencia DENTRO del normal (debe fallar)
4. ✅ Intentar crear emergencia en horario de bloqueo (debe fallar)
5. ✅ Verificar que los slots se muestren correctamente en la UI
6. ✅ Crear una reserva en horario de emergencia
7. ✅ Verificar que aparezca en el calendario

---

## 14. Soporte

Para preguntas o problemas:
1. Revisar este documento
2. Verificar logs en `logs_seguridad`
3. Revisar mensajes de error en pantalla

---

**Fecha de implementación:** 6 de Febrero de 2026  
**Versión:** 1.1.0
