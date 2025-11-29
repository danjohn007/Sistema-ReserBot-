<div class="bg-white rounded-xl shadow-sm p-6">
    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
        <?php if (!empty($branches) && count($branches) > 1): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
            <select id="filter_sucursal" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" onchange="filterCalendar()">
                <option value="">Todas las sucursales</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($specialists)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Especialista</label>
            <select id="filter_especialista" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" onchange="filterCalendar()">
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
    </div>
    
    <!-- Calendar -->
    <div id="calendar"></div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" id="modal-title">Detalles de la Cita</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="modal-content">
            <!-- Content loaded dynamically -->
        </div>
        <div class="p-4 bg-gray-50 border-t flex justify-end">
            <a href="#" id="modal-view-link" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
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
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
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
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            showEventModal(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.title = info.event.title;
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        nowIndicator: true,
        editable: false,
        selectable: false,
        dayMaxEvents: true
    });
    
    calendar.render();
});

function filterCalendar() {
    window.calendar.refetchEvents();
}

function showEventModal(event) {
    const modal = document.getElementById('eventModal');
    const props = event.extendedProps;
    
    document.getElementById('modal-title').textContent = event.title;
    document.getElementById('modal-content').innerHTML = `
        <div class="space-y-3">
            <p><strong>Código:</strong> ${props.codigo}</p>
            <p><strong>Cliente:</strong> ${props.cliente}</p>
            <p><strong>Especialista:</strong> ${props.especialista}</p>
            <p><strong>Servicio:</strong> ${props.servicio}</p>
            <p><strong>Precio:</strong> ${props.precio}</p>
            <p><strong>Estado:</strong> <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(props.estado)}">${props.estado}</span></p>
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
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'confirmada': 'bg-green-100 text-green-800',
        'en_progreso': 'bg-blue-100 text-blue-800',
        'completada': 'bg-gray-100 text-gray-800',
        'cancelada': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Close modal on overlay click
document.getElementById('eventModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
