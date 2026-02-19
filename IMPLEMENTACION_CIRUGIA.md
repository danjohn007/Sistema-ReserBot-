# Implementación de Función de Cirugía

## Descripción
Se ha implementado una nueva funcionalidad en el calendario que permite a los especialistas programar cirugías. Esta función es similar al bloqueo de horarios pero está específicamente diseñada para procedimientos quirúrgicos.

## Características
- **Botón dedicado**: Opción "Programar Cirugía" en el modal de acciones del calendario (color morado/púrpura)
- **Campos específicos**: 
  - Fecha y hora de inicio/fin
  - Sucursal (opcional)
  - Asistentes (en lugar de "Motivo")
- **Tipo automático**: El tipo se establece automáticamente como 'cirugia' (no requiere selección manual)
- **Duración sugerida**: Por defecto, se sugiere una duración de 2 horas (vs 1 hora para bloqueos normales)
- **Visualización**: Las cirugías aparecen en el calendario con el icono ⚕️ y **color morado (#9333EA)** para diferenciarlas de otros bloqueos

## Instalación

### 1. Actualizar Base de Datos
Antes de usar esta funcionalidad, **DEBES ejecutar el siguiente script SQL**:

```sql
-- Ejecutar en phpMyAdmin o cliente MySQL
ALTER TABLE bloqueos_horario 
MODIFY tipo ENUM('vacaciones', 'pausa', 'personal', 'puntual', 'cirugia', 'otro') DEFAULT 'otro';
```

O ejecutar el archivo SQL incluido:
```bash
mysql -u tu_usuario -p nombre_base_datos < sql/add_cirugia_tipo.sql
```

### 2. Archivos Modificados
- `app/views/calendar/index.php` - Vista del calendario con modal y funciones de cirugía
- `app/controllers/CalendarController.php` - Backend para procesar cirugías y asignar colores por tipo de bloqueo
- `sql/add_cirugia_tipo.sql` - Script de migración para la base de datos (NUEVO)

## Uso

1. En el calendario, haz clic en un día disponible
2. Selecciona "Programar Cirugía" (botón morado)
3. Completa los campos:
   - **Hora de Inicio**: Hora de inicio del procedimiento
   - **Hora de Fin**: Hora estimada de finalización
   - **Sucursal**: (Opcional) Selecciona la sucursal o deja vacío para todas
   - **Asistentes**: (Opcional) Lista de personal que participará (ej: Dr. García, Enf. María López)
4. Haz clic en "Programar Cirugía"

## Notas Técnicas
- Las cirugías se almacenan en la tabla `bloqueos_horario` con `tipo='cirugia'`
- El campo `motivo` se reutiliza para almacenar la lista de asistentes
- El calendario bloqueará automáticamente ese horario para reservaciones normales
- Los especialistas pueden eliminar cirugías programadas desde el modal de detalles del evento

## Diferencias con Bloqueo Normal

| Característica | Bloqueo Normal | Cirugía |
|----------------|----------------|---------|
| Color en calendario | Rojo (#DC2626) | **Morado (#9333EA)** |
| Icono | 🔒 | ⚕️ |
| Duración sugerida | 1 hora | 2 horas |
| Campo descriptivo | Motivo | Asistentes |
| Tipo ENUM | 'puntual', 'personal', etc. | 'cirugia' |

## Validaciones
- La hora de inicio debe ser menor que la hora de fin
- No se permiten cirugías si ya hay reservaciones confirmadas en ese horario
- Solo el especialista propietario puede programar/eliminar cirugías en su agenda

## Colores de Bloqueos en el Calendario

Para facilitar la identificación visual, cada tipo de bloqueo tiene un color distintivo:

| Tipo | Icono | Color | Código Hex |
|------|-------|-------|------------|
| Cirugía | ⚕️ | **Morado** | #9333EA |
| Bloqueo Puntual | 🔒 | Rojo | #DC2626 |
| Vacaciones | 🌴 | Azul | #3B82F6 |
| Pausa/Descanso | ☕ | Amarillo | #F59E0B |
| Asunto Personal | 👤 | Violeta | #8B5CF6 |
| Otro | ⛔ | Gris | #6B7280 |

---
**Fecha de implementación**: <?= date('Y-m-d') ?>
**Versión**: 1.0.0
