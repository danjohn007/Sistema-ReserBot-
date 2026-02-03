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
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #3B82F6; color: white;">
                        <i class="fas fa-play mr-1"></i>En Progreso
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #6B7280; color: white;">
                        <i class="fas fa-check-double mr-1"></i>Completada
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
        <div class="p-6 bg-gray-50 border-t rounded-b-2xl flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Haz clic en "Ver Detalle" para m&aacute;s informaci&oacute;n
            </div>
            <a href="#" id="modal-view-link" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary transition shadow-md hover:shadow-lg">
                <i class="fas fa-eye mr-2"></i>Ver Detalle
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    window.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
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
                .then(data => successCallback(data))
                .catch(error => {
                    console.error('Error loading events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showEventModal(info.event);
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
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        nowIndicator: true,
        editable: false,
        selectable: false,
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
        },
        eventClassNames: function(arg) {
            return ['rounded-lg', 'border-l-4'];
        }
    });
    
    calendar.render();
});

function filterCalendar() {
    window.calendar.refetchEvents();
}

function showEventModal(event) {
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

// Close modal on overlay click
document.getElementById('eventModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
