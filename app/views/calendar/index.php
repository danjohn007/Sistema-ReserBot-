<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>Calendario de Citas
                </h2>
                <p class="text-sm text-gray-500 mt-1">Visualiza y gestiona tus citas programadas</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-4">
            <?php if (!empty($branches)): ?>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building text-gray-400 mr-1"></i>Sucursal
                </label>
                <select id="filter_sucursal" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition" 
                        onchange="filterCalendar()">
                    <option value="">
                        <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
                        Todas mis sucursales
                        <?php else: ?>
                        Todas las sucursales
                        <?php endif; ?>
                    </option>
                    <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($specialists)): ?>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-md text-gray-400 mr-1"></i>Especialista
                </label>
                <select id="filter_especialista" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition" 
                        onchange="filterCalendar()">
                    <option value="">Todos los especialistas</option>
                    <?php foreach ($specialists as $spec): ?>
                    <option value="<?= $spec['id'] ?>">
                        <?= e($spec['nombre'] . ' ' . $spec['apellidos']) ?>
                        <?= isset($spec['sucursal_nombre']) ? ' - ' . e($spec['sucursal_nombre']) : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Estado Legend -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-info-circle text-gray-400 mr-1"></i>Leyenda de Estados
                </label>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #F59E0B; color: white;">
                        <i class="fas fa-clock mr-1"></i>Pendiente
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #10B981; color: white;">
                        <i class="fas fa-check mr-1"></i>Confirmada
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #6B7280; color: white;">
                        <i class="fas fa-check-double mr-1"></i>Completada
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #F97316; color: white;">
                        <i class="fas fa-user-times mr-1"></i>No Asisti&oacute;
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Calendar Container -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div id="calendar"></div>
    </div>
</div>

<style>
/* Hacer que los días del calendario sean claramente clickeables */
.fc-daygrid-day {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.fc-daygrid-day:hover {
    background-color: rgba(59, 130, 246, 0.05) !important;
}

.fc-daygrid-day.fc-day-past {
    cursor: not-allowed;
    opacity: 0.6;
}

.fc-daygrid-day.fc-day-past:hover {
    background-color: transparent !important;
}

/* Los eventos mantienen su propio cursor */
.fc-event {
    cursor: pointer !important;
}

.fc-event:hover {
    opacity: 0.9;
}

/* Indicador visual sutil en días vacíos */
.fc-daygrid-day-frame {
    position: relative;
}

.fc-daygrid-day:not(.fc-day-past) .fc-daygrid-day-number {
    transition: all 0.2s ease;
}

.fc-daygrid-day:not(.fc-day-past):hover .fc-daygrid-day-number {
    transform: scale(1.1);
    font-weight: 600;
    color: #3B82F6;
}

/* Animación de pulso para horario pre-seleccionado */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
    }
}
</style>

