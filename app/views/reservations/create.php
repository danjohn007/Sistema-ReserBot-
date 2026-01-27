<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/reservaciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Reservaciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Nueva Reservaci&oacute;n</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/reservaciones/nueva') ?>" id="reservationForm">
            <!-- Step 0: Client Selection (only for admins/receptionists) -->
            <?php if (!empty($clients) && count($clients) > 0): ?>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                <select name="cliente_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- Seleccione un cliente --</option>
                    <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id'] ?>">
                        <?= e($client['nombre'] . ' ' . $client['apellidos']) ?> 
                        <?php if ($client['email']): ?>
                        - <?= e($client['email']) ?>
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Step 1: Branch -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">1. Seleccione una Sucursal *</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($branches as $branch): ?>
                    <label class="relative">
                        <input type="radio" name="sucursal_id" value="<?= $branch['id'] ?>" 
                               class="peer sr-only" <?= $selectedBranch == $branch['id'] ? 'checked' : '' ?>
                               onchange="loadSpecialists(this.value)">
                        <div class="p-4 border-2 rounded-lg cursor-pointer peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                            <h4 class="font-semibold text-gray-800"><?= e($branch['nombre']) ?></h4>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Step 2: Specialist -->
            <div class="mb-6" id="specialistSection" style="<?= empty($specialists) ? 'display:none' : '' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">2. Seleccione un Especialista *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="specialistList">
                    <?php foreach ($specialists as $spec): ?>
                    <label class="relative">
                        <input type="radio" name="especialista_id" value="<?= $spec['id'] ?>" 
                               class="peer sr-only" <?= $selectedSpecialist == $spec['id'] ? 'checked' : '' ?>
                               onchange="loadServices(this.value)">
                        <div class="p-4 border-2 rounded-lg cursor-pointer peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                            <h4 class="font-semibold text-gray-800"><?= e($spec['nombre'] . ' ' . $spec['apellidos']) ?></h4>
                            <p class="text-sm text-gray-500"><?= e($spec['profesion']) ?></p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Step 3: Service -->
            <div class="mb-6" id="serviceSection" style="<?= empty($services) ? 'display:none' : '' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">3. Seleccione un Servicio *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="serviceList">
                    <?php foreach ($services as $serv): ?>
                    <label class="relative">
                        <input type="radio" name="servicio_id" value="<?= $serv['id'] ?>" 
                               class="peer sr-only" <?= $selectedService == $serv['id'] ? 'checked' : '' ?>
                               onchange="document.getElementById('dateSection').style.display='block'">
                        <div class="p-4 border-2 rounded-lg cursor-pointer peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold text-gray-800"><?= e($serv['nombre']) ?></h4>
                                    <p class="text-sm text-gray-500"><?= $serv['duracion_minutos'] ?> minutos</p>
                                </div>
                                <span class="text-primary font-bold"><?= formatMoney($serv['precio']) ?></span>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Step 4: Date -->
            <div class="mb-6" id="dateSection" style="<?= empty($selectedService) ? 'display:none' : '' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">4. Seleccione Fecha *</label>
                <input type="date" name="fecha_cita" id="fecha_cita" 
                       min="<?= date('Y-m-d') ?>" value="<?= $selectedDate ?? '' ?>"
                       class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                       onchange="loadAvailability()">
            </div>
            
            <!-- Step 5: Time -->
            <div class="mb-6" id="timeSection" style="<?= empty($availableSlots) ? 'display:none' : '' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">5. Seleccione Horario *</label>
                <div class="grid grid-cols-3 md:grid-cols-6 gap-2" id="timeList">
                    <?php foreach ($availableSlots as $slot): ?>
                    <label class="relative">
                        <input type="radio" name="hora_inicio" value="<?= $slot['hora_inicio'] ?>" class="peer sr-only">
                        <div class="p-2 border-2 rounded-lg cursor-pointer text-center text-sm peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                            <?= formatTime($slot['hora_inicio']) ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas adicionales</label>
                <textarea name="notas_cliente" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                          placeholder="Informaci&oacute;n adicional para el especialista..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="<?= url('/reservaciones') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-calendar-check mr-2"></i>Confirmar Reservaci&oacute;n
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const baseUrl = '<?= BASE_URL ?>';

async function loadSpecialists(branchId) {
    const response = await fetch(`${baseUrl}/api/especialistas?sucursal_id=${branchId}`);
    const data = await response.json();
    
    const container = document.getElementById('specialistList');
    container.innerHTML = '';
    
    data.specialists.forEach(spec => {
        container.innerHTML += `
            <label class="relative">
                <input type="radio" name="especialista_id" value="${spec.id}" 
                       class="peer sr-only" onchange="loadServices(this.value)">
                <div class="p-4 border-2 rounded-lg cursor-pointer peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                    <h4 class="font-semibold text-gray-800">${spec.nombre} ${spec.apellidos}</h4>
                    <p class="text-sm text-gray-500">${spec.profesion || ''}</p>
                </div>
            </label>
        `;
    });
    
    document.getElementById('specialistSection').style.display = 'block';
    document.getElementById('serviceSection').style.display = 'none';
    document.getElementById('dateSection').style.display = 'none';
    document.getElementById('timeSection').style.display = 'none';
}

async function loadServices(specialistId) {
    const response = await fetch(`${baseUrl}/api/servicios?especialista_id=${specialistId}`);
    const data = await response.json();
    
    const container = document.getElementById('serviceList');
    container.innerHTML = '';
    
    data.services.forEach(serv => {
        const price = parseFloat(serv.precio).toLocaleString('es-MX', {style: 'currency', currency: 'MXN'});
        container.innerHTML += `
            <label class="relative">
                <input type="radio" name="servicio_id" value="${serv.id}" 
                       class="peer sr-only" onchange="document.getElementById('dateSection').style.display='block'">
                <div class="p-4 border-2 rounded-lg cursor-pointer peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">${serv.nombre}</h4>
                            <p class="text-sm text-gray-500">${serv.duracion_minutos} minutos</p>
                        </div>
                        <span class="text-blue-600 font-bold">${price}</span>
                    </div>
                </div>
            </label>
        `;
    });
    
    document.getElementById('serviceSection').style.display = 'block';
    document.getElementById('dateSection').style.display = 'none';
    document.getElementById('timeSection').style.display = 'none';
}

async function loadAvailability() {
    const specialistId = document.querySelector('input[name="especialista_id"]:checked')?.value;
    const serviceId = document.querySelector('input[name="servicio_id"]:checked')?.value;
    const date = document.getElementById('fecha_cita').value;
    
    if (!specialistId || !serviceId || !date) return;
    
    const response = await fetch(`${baseUrl}/api/disponibilidad?especialista_id=${specialistId}&servicio_id=${serviceId}&fecha=${date}`);
    const data = await response.json();
    
    const container = document.getElementById('timeList');
    container.innerHTML = '';
    
    if (data.slots.length === 0) {
        container.innerHTML = '<p class="col-span-6 text-center text-gray-500 py-4">No hay horarios disponibles para esta fecha</p>';
    } else {
        data.slots.forEach(slot => {
            const time = slot.hora_inicio.substring(0, 5);
            container.innerHTML += `
                <label class="relative">
                    <input type="radio" name="hora_inicio" value="${slot.hora_inicio}" class="peer sr-only">
                    <div class="p-2 border-2 rounded-lg cursor-pointer text-center text-sm peer-checked:border-primary peer-checked:bg-blue-50 hover:border-gray-400">
                        ${time}
                    </div>
                </label>
            `;
        });
    }
    
    document.getElementById('timeSection').style.display = 'block';
}
</script>
