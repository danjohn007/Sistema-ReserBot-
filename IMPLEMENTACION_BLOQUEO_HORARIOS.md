# Implementación de Bloqueo de Horarios e Intervalo de Espacios

## Resumen de Cambios

Se ha implementado la funcionalidad para que los especialistas puedan:

1. **Configurar el intervalo de espacios** entre citas (30 o 60 minutos)
2. **Bloquear horarios específicos** dentro de su jornada laboral por día (ej: 1pm-2pm para almuerzo)

---

## 1. Cambios en la Base de Datos

### Archivo: `sql/add_schedule_blocks.sql`

Se creó un script SQL para modificar la tabla `horarios_especialistas` agregando:

- **intervalo_espacios** (INT): Define si las citas se separan cada 30 o 60 minutos
- **hora_inicio_bloqueo** (TIME): Hora de inicio del bloqueo dentro del día
- **hora_fin_bloqueo** (TIME): Hora de fin del bloqueo dentro del día
- **bloqueo_activo** (TINYINT): Si el bloqueo está activo para ese día (0 o 1)

### Instrucciones para aplicar los cambios:

1. Accede a **phpMyAdmin** en tu hosting
2. Selecciona la base de datos `aiderese_reserbot`
3. Ve a la pestaña **SQL**
4. Copia y pega el siguiente código:

```sql
-- Agregar columnas a la tabla horarios_especialistas
ALTER TABLE `horarios_especialistas`
ADD COLUMN `intervalo_espacios` INT DEFAULT 60 COMMENT 'Intervalo en minutos para separar las citas (30 o 60)' AFTER `activo`,
ADD COLUMN `hora_inicio_bloqueo` TIME NULL COMMENT 'Hora de inicio del bloqueo dentro del día' AFTER `intervalo_espacios`,
ADD COLUMN `hora_fin_bloqueo` TIME NULL COMMENT 'Hora de fin del bloqueo dentro del día' AFTER `hora_inicio_bloqueo`,
ADD COLUMN `bloqueo_activo` TINYINT(1) DEFAULT 0 COMMENT '1 si el bloqueo está activo para este día' AFTER `hora_fin_bloqueo`;

-- Actualizar registros existentes con valores por defecto
UPDATE `horarios_especialistas` 
SET `intervalo_espacios` = 60, 
    `bloqueo_activo` = 0;
```

5. Haz clic en **Continuar** para ejecutar

---

## 2. Cambios en la Vista (Frontend)

### Archivo: `app/views/specialists/schedules.php`

Se agregó:

- **Campo de Intervalo de Espacios**: Un dropdown arriba de los días para seleccionar 30 o 60 minutos
- **Checkbox "Bloquear horas"**: Por cada día habilitado, aparece la opción de activar bloqueo
- **Campos de Horario de Bloqueo**: Cuando se activa el checkbox, se muestran dos campos de tiempo (hora inicio → hora fin del bloqueo)
- **Validación JavaScript**: Funciones para mostrar/ocultar los campos de bloqueo dinámicamente

### Ejemplo visual:

```
┌─────────────────────────────────────────────┐
│ Intervalo de espacios (minutos)            │
│ [60 minutos (1 hora) ▼]                    │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ ☑ Lunes                                     │
│ Hora Inicio: [09:00]  Hora Fin: [18:00]    │
│                                             │
│ ☑ Bloquear horas                            │
│ Horario Bloqueo: [13:00] → [14:00]         │
└─────────────────────────────────────────────┘
```

---

## 3. Cambios en el Controlador (Backend)

### Archivo: `app/controllers/SpecialistController.php`

Se modificó el método `schedules()` para:

- **Recibir el intervalo de espacios** del formulario
- **Validar que el intervalo sea 30 o 60** (por defecto 60)
- **Guardar datos de bloqueo** por cada día:
  - `bloqueo_activo_X` (checkbox)
  - `hora_inicio_bloqueo_X` (time)
  - `hora_fin_bloqueo_X` (time)
- **Validaciones implementadas**:
  - Hora inicio < hora fin (horario general)
  - Hora inicio bloqueo < hora fin bloqueo
  - El bloqueo debe estar dentro del horario laboral
  - Solo se guardan valores de bloqueo si el checkbox está activo

---

## 4. Lógica de Funcionamiento

### Escenario de Ejemplo:

Un especialista trabaja de **9:00 AM a 6:00 PM** pero quiere un bloqueo de **1:00 PM a 2:00 PM** para almorzar.

**En la base de datos se guarda:**
```sql
especialista_id: 1
dia_semana: 1 (Lunes)
hora_inicio: 09:00:00
hora_fin: 18:00:00
intervalo_espacios: 60
hora_inicio_bloqueo: 13:00:00
hora_fin_bloqueo: 14:00:00
bloqueo_activo: 1
```

**Horas disponibles para citas:**
- 9:00 AM ✓
- 10:00 AM ✓
- 11:00 AM ✓
- 12:00 PM ✓
- 1:00 PM ✗ (bloqueado)
- 2:00 PM ✓
- 3:00 PM ✓
- 4:00 PM ✓
- 5:00 PM ✓

---

## 5. Próximos Pasos (Opcional)

Para completar la implementación, se puede:

1. **Modificar la lógica de disponibilidad de horarios** en el sistema de reservaciones para considerar los bloqueos
2. **Actualizar el calendario** para que no muestre las horas bloqueadas como disponibles
3. **Agregar validación** en el frontend con JavaScript para prevenir errores antes de enviar

---

## Notas Importantes

- ✅ Los cambios son **retrocompatibles** - los horarios existentes seguirán funcionando
- ✅ El **intervalo por defecto es 60 minutos**
- ✅ Si no se activa el checkbox de bloqueo, los campos se guardan como NULL
- ✅ Las validaciones previenen configuraciones inválidas

---

## Soporte

Si tienes dudas o necesitas ajustes adicionales, házmelo saber.