<!-- Create Reservation Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-t-2xl sticky top-0 z-10">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-plus-circle mr-3"></i>
                    <span>Nueva Reservaci&oacute;n</span>
                </h3>
                <button onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <p class="text-green-100 text-sm mt-2" id="selected-date-display">Fecha seleccionada</p>
        </div>
        
        <form id="createReservationForm" class="p-6 space-y-6">
            <!-- Cliente -->
            <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-1"></i>Nombre del Cliente *
                </label>
                <input type="text" id="create_nombre_cliente" required
                       placeholder="Ingrese el nombre completo del cliente"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            <?php endif; ?>
            
            <!-- Sucursal -->
            <?php if (!empty($branches)): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building mr-1"></i>Sucursal *
                </label>
                <select id="create_sucursal" required onchange="loadServicesForCreate()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- Seleccione una sucursal --</option>
                    <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Servicio -->
            <div id="create_service_section" style="display:none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-concierge-bell mr-1"></i>Servicio *
                </label>
                <select id="create_servicio" required onchange="loadTimeSlotsForCreate()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- Seleccione un servicio --</option>
                </select>
                <div id="service_details" class="mt-2 p-3 bg-blue-50 rounded-lg hidden">
                    <p class="text-sm text-gray-700">
                        <strong>Duraci&oacute;n:</strong> <span id="service_duration"></span> min | 
                        <strong>Precio:</strong> <span id="service_price"></span>
                    </p>
                    <div id="emergency_service_notice" class="mt-2 p-2 bg-red-100 border border-red-300 rounded text-sm text-red-800 hidden">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Servicio de Emergencia:</strong> Solo disponible en horarios de emergencia
                    </div>
                </div>
            </div>
            
            <!-- Horarios disponibles -->
            <div id="create_time_section" style="display:none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1"></i>Horario Disponible *
                </label>
                <div id="time_slots_container" class="grid grid-cols-4 gap-2">
                    <!-- Time slots will be loaded here -->
                </div>
                <p class="text-xs text-gray-500 mt-2" id="no_slots_message" style="display:none;">
                    <i class="fas fa-info-circle mr-1"></i>No hay horarios disponibles para esta fecha
                </p>
            </div>
            
            <!-- Notas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-notes-medical mr-1"></i>Notas adicionales
                </label>
                <textarea id="create_notas" rows="3"
                          placeholder="Informaci&oacute;n adicional..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
            </div>
            
            <input type="hidden" id="create_fecha" value="">
            <input type="hidden" id="create_especialista_id" value="<?= $currentSpecialistId ?? '' ?>">
            <input type="hidden" id="create_hora_inicio" value="">
        </form>
        
        <div class="p-6 bg-gray-50 border-t rounded-b-2xl sticky bottom-0">
            <div class="flex justify-end gap-3">
                <button onclick="closeCreateModal()" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button onclick="submitCreateReservation()" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-check mr-2"></i>Crear Reservaci&oacute;n
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
        <div class="p-6 bg-gradient-to-r from-primary to-secondary rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center" id="modal-title">
                    <i class="fas fa-calendar-check mr-3"></i>
                    <span>Detalles de la Cita</span>
                </h3>
                <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="modal-content">
            <!-- Content loaded dynamically -->
        </div>
        <div class="p-6" id="modal-edit-content" style="display:none;">
            <!-- Edit form loaded dynamically -->
        </div>
        <div class="p-6 bg-gray-50 border-t rounded-b-2xl" id="modal-footer-view">
            <div class="space-y-3">
                <p class="text-sm text-gray-500 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Haz clic en "Ver Detalle" para m&aacute;s informaci&oacute;n
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Confirmar: solo si está pendiente -->
                    <button onclick="confirmReservation()" id="modal-confirm-btn" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg flex items-center justify-center" style="display:none;">
                        <i class="fas fa-check-circle mr-2"></i>Confirmar
                    </button>
                    <!-- Reagendar: solo si está pendiente -->
                    <button onclick="toggleEditMode()" id="modal-reschedule-btn" class="px-4 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition shadow-md hover:shadow-lg flex items-center justify-center" style="display:none;">
                        <i class="fas fa-edit mr-2"></i>Reagendar
                    </button>
                    <!-- Completar: solo si está confirmada -->
                    <button onclick="completeReservation()" id="modal-complete-btn" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md hover:shadow-lg flex items-center justify-center" style="display:none;">
                        <i class="fas fa-check-double mr-2"></i>Completar
                    </button>
                    <!-- No Asistió: solo si está confirmada -->
                    <button onclick="noShowReservation()" id="modal-noshow-btn" class="px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition shadow-md hover:shadow-lg flex items-center justify-center" style="display:none;">
                        <i class="fas fa-user-times mr-2"></i>No Asisti&oacute;
                    </button>
                    <!-- Cancelar: si está pendiente o confirmada -->
                    <button onclick="cancelReservation()" id="modal-cancel-btn" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-md hover:shadow-lg flex items-center justify-center" style="display:none;">
                        <i class="fas fa-times-circle mr-2"></i>Cancelar
                    </button>
                    <!-- Ver Detalle: siempre visible -->
                    <a href="#" id="modal-view-link" class="px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary transition shadow-md hover:shadow-lg flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>Ver Detalle
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6 bg-gray-50 border-t rounded-b-2xl" id="modal-footer-edit" style="display:none;">
            <div class="flex justify-end gap-3">
                <button onclick="cancelEdit()" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button onclick="saveReschedule()" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    // Crear mapeo de sucursales a colores basado en el orden del filtro
    const sucursalColorMap = {};
    <?php foreach ($branches as $index => $branch): ?>
    sucursalColorMap[<?= $branch['id'] ?>] = <?= $index ?>;
    <?php endforeach; ?>
    <?php endif; ?>
    
    window.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 650,
        contentHeight: 600,
        aspectRatio: 1.8,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        buttonIcons: {
            prev: 'chevron-left',
            next: 'chevron-right'
        },
        events: function(info, successCallback, failureCallback) {
            const specialistId = document.getElementById('filter_especialista')?.value || '';
            const branchId = document.getElementById('filter_sucursal')?.value || '';
            
            let url = '<?= url('/calendario/eventos') ?>?start=' + info.startStr + '&end=' + info.endStr;
            if (specialistId) url += '&especialista_id=' + specialistId;
            if (branchId) url += '&sucursal_id=' + branchId;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showEventModal(info.event);
        },
        dateClick: function(info) {
            // Solo permitir clicks en fechas futuras o hoy
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Extraer fecha y hora si viene en formato ISO
            const fechaSoloDate = info.dateStr.split('T')[0];
            const clickedDate = new Date(fechaSoloDate);
            
            if (clickedDate >= today) {
                // Extraer hora si viene en el dateStr (vista semanal/día)
                let horaSeleccionada = null;
                if (info.dateStr.includes('T')) {
                    const partes = info.dateStr.split('T');
                    if (partes[1]) {
                        horaSeleccionada = partes[1].substring(0, 5); // "09:00"
                    }
                }
                openCreateModal(info.dateStr, horaSeleccionada);
            }
        },
        eventDidMount: function(info) {
            // Add tooltip with enhanced styling
            info.el.title = info.event.title;
            info.el.style.cursor = 'pointer';
            info.el.style.transition = 'all 0.2s';
            
            // Add hover effect
            info.el.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            });
            info.el.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });
            
            <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
            // Colorear la celda del día cuando se monta un evento
            const fecha = info.event.start;
            if (fecha && info.event.extendedProps.sucursal_id) {
                // Verificar si hay filtro de sucursal activo
                const filtroSucursal = document.getElementById('filter_sucursal')?.value || '';
                
                // Si hay filtro y el evento no es de esa sucursal, no colorear
                if (filtroSucursal && info.event.extendedProps.sucursal_id != filtroSucursal) {
                    return;
                }
                
                const year = fecha.getFullYear();
                const month = String(fecha.getMonth() + 1).padStart(2, '0');
                const day = String(fecha.getDate()).padStart(2, '0');
                const fechaStr = `${year}-${month}-${day}`;
                
                const sucursalId = info.event.extendedProps.sucursal_id;
                const colorIndex = sucursalColorMap[sucursalId] !== undefined 
                    ? sucursalColorMap[sucursalId] % 6
                    : 0;
                
                // Detectar si es hoy
                const hoy = new Date();
                const hoyStr = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
                const esHoy = fechaStr === hoyStr;
                
                const coloresSuaves = [
                    'rgba(59, 130, 246, 0.25)',   // Azul
                    'rgba(236, 72, 153, 0.25)',   // Rosa
                    'rgba(16, 185, 129, 0.25)',   // Verde
                    'rgba(245, 158, 11, 0.25)',   // Naranja
                    'rgba(139, 92, 246, 0.25)',   // Púrpura
                    'rgba(239, 68, 68, 0.25)'     // Rojo
                ];
                
                const coloresFuertes = [
                    'rgba(59, 130, 246, 0.45)',   // Azul
                    'rgba(236, 72, 153, 0.45)',   // Rosa
                    'rgba(16, 185, 129, 0.45)',   // Verde
                    'rgba(245, 158, 11, 0.45)',   // Naranja
                    'rgba(139, 92, 246, 0.45)',   // Púrpura
                    'rgba(239, 68, 68, 0.45)'     // Rojo
                ];
                
                const color = esHoy ? coloresFuertes[colorIndex] : coloresSuaves[colorIndex];
                
                // Buscar la celda correspondiente
                setTimeout(() => {
                    const celda = document.querySelector(`.fc-day[data-date="${fechaStr}"]`);
                    if (celda) {
                        celda.style.backgroundColor = color;
                        celda.style.transition = 'background-color 0.3s ease';
                    }
                }, 10);
            }
            <?php endif; ?>
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        nowIndicator: true,
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 3,
        moreLinkText: 'más',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        views: {
            timeGridWeek: {
                slotDuration: '00:30:00'
            },
            timeGridDay: {
                slotDuration: '00:15:00'
            }
        }
    });
    
    calendar.render();
});

