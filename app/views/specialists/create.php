<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/especialistas') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Especialistas
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Nuevo Especialista</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/especialistas/crear') ?>">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información Personal</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Mínimo 6 caracteres">
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información Profesional</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Sucursal *</label>
                        <button type="button" onclick="openBranchModal()" 
                                class="text-xs text-primary hover:text-secondary flex items-center">
                            <i class="fas fa-plus-circle mr-1"></i>Nueva Sucursal
                        </button>
                    </div>
                    <select name="sucursal_id" id="sucursal_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <?php if (empty($branches)): ?>
                        <option value="">Primero cree una sucursal</option>
                        <?php else: ?>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                    <input type="text" name="profesion"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Ej: Médico, Abogado, Psicólogo">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" name="especialidad"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Ej: Medicina General, Derecho Civil">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia</label>
                    <input type="number" name="experiencia_anos" min="0" value="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción / Biografía</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                              placeholder="Breve descripción del especialista"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa Base</label>
                    <input type="number" name="tarifa_base" step="0.01" min="0" value="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-2 flex justify-between items-center">
                <span>Servicios que Ofrece</span>
                <button type="button" onclick="openServiceModal()" 
                        class="text-xs text-primary hover:text-secondary flex items-center">
                    <i class="fas fa-plus-circle mr-1"></i>Nuevo Servicio
                </button>
            </h3>
            
            <div class="mb-6" id="services-container">
                <?php if (empty($services)): ?>
                <p class="text-gray-500 text-center py-4">No hay servicios disponibles. Crea uno nuevo arriba.</p>
                <?php else: ?>
                    <?php 
                    $currentCategory = '';
                    foreach ($services as $service): 
                        if ($currentCategory != $service['categoria_nombre']):
                            if ($currentCategory != ''): ?>
                    </div>
                </div>
                            <?php endif;
                            $currentCategory = $service['categoria_nombre'];
                    ?>
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2 border-b pb-1"><?= e($currentCategory) ?></h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <?php endif; ?>
                
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                            <input type="checkbox" name="servicios[]" value="<?= $service['id'] ?>"
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700"><?= e($service['nombre']) ?></span>
                        </label>
                
                    <?php endforeach; ?>
                    <?php if ($currentCategory != ''): ?>
                    </div>
                </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="<?= url('/especialistas') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nueva Sucursal -->
<div id="branchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Nueva Sucursal</h3>
            <button type="button" onclick="closeBranchModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="branchForm" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="branch_nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Nombre de la sucursal">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" id="branch_direccion"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Calle, número, colonia">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" id="branch_ciudad"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Ciudad">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" id="branch_estado" value="Querétaro"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                    <input type="text" id="branch_codigo_postal"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="76000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" id="branch_telefono"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="branch_email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="sucursal@reserbot.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Apertura</label>
                    <input type="time" id="branch_horario_apertura" value="08:00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Cierre</label>
                    <input type="time" id="branch_horario_cierre" value="20:00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeBranchModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    <i class="fas fa-save mr-2"></i>Crear Sucursal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo Servicio -->
<div id="serviceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Nuevo Servicio</h3>
            <button type="button" onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="serviceForm" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select id="service_categoria_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Seleccionar categoría</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="service_nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Nombre del servicio">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="service_descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                              placeholder="Descripción del servicio"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duración (minutos)</label>
                        <input type="number" id="service_duracion_minutos" value="30" min="5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <input type="number" id="service_precio" step="0.01" value="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeServiceModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    <i class="fas fa-save mr-2"></i>Crear Servicio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Datos iniciales de servicios
let allServices = <?= json_encode($services) ?>;

// Renderizar servicios
function renderServices() {
    const container = document.getElementById('services-container');
    if (allServices.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No hay servicios disponibles. Crea uno nuevo arriba.</p>';
        return;
    }
    
    let currentCategory = '';
    let html = '';
    
    allServices.forEach((service, index) => {
        if (currentCategory != service.categoria_nombre) {
            if (currentCategory != '') {
                html += '</div></div>';
            }
            currentCategory = service.categoria_nombre;
            html += `
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2 border-b pb-1">${service.categoria_nombre}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            `;
        }
        
        html += `
            <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                <input type="checkbox" name="servicios[]" value="${service.id}"
                       class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-700">${service.nombre}</span>
            </label>
        `;
        
        if (index === allServices.length - 1) {
            html += '</div></div>';
        }
    });
    
    container.innerHTML = html;
}

// Modal de Sucursal
function openBranchModal() {
    document.getElementById('branchModal').classList.remove('hidden');
}

function closeBranchModal() {
    document.getElementById('branchModal').classList.add('hidden');
    document.getElementById('branchForm').reset();
}

document.getElementById('branchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        nombre: document.getElementById('branch_nombre').value,
        direccion: document.getElementById('branch_direccion').value,
        ciudad: document.getElementById('branch_ciudad').value,
        estado: document.getElementById('branch_estado').value,
        codigo_postal: document.getElementById('branch_codigo_postal').value,
        telefono: document.getElementById('branch_telefono').value,
        email: document.getElementById('branch_email').value,
        horario_apertura: document.getElementById('branch_horario_apertura').value,
        horario_cierre: document.getElementById('branch_horario_cierre').value
    };
    
    try {
        const response = await fetch('<?= url('/api/sucursales/crear') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Agregar nueva sucursal al select
            const select = document.getElementById('sucursal_id');
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = formData.nombre;
            option.selected = true;
            select.appendChild(option);
            
            closeBranchModal();
            
            // Mostrar mensaje de éxito
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50';
            successDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Sucursal creada correctamente';
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 3000);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al crear la sucursal');
        console.error(error);
    }
});

// Modal de Servicio
function openServiceModal() {
    document.getElementById('serviceModal').classList.remove('hidden');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.add('hidden');
    document.getElementById('serviceForm').reset();
}

document.getElementById('serviceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        categoria_id: document.getElementById('service_categoria_id').value,
        nombre: document.getElementById('service_nombre').value,
        descripcion: document.getElementById('service_descripcion').value,
        duracion_minutos: document.getElementById('service_duracion_minutos').value,
        precio: document.getElementById('service_precio').value
    };
    
    try {
        const response = await fetch('<?= url('/api/servicios/crear') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Obtener nombre de categoría
            const catSelect = document.getElementById('service_categoria_id');
            const catName = catSelect.options[catSelect.selectedIndex].text;
            
            // Agregar servicio a la lista
            allServices.push({
                id: data.id,
                nombre: formData.nombre,
                categoria_nombre: catName
            });
            
            // Re-ordenar por categoría
            allServices.sort((a, b) => {
                if (a.categoria_nombre < b.categoria_nombre) return -1;
                if (a.categoria_nombre > b.categoria_nombre) return 1;
                return 0;
            });
            
            renderServices();
            closeServiceModal();
            
            // Mostrar mensaje de éxito
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50';
            successDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Servicio creado correctamente';
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 3000);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al crear el servicio');
        console.error(error);
    }
});

// Inicializar (no necesitamos renderizar porque PHP ya lo hizo)
// Solo necesitamos asegurarnos de que los modales estén disponibles
console.log('Modales listos');
</script>
