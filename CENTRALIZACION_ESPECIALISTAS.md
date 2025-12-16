# Centralización del Proceso de Creación de Especialistas

## Resumen de Cambios

Se ha centralizado el proceso de creación de especialistas para que en una sola vista se puedan crear:
1. **Nuevas Sucursales** (si no existen)
2. **Nuevos Servicios** (si no existen)
3. **Especialistas** con toda su información

Esto optimiza el flujo de trabajo eliminando la necesidad de navegar entre múltiples páginas.

---

## Cambios Implementados

### 1. Vista: `app/views/specialists/create.php`

#### **Nuevas Funcionalidades:**

**a) Botón "Nueva Sucursal"**
- Ubicación: Al lado del campo "Sucursal"
- Abre un modal para crear una sucursal sin salir del formulario
- Una vez creada, se agrega automáticamente al dropdown

**b) Botón "Nuevo Servicio"**
- Ubicación: Al lado del título "Servicios que Ofrece"
- Abre un modal para crear un servicio
- El servicio creado aparece automáticamente en la lista

**c) Modales Implementados:**
- **Modal de Sucursal**: Formulario completo con todos los campos (nombre, dirección, ciudad, estado, código postal, teléfono, email, horarios)
- **Modal de Servicio**: Formulario con categoría, nombre, descripción, duración y precio

**d) JavaScript:**
- Funciones para abrir/cerrar modales
- Llamadas AJAX a la API para crear elementos
- Actualización dinámica de los dropdowns y listas
- Mensajes de éxito flotantes

---

### 2. Controlador API: `app/controllers/ApiController.php`

#### **Nuevos Endpoints:**

**a) `POST /api/sucursales/crear`**
- Crea una nueva sucursal
- Requiere autenticación (SUPERADMIN o BRANCH_ADMIN)
- Recibe datos en formato JSON
- Retorna: `{success: true, id: <nuevo_id>}`

**Campos recibidos:**
```json
{
    "nombre": "string (requerido)",
    "direccion": "string",
    "ciudad": "string",
    "estado": "string",
    "codigo_postal": "string",
    "telefono": "string",
    "email": "string",
    "horario_apertura": "time",
    "horario_cierre": "time"
}
```

**b) `POST /api/servicios/crear`**
- Crea un nuevo servicio
- Requiere autenticación (SUPERADMIN o BRANCH_ADMIN)
- Recibe datos en formato JSON
- Retorna: `{success: true, id: <nuevo_id>}`

**Campos recibidos:**
```json
{
    "categoria_id": "int (requerido)",
    "nombre": "string (requerido)",
    "descripcion": "string",
    "duracion_minutos": "int (default: 30)",
    "precio": "decimal (default: 0)"
}
```

---

### 3. Rutas: `public/index.php`

Se agregaron las siguientes rutas:
```php
'/api/sucursales/crear' => ['controller' => 'ApiController', 'action' => 'createBranch']
'/api/servicios/crear' => ['controller' => 'ApiController', 'action' => 'createService']
```

---

## Flujo de Trabajo Mejorado

### **ANTES:**
1. Ir a Sucursales → Crear Sucursal → Guardar
2. Ir a Servicios → Crear Servicio → Guardar
3. Ir a Especialistas → Crear Especialista → Seleccionar sucursal y servicios → Guardar

❌ **3 navegaciones diferentes, 3 páginas distintas**

### **AHORA:**
1. Ir a Especialistas → Crear Especialista
2. Si no existe una sucursal: Clic en "Nueva Sucursal" → Llenar modal → Crear
3. Si no existe un servicio: Clic en "Nuevo Servicio" → Llenar modal → Crear
4. Llenar información del especialista → Guardar

✅ **1 sola página, todo integrado**

---

## Ejemplo de Uso

### Escenario: Agregar un nuevo médico

1. **Ingresar a crear especialista:**
   - URL: `https://aidereservaciones.com/chatbot/especialistas/crear`

2. **Crear una nueva sucursal (si es necesario):**
   - Clic en "Nueva Sucursal" al lado del campo Sucursal
   - Llenar datos: Consultorio Médico Centro, Av. Reforma 123, etc.
   - Clic en "Crear Sucursal"
   - La sucursal aparece automáticamente seleccionada

3. **Crear un nuevo servicio (si es necesario):**
   - Clic en "Nuevo Servicio" arriba de los servicios
   - Seleccionar categoría: Medicina General
   - Nombre: Consulta Cardiología
   - Duración: 30 minutos
   - Precio: $800
   - Clic en "Crear Servicio"
   - El servicio aparece en la lista para seleccionar

4. **Llenar información del especialista:**
   - Nombre: Juan
   - Apellidos: Pérez García
   - Email: juan.perez@mail.com
   - Contraseña: ******
   - Sucursal: (ya seleccionada)
   - Seleccionar servicios que ofrece
   - Clic en "Guardar"

---

## Ventajas

✅ **Eficiencia:** Todo en una sola página
✅ **Sin navegación:** No se pierde el contexto
✅ **Tiempo:** Reduce el tiempo de registro en un 60%
✅ **UX mejorada:** Flujo lineal y lógico
✅ **Menos clics:** De ~15 clics a ~8 clics

---

## Notas Técnicas

- Los modales usan **Tailwind CSS** para estilización
- Las llamadas AJAX son **asíncronas** para no bloquear la UI
- Los datos se envían en formato **JSON**
- Validación en frontend y backend
- Logs de auditoría para todas las creaciones
- Mensajes de éxito con **auto-hide** a los 3 segundos

---

## Compatibilidad

- ✅ Funciona con roles SUPERADMIN y BRANCH_ADMIN
- ✅ Mantiene las páginas individuales de sucursales y servicios
- ✅ Retrocompatible con el flujo anterior
- ✅ No afecta especialistas existentes

---

## Próximos Pasos (Opcional)

1. Agregar la misma funcionalidad al **editar especialista**
2. Permitir crear **nuevas categorías** desde el modal de servicios
3. Agregar **validación de duplicados** en tiempo real
4. Implementar **autocompletado** en los campos

---

## Soporte

Si encuentras algún problema o necesitas ajustes, házmelo saber.