function filterCalendar() {
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    // Limpiar colores de fondo antes de recargar eventos
    document.querySelectorAll('.fc-day').forEach(celda => {
        celda.style.backgroundColor = '';
    });
    <?php endif; ?>
    window.calendar.refetchEvents();
}

let currentEvent = null;

function showEventModal(event) {
    currentEvent = event;
    const modal = document.getElementById('eventModal');
    const props = event.extendedProps;
    
    const startDate = new Date(event.start);
    const endDate = new Date(event.end);
    const dateStr = startDate.toLocaleDateString('es-MX', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    const timeStr = startDate.toLocaleTimeString('es-MX', { 
        hour: '2-digit', 
        minute: '2-digit' 
    }) + ' - ' + endDate.toLocaleTimeString('es-MX', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    // Asegurarse de estar en modo vista
    document.getElementById('modal-content').style.display = 'block';
    document.getElementById('modal-edit-content').style.display = 'none';
    document.getElementById('modal-footer-view').style.display = 'block';
    document.getElementById('modal-footer-edit').style.display = 'none';
    
    // Mostrar/ocultar botones según estado
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const rescheduleBtn = document.getElementById('modal-reschedule-btn');
    const completeBtn = document.getElementById('modal-complete-btn');
    const noShowBtn = document.getElementById('modal-noshow-btn');
    const cancelBtn = document.getElementById('modal-cancel-btn');
    
    // Ocultar todos primero
    confirmBtn.style.display = 'none';
    rescheduleBtn.style.display = 'none';
    completeBtn.style.display = 'none';
    noShowBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    
    // Lógica según estado
    switch(props.estado) {
        case 'pendiente':
            // Pendiente: Confirmar, Reagendar, Cancelar
            confirmBtn.style.display = 'block';
            rescheduleBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
            break;
            
        case 'confirmada':
            // Confirmada: Completar, No Asistió, Cancelar (NO reagendar)
            completeBtn.style.display = 'block';
            noShowBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
            break;
            
        case 'cancelada':
        case 'completada':
        case 'no_asistio':
            // Estados finales: solo ver detalle (todos los botones ocultos)
            break;
    }
    
    document.getElementById('modal-content').innerHTML = `
        <div class="space-y-4">
            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-calendar text-blue-600 mt-1 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-gray-700">Fecha y Hora</p>
                    <p class="text-sm text-gray-900">${dateStr}</p>
                    <p class="text-sm text-blue-600 font-semibold">${timeStr}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-barcode mr-1"></i>Código</p>
                    <p class="text-sm font-semibold text-gray-900">${props.codigo}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-flag mr-1"></i>Estado</p>
                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium ${getStatusClass(props.estado)}">
                        ${props.estado.charAt(0).toUpperCase() + props.estado.slice(1)}
                    </span>
                </div>
            </div>
            
            <div class="p-3 bg-green-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1"><i class="fas fa-user mr-1"></i>Cliente</p>
                <p class="text-sm font-semibold text-gray-900">${props.cliente}</p>
            </div>
            
            <div class="p-3 bg-purple-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1"><i class="fas fa-user-md mr-1"></i>Especialista</p>
                <p class="text-sm font-semibold text-gray-900">${props.especialista}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-indigo-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-concierge-bell mr-1"></i>Servicio</p>
                    <p class="text-sm font-semibold text-gray-900">${props.servicio}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-dollar-sign mr-1"></i>Precio</p>
                    <p class="text-sm font-semibold text-emerald-700">${props.precio}</p>
                </div>
            </div>
        </div>
    `;
    document.getElementById('modal-view-link').href = '<?= url('/reservaciones/ver') ?>?id=' + event.id;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('eventModal').classList.add('hidden');
    currentEvent = null;
}

function toggleEditMode() {
    if (!currentEvent) return;
    
    const startDate = new Date(currentEvent.start);
    const props = currentEvent.extendedProps;
    const fechaStr = startDate.toISOString().split('T')[0];
    const horaInicio = startDate.toTimeString().substring(0, 5);
    
    document.getElementById('modal-edit-content').innerHTML = `
        <div class="space-y-4">
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Reagendamiento de Cita
                </p>
                <p class="text-xs text-gray-600">
                    Selecciona nueva fecha y hora para la cita. El sistema verificará la disponibilidad automáticamente.
                </p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-barcode mr-1"></i>Código</p>
                    <p class="text-sm font-semibold text-gray-900">${props.codigo}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1"><i class="fas fa-concierge-bell mr-1"></i>Servicio</p>
                    <p class="text-sm font-semibold text-gray-900">${props.servicio}</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-day mr-1 text-primary"></i>Nueva Fecha
                </label>
                <input type="date" id="edit-fecha" value="${fechaStr}" 
                       onchange="loadAvailableTimesForEdit()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            
            <div id="edit-time-container">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1 text-primary"></i>Nueva Hora
                </label>
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando horarios disponibles...
                </div>
            </div>
            
            <input type="hidden" id="edit-reservacion-id" value="${currentEvent.id}">
            <input type="hidden" id="edit-especialista-id" value="${props.especialista_id || ''}">
            <input type="hidden" id="edit-servicio-id" value="${props.servicio_id || ''}">
            <input type="hidden" id="edit-sucursal-id" value="${props.sucursal_id || ''}">
        </div>
    `;
    
    // Cambiar a modo edición
    document.getElementById('modal-content').style.display = 'none';
    document.getElementById('modal-edit-content').style.display = 'block';
    document.getElementById('modal-footer-view').style.display = 'none';
    document.getElementById('modal-footer-edit').style.display = 'block';
    
    // Cargar horarios disponibles para la fecha actual
    loadAvailableTimesForEdit();
}

function cancelEdit() {
    document.getElementById('modal-content').style.display = 'block';
    document.getElementById('modal-edit-content').style.display = 'none';
    document.getElementById('modal-footer-view').style.display = 'block';
    document.getElementById('modal-footer-edit').style.display = 'none';
}

async function loadAvailableTimesForEdit() {
    const fecha = document.getElementById('edit-fecha').value;
    const especialistaId = document.getElementById('edit-especialista-id').value;
    const servicioId = document.getElementById('edit-servicio-id').value;
    const sucursalId = document.getElementById('edit-sucursal-id').value;
    const reservacionId = document.getElementById('edit-reservacion-id').value;
    
    if (!fecha || !especialistaId || !servicioId) return;
    
    const container = document.getElementById('edit-time-container');
    container.innerHTML = `
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-clock mr-1 text-primary"></i>Nueva Hora
        </label>
        <div class="text-center py-4 text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando horarios disponibles...
        </div>
    `;
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/api/disponibilidad?especialista_id=${especialistaId}&servicio_id=${servicioId}&fecha=${fecha}&sucursal_id=${sucursalId}&excluir_reservacion=${reservacionId}`);
        const data = await response.json();
        
        if (data.slots && data.slots.length > 0) {
            let slotsHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1 text-primary"></i>Nueva Hora
                </label>
                <div class="grid grid-cols-4 gap-2 max-h-64 overflow-y-auto p-2 border border-gray-200 rounded-lg">
            `;
            
            data.slots.forEach(slot => {
                const time = slot.hora_inicio.substring(0, 5);
                slotsHTML += `
                    <label class="relative cursor-pointer">
                        <input type="radio" name="edit-hora" value="${slot.hora_inicio}" class="peer sr-only">
                        <div class="p-2 border-2 rounded-lg text-center text-sm peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400 transition">
                            ${time}
                        </div>
                    </label>
                `;
            });
            
            slotsHTML += '</div>';
            container.innerHTML = slotsHTML;
        } else {
            container.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1 text-primary"></i>Nueva Hora
                </label>
                <div class="text-center py-4 text-orange-600 bg-orange-50 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>No hay horarios disponibles para esta fecha
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar horarios:', error);
        container.innerHTML = `
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-clock mr-1 text-primary"></i>Nueva Hora
            </label>
            <div class="text-center py-4 text-red-600 bg-red-50 rounded-lg">
                <i class="fas fa-times-circle mr-2"></i>Error al cargar horarios
            </div>
        `;
    }
}

async function saveReschedule() {
    const reservacionId = document.getElementById('edit-reservacion-id').value;
    const nuevaFecha = document.getElementById('edit-fecha').value;
    const nuevaHora = document.querySelector('input[name="edit-hora"]:checked')?.value;
    
    if (!nuevaFecha || !nuevaHora) {
        alert('Por favor selecciona una fecha y hora');
        return;
    }
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/reservaciones/reagendar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${reservacionId}&fecha_cita=${nuevaFecha}&hora_inicio=${nuevaHora}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Cita reagendada exitosamente');
            closeModal();
            // Recargar eventos del calendario
            window.calendar.refetchEvents();
        } else {
            alert('Error al reagendar: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al guardar:', error);
        alert('Error al guardar los cambios');
    }
}

function getStatusClass(status) {
    const classes = {
        'pendiente': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'confirmada': 'bg-green-100 text-green-800 border border-green-200',
        'en_progreso': 'bg-blue-100 text-blue-800 border border-blue-200',
        'completada': 'bg-gray-100 text-gray-800 border border-gray-200',
        'cancelada': 'bg-red-100 text-red-800 border border-red-200',
        'no_asistio': 'bg-orange-100 text-orange-800 border border-orange-200'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Confirmar reservación
async function confirmReservation() {
    if (!currentEvent) return;
    
    if (!confirm('¿Está seguro de confirmar esta cita?')) {
        return;
    }
    
    try {
        const response = await fetch('<?= BASE_URL ?>/reservaciones/confirmar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${currentEvent.id}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Cita confirmada exitosamente');
            closeModal();
            window.calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'No se pudo confirmar la cita'));
        }
    } catch (error) {
        console.error('Error al confirmar:', error);
        alert('Error al confirmar la cita');
    }
}

// Cancelar reservación
async function cancelReservation() {
    if (!currentEvent) return;
    
    const motivo = prompt('¿Por qué desea cancelar esta cita?\n(Opcional - presione Aceptar para continuar)');
    
    if (motivo === null) {
        // Usuario presionó Cancelar
        return;
    }
    
    if (!confirm('¿Está seguro de cancelar esta cita?')) {
        return;
    }
    
    try {
        const formData = new URLSearchParams();
        formData.append('id', currentEvent.id);
        if (motivo && motivo.trim()) {
            formData.append('motivo', motivo.trim());
        }
        
        const response = await fetch('<?= BASE_URL ?>/reservaciones/cancelar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Cita cancelada exitosamente');
            closeModal();
            window.calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'No se pudo cancelar la cita'));
        }
    } catch (error) {
        console.error('Error al cancelar:', error);
        alert('Error al cancelar la cita');
    }
}

// Completar reservación
async function completeReservation() {
    if (!currentEvent) return;
    
    if (!confirm('¿Marcar esta cita como completada?')) {
        return;
    }
    
    try {
        const response = await fetch('<?= BASE_URL ?>/reservaciones/completar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${currentEvent.id}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Cita marcada como completada');
            closeModal();
            window.calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'No se pudo completar la cita'));
        }
    } catch (error) {
        console.error('Error al completar:', error);
        alert('Error al completar la cita');
    }
}

// Marcar como no asistió
async function noShowReservation() {
    if (!currentEvent) return;
    
    if (!confirm('¿Marcar que el cliente NO asistió a esta cita?')) {
        return;
    }
    
    try {
        const response = await fetch('<?= BASE_URL ?>/reservaciones/no-asistio', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${currentEvent.id}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Marcado como no asistió');
            closeModal();
            window.calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'No se pudo marcar como no asistió'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al marcar como no asistió');
    }
}

// Close modal on overlay click
document.getElementById('eventModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('createModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeCreateModal();
    }
});

// ============= FUNCIONES PARA CREAR RESERVA =============

// Variable global para almacenar la hora pre-seleccionada
let horaPreseleccionada = null;

function openCreateModal(dateStr, horaSeleccionada = null) {
    const modal = document.getElementById('createModal');
    const dateDisplay = document.getElementById('selected-date-display');
    const dateInput = document.getElementById('create_fecha');
    
    // Guardar hora pre-seleccionada para usar después
    horaPreseleccionada = horaSeleccionada;
    
    // Extraer solo la fecha (YYYY-MM-DD) del dateStr
    // En vista mensual viene "2026-02-15"
    // En vista semanal viene "2026-02-15T14:00:00"
    const fechaSoloDate = dateStr.split('T')[0];
    
    // Formatear fecha para display
    const fecha = new Date(fechaSoloDate + 'T00:00:00');
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fecha.toLocaleDateString('es-MX', opciones);
    
    // Agregar hora al display si fue seleccionada
    let displayText = `Fecha seleccionada: ${fechaFormateada}`;
    if (horaSeleccionada) {
        displayText += ` - Hora: ${horaSeleccionada}`;
    }
    dateDisplay.textContent = displayText;
    dateInput.value = fechaSoloDate;
    
    // Resetear formulario
    document.getElementById('createReservationForm').reset();
    document.getElementById('create_fecha').value = fechaSoloDate;
    document.getElementById('create_service_section').style.display = 'none';
    document.getElementById('create_time_section').style.display = 'none';
    document.getElementById('service_details').classList.add('hidden');
    
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    // Si es especialista, determinar automáticamente la sucursal según el día
    const usuarioId = <?= $user['id'] ?>;
    document.getElementById('create_especialista_id').value = usuarioId;
    
    // Calcular el día de la semana (1=Lunes, 7=Domingo)
    const dayOfWeek = fecha.getDay(); // 0=Domingo, 1=Lunes, ..., 6=Sábado
    const diaSemana = dayOfWeek === 0 ? 7 : dayOfWeek; // Convertir a formato MySQL (1-7)
    
    // Obtener la sucursal donde trabaja ese día
    fetch(`<?= BASE_URL ?>/api/especialista-sucursal-dia?usuario_id=${usuarioId}&dia_semana=${diaSemana}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucursal_id) {
                // Auto-seleccionar la sucursal
                const sucursalSelect = document.getElementById('create_sucursal');
                sucursalSelect.value = data.sucursal_id;
                document.getElementById('create_especialista_id').value = data.especialista_id;
                
                // Cargar servicios automáticamente
                loadServicesForCreate();
            } else {
                alert('No tiene horarios configurados para este día de la semana');
                modal.classList.add('hidden');
                return;
            }
        })
        .catch(error => {
            console.error('Error al obtener sucursal:', error);
            alert('Error al cargar la información. Por favor intente nuevamente.');
            modal.classList.add('hidden');
            return;
        });
    <?php endif; ?>
    
    modal.classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    horaPreseleccionada = null; // Limpiar hora pre-seleccionada
}

async function loadServicesForCreate() {
    const sucursalId = document.getElementById('create_sucursal').value;
    const especialistaId = document.getElementById('create_especialista_id').value;
    
    if (!sucursalId) return;
    
    // Si es especialista, obtener su especialista_id para esta sucursal
    let finalEspecialistaId = especialistaId;
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    if (especialistaId) {
        try {
            const response = await fetch(`<?= BASE_URL ?>/api/especialista-sucursal?usuario_id=${especialistaId}&sucursal_id=${sucursalId}`);
            const data = await response.json();
            if (data.especialista_id) {
                finalEspecialistaId = data.especialista_id;
                document.getElementById('create_especialista_id').value = finalEspecialistaId;
            }
        } catch (error) {
            console.error('Error loading specialist:', error);
            return;
        }
    }
    <?php endif; ?>
    
    if (!finalEspecialistaId) return;
    
    // Cargar servicios
    try {
        const response = await fetch(`<?= BASE_URL ?>/api/servicios?especialista_id=${finalEspecialistaId}`);
        const data = await response.json();
        
        const select = document.getElementById('create_servicio');
        select.innerHTML = '<option value="">-- Seleccione un servicio --</option>';
        
        if (data.services && data.services.length > 0) {
            data.services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                // Agregar emoji de emergencia si es servicio de emergencia
                const emergencyLabel = service.es_emergencia ? '🚨 ' : '';
                option.textContent = `${emergencyLabel}${service.nombre} - ${service.duracion_minutos} min`;
                option.dataset.duracion = service.duracion_minutos;
                option.dataset.precio = service.precio;
                option.dataset.esEmergencia = service.es_emergencia == 1 ? '1' : '0';
                
                // Debug
                console.log(`Servicio: ${service.nombre}, es_emergencia DB: ${service.es_emergencia}, dataset: ${option.dataset.esEmergencia}`);
                
                select.appendChild(option);
            });
            document.getElementById('create_service_section').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading services:', error);
    }
}

function loadTimeSlotsForCreate() {
    const servicioSelect = document.getElementById('create_servicio');
    const selectedOption = servicioSelect.options[servicioSelect.selectedIndex];
    
    if (!selectedOption.value) return;
    
    // Obtener si es servicio de emergencia
    const esEmergencia = selectedOption.dataset.esEmergencia === '1';
    
    // Debug: verificar detección
    console.log('Dataset esEmergencia:', selectedOption.dataset.esEmergencia, 'Tipo:', typeof selectedOption.dataset.esEmergencia);
    console.log('Es emergencia (booleano):', esEmergencia);
    
    // Mostrar detalles del servicio
    document.getElementById('service_duration').textContent = selectedOption.dataset.duracion;
    document.getElementById('service_price').textContent = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(selectedOption.dataset.precio);
    document.getElementById('service_details').classList.remove('hidden');
    
    // Mostrar aviso si es servicio de emergencia
    const emergencyNotice = document.getElementById('emergency_service_notice');
    if (esEmergencia) {
        emergencyNotice.classList.remove('hidden');
    } else {
        emergencyNotice.classList.add('hidden');
    }
    
    // Cargar horarios disponibles
    const especialistaId = document.getElementById('create_especialista_id').value;
    const servicioId = selectedOption.value;
    const fecha = document.getElementById('create_fecha').value;
    
    if (!especialistaId || !servicioId || !fecha) return;
    
    // Debug: mostrar parámetros en consola
    console.log('Cargando disponibilidad con:', {
        especialistaId,
        servicioId,
        fecha,
        esServicioEmergencia: esEmergencia,
        horaPreseleccionada: horaPreseleccionada
    });
    
    // Mostrar mensaje mientras carga
    const container = document.getElementById('time_slots_container');
    const loadingMsg = horaPreseleccionada 
        ? `<div class="col-span-4 text-center py-2 text-blue-600"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando horarios... (buscando ${horaPreseleccionada})</div>`
        : '<div class="col-span-4 text-center py-2 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando horarios...</div>';
    container.innerHTML = loadingMsg;
    document.getElementById('create_time_section').style.display = 'block';
    
    fetch(`<?= BASE_URL ?>/api/disponibilidad?especialista_id=${especialistaId}&servicio_id=${servicioId}&fecha=${fecha}`)
        .then(response => response.json())
        .then(data => {
            console.log('===== DEBUG DISPONIBILIDAD =====');
            console.log('Parámetros enviados:', { especialistaId, servicioId, fecha, esEmergencia });
            console.log('Respuesta completa:', data);
            console.log('Cantidad de slots:', data.slots ? data.slots.length : 0);
            if (data.slots && data.slots.length > 0) {
                console.log('Primer slot:', data.slots[0]);
                console.log('Tipos de slots:', data.slots.map(s => s.tipo || 'normal'));
            }
            console.log('================================');
            
            // Mostrar mensaje informativo si es servicio de emergencia
            if (esEmergencia && data.slots && data.slots.length > 0) {
                const hasEmergencySlots = data.slots.some(slot => slot.tipo === 'emergencia');
                if (hasEmergencySlots) {
                    console.log('✅ Mostrando horarios de EMERGENCIA');
                } else {
                    console.warn('⚠️ Servicio de emergencia pero no hay slots de emergencia disponibles');
                }
            }
            
            const container = document.getElementById('time_slots_container');
            const noSlotsMsg = document.getElementById('no_slots_message');
            container.innerHTML = '';
            
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    
                    // Estilos diferentes para slots de emergencia
                    if (slot.tipo === 'emergencia') {
                        button.className = 'px-3 py-2 border-2 border-red-500 bg-red-50 rounded-lg hover:border-red-700 hover:bg-red-100 transition text-sm font-medium time-slot-btn';
                        button.textContent = '🚨 ' + slot.hora_inicio.substring(0, 5);
                    } else {
                        button.className = 'px-3 py-2 border-2 border-gray-300 rounded-lg hover:border-primary hover:bg-blue-50 transition text-sm font-medium time-slot-btn';
                        button.textContent = slot.hora_inicio.substring(0, 5);
                    }
                    
                    button.dataset.hora = slot.hora_inicio;
                    button.dataset.tipo = slot.tipo || 'normal';
                    button.onclick = function() {
                        // Remover selección previa
                        document.querySelectorAll('.time-slot-btn').forEach(btn => {
                            btn.classList.remove('border-primary', 'bg-blue-100', 'border-red-700', 'bg-red-200');
                        });
                        // Seleccionar este
                        if (this.dataset.tipo === 'emergencia') {
                            this.classList.add('border-red-700', 'bg-red-200');
                        } else {
                            this.classList.add('border-primary', 'bg-blue-100');
                        }
                        document.getElementById('create_hora_inicio').value = this.dataset.hora;
                    };
                    container.appendChild(button);
                    
                    // Auto-seleccionar si coincide con la hora pre-seleccionada
                    if (horaPreseleccionada && slot.hora_inicio.substring(0, 5) === horaPreseleccionada) {
                        // Simular click para seleccionar automáticamente
                        setTimeout(() => {
                            button.click();
                            // Hacer scroll al botón seleccionado y destacarlo
                            button.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            
                            // Agregar animación de pulso temporal
                            button.style.animation = 'pulse 1s ease-in-out 2';
                            setTimeout(() => {
                                button.style.animation = '';
                            }, 2000);
                        }, 100);
                    }
                });
                noSlotsMsg.style.display = 'none';
                document.getElementById('create_time_section').style.display = 'block';
                
                // Si había hora pre-seleccionada pero no se encontró, avisar al usuario
                if (horaPreseleccionada) {
                    const horaEncontrada = data.slots.some(slot => slot.hora_inicio.substring(0, 5) === horaPreseleccionada);
                    if (!horaEncontrada) {
                        const container = document.getElementById('time_slots_container');
                        const warningMsg = document.createElement('div');
                        warningMsg.className = 'col-span-4 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800 flex items-center justify-center';
                        warningMsg.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>La hora seleccionada (' + horaPreseleccionada + ') no está disponible. Por favor elige otra.';
                        container.insertBefore(warningMsg, container.firstChild);
                    }
                }
            } else {
                noSlotsMsg.style.display = 'block';
                document.getElementById('create_time_section').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
        });
}

async function submitCreateReservation() {
    const nombreCliente = document.getElementById('create_nombre_cliente')?.value;
    const sucursalId = document.getElementById('create_sucursal').value;
    const especialistaId = document.getElementById('create_especialista_id').value;
    const servicioId = document.getElementById('create_servicio').value;
    const fecha = document.getElementById('create_fecha').value;
    const horaInicio = document.getElementById('create_hora_inicio').value;
    const notas = document.getElementById('create_notas').value;
    
    // Validaciones
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    if (!nombreCliente) {
        alert('Por favor ingrese el nombre del cliente');
        return;
    }
    <?php endif; ?>
    
    if (!sucursalId || !especialistaId || !servicioId || !fecha || !horaInicio) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Crear FormData
    const formData = new URLSearchParams();
    <?php if ($user['rol_id'] == ROLE_SPECIALIST): ?>
    formData.append('nombre_cliente', nombreCliente);
    <?php endif; ?>
    formData.append('sucursal_id', sucursalId);
    formData.append('especialista_id', especialistaId);
    formData.append('servicio_id', servicioId);
    formData.append('fecha_cita', fecha);
    formData.append('hora_inicio', horaInicio);
    formData.append('notas_cliente', notas);
    
    try {
        const response = await fetch('<?= BASE_URL ?>/reservaciones/nueva', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });
        
        if (response.ok) {
            alert('Reservación creada exitosamente');
            closeCreateModal();
            horaPreseleccionada = null; // Limpiar hora pre-seleccionada
            window.calendar.refetchEvents();
        } else {
            const text = await response.text();
            alert('Error al crear la reservación. Por favor intente nuevamente.');
            console.error('Error:', text);
        }
    } catch (error) {
        console.error('Error al crear reservación:', error);
        alert('Error al crear la reservación. Por favor intente nuevamente.');
    }
}
</script>
