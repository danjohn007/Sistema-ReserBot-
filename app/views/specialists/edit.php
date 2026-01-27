<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/especialistas') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Especialistas
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Especialista</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/especialistas/editar?id=' . $specialist['id']) ?>">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Informaci&oacute;n Personal</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= e($specialist['nombre']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" required value="<?= e($specialist['apellidos']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required value="<?= e($specialist['email']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= e($specialist['telefono']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Informaci&oacute;n Profesional</h3>
            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Sucursales * <span class="text-xs text-gray-500">(Selecciona al menos una)</span></label>
                    <button type="button" onclick="openBranchModal()" 
                            class="text-sm px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-plus mr-1"></i>Nueva Sucursal
                    </button>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Panel de búsqueda y selección -->
                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Buscar y seleccionar sucursal</label>
                        
                        <input type="text" id="searchBranches" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Buscar sucursal...">
                        
                        <div id="branchList" class="space-y-2 max-h-64 overflow-y-auto">
                            <?php foreach ($branches as $branch): ?>
                            <div class="branch-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-primary transition"
                                 data-branch-id="<?= $branch['id'] ?>"
                                 data-branch-name="<?= e($branch['nombre']) ?>">
                                <span class="text-sm text-gray-700"><?= e($branch['nombre']) ?></span>
                                <button type="button" 
                                        onclick="addBranch(<?= $branch['id'] ?>, '<?= e($branch['nombre']) ?>')"
                                        class="add-branch-btn w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Panel de sucursales seleccionadas -->
                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Sucursales seleccionadas</label>
                        <div id="selectedBranches" class="space-y-2 min-h-[200px]">
                            <p class="text-sm text-gray-400 text-center py-8" id="emptyMessage">
                                No hay sucursales seleccionadas
                            </p>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="validation_required" id="validation_required" required>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                    <input type="text" name="profesion" value="<?= e($specialist['profesion']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" name="especialidad" value="<?= e($specialist['especialidad']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia</label>
                    <input type="number" name="experiencia_anos" min="0" value="<?= $specialist['experiencia_anos'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripci&oacute;n</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"><?= e($specialist['descripcion']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa Base</label>
                    <input type="number" name="tarifa_base" step="0.01" min="0" value="<?= $specialist['tarifa_base'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" <?= $specialist['activo'] ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Especialista activo</span>
                    </label>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-3 flex justify-between items-center">
                <span>Servicios que Ofrece</span>
                <button type="button" onclick="openServiceModal()" 
                        class="text-sm px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-plus mr-1"></i>Nuevo Servicio
                </button>
            </h3>
            
            <div class="mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Panel de búsqueda y selección -->
                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Buscar y seleccionar servicio</label>
                        
                        <input type="text" id="searchServices" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Buscar servicio...">
                        
                        <div id="serviceList" class="space-y-2 max-h-80 overflow-y-auto">
                            <?php 
                            $currentCategory = '';
                            foreach ($services as $service): 
                                if ($currentCategory != $service['categoria_nombre']):
                                    if ($currentCategory != ''): ?>
                                    </div>
                                    <?php endif;
                                    $currentCategory = $service['categoria_nombre'];
                            ?>
                            <div class="category-group mb-3">
                                <h4 class="font-medium text-xs text-gray-500 uppercase mb-1 px-2"><?= e($currentCategory) ?></h4>
                                <?php endif; ?>
                                
                                <div class="service-item flex items-center justify-between p-2.5 bg-white rounded-lg border border-gray-200 hover:border-primary transition mb-1.5"
                                     data-service-id="<?= $service['id'] ?>"
                                     data-service-name="<?= e($service['nombre']) ?>"
                                     data-service-category="<?= e($service['categoria_nombre']) ?>">
                                    <span class="text-sm text-gray-700"><?= e($service['nombre']) ?></span>
                                    <button type="button" 
                                            onclick="addService(<?= $service['id'] ?>, '<?= addslashes(e($service['nombre'])) ?>', '<?= addslashes(e($service['categoria_nombre'])) ?>')"
                                            class="add-service-btn w-7 h-7 flex items-center justify-center bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                
                            <?php endforeach; ?>
                            <?php if ($currentCategory != ''): ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Panel de servicios seleccionados -->
                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Servicios seleccionados</label>
                        <div id="selectedServices" class="space-y-2 min-h-[200px]">
                            <p class="text-sm text-gray-400 text-center py-8" id="emptyServicesMessage">
                                No hay servicios seleccionados
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="<?= url('/especialistas') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Apertura <span class="text-xs text-gray-500">(Opcional)</span></label>
                    <input type="time" id="branch_horario_apertura"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Opcional">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Cierre <span class="text-xs text-gray-500">(Opcional)</span></label>
                    <input type="time" id="branch_horario_cierre"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Opcional">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categor&iacute;a *</label>
                    <select id="service_categoria_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Seleccionar categor&iacute;a</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripci&oacute;n</label>
                    <textarea id="service_descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                              placeholder="Descripci&oacute;n del servicio"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duración (min) *</label>
                        <input type="number" id="service_duracion_minutos" required min="5" step="5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="30">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                        <input type="number" id="service_precio" required step="0.01" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="0.00">
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
            // Agregar nueva sucursal a la lista de búsqueda
            const branchList = document.getElementById('branchList');
            if (branchList) {
                const div = document.createElement('div');
                div.className = 'branch-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-primary transition';
                div.setAttribute('data-branch-id', data.id);
                div.setAttribute('data-branch-name', formData.nombre);
                div.innerHTML = `
                    <span class="text-sm text-gray-700">${formData.nombre}</span>
                    <button type="button" 
                            onclick="addBranch(${data.id}, '${formData.nombre}')"
                            class="add-branch-btn w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                        <i class="fas fa-plus"></i>
                    </button>
                `;
                branchList.insertBefore(div, branchList.firstChild);
                
                // Auto-seleccionar la sucursal recién creada
                addBranch(data.id, formData.nombre);
            }
            
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
            
            // Agregar servicio a la lista de búsqueda
            const serviceList = document.getElementById('serviceList');
            if (serviceList) {
                // Buscar si existe un grupo de categoría
                let categoryGroup = Array.from(serviceList.querySelectorAll('.category-group h4')).find(h => h.textContent === catName)?.parentElement;
                
                if (!categoryGroup) {
                    // Crear nuevo grupo de categoría
                    categoryGroup = document.createElement('div');
                    categoryGroup.className = 'category-group mb-3';
                    categoryGroup.innerHTML = `<h4 class="font-medium text-xs text-gray-500 uppercase mb-1 px-2">${catName}</h4>`;
                    serviceList.appendChild(categoryGroup);
                }
                
                // Crear elemento de servicio
                const div = document.createElement('div');
                div.className = 'service-item flex items-center justify-between p-2.5 bg-white rounded-lg border border-gray-200 hover:border-primary transition mb-1.5';
                div.setAttribute('data-service-id', data.id);
                div.setAttribute('data-service-name', formData.nombre);
                div.setAttribute('data-service-category', catName);
                div.innerHTML = `
                    <span class="text-sm text-gray-700">${formData.nombre}</span>
                    <button type="button" 
                            onclick="addService(${data.id}, '${formData.nombre.replace(/'/g, "\\'")}', '${catName.replace(/'/g, "\\'")}')"
                            class="add-service-btn w-7 h-7 flex items-center justify-center bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                `;
                categoryGroup.appendChild(div);
                
                // Auto-seleccionar el servicio recién creado
                addService(data.id, formData.nombre, catName);
            }
            
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

// ===== Gestión de Sucursales =====
const selectedBranchesSet = new Set();

// Función para agregar sucursal
function addBranch(branchId, branchName) {
    if (selectedBranchesSet.has(branchId)) {
        return; // Ya está agregada
    }
    
    selectedBranchesSet.add(branchId);
    
    // Ocultar mensaje vacío
    const emptyMessage = document.getElementById('emptyMessage');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
    
    // Crear elemento en sucursales seleccionadas
    const container = document.getElementById('selectedBranches');
    const div = document.createElement('div');
    div.id = `selected-branch-${branchId}`;
    div.className = 'flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg';
    div.innerHTML = `
        <span class="text-sm text-gray-700 font-medium">${branchName}</span>
        <button type="button" 
                onclick="removeBranch(${branchId})"
                class="w-6 h-6 flex items-center justify-center text-red-500 hover:text-red-700 transition">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
    
    // Agregar input hidden para el formulario
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'sucursales[]';
    input.value = branchId;
    input.id = `branch-input-${branchId}`;
    container.appendChild(input);
    
    // Ocultar botón de agregar en la lista
    const branchItem = document.querySelector(`.branch-item[data-branch-id="${branchId}"]`);
    if (branchItem) {
        const addBtn = branchItem.querySelector('.add-branch-btn');
        if (addBtn) {
            addBtn.style.display = 'none';
        }
    }
    
    updateValidation();
}

// Función para eliminar sucursal
function removeBranch(branchId) {
    selectedBranchesSet.delete(branchId);
    
    // Eliminar elemento visual
    const element = document.getElementById(`selected-branch-${branchId}`);
    if (element) {
        element.remove();
    }
    
    // Eliminar input hidden
    const input = document.getElementById(`branch-input-${branchId}`);
    if (input) {
        input.remove();
    }
    
    // Mostrar botón de agregar en la lista
    const branchItem = document.querySelector(`.branch-item[data-branch-id="${branchId}"]`);
    if (branchItem) {
        const addBtn = branchItem.querySelector('.add-branch-btn');
        if (addBtn) {
            addBtn.style.display = 'flex';
        }
    }
    
    // Mostrar mensaje vacío si no hay seleccionadas
    if (selectedBranchesSet.size === 0) {
        const emptyMessage = document.getElementById('emptyMessage');
        if (emptyMessage) {
            emptyMessage.style.display = 'block';
        }
    }
    
    updateValidation();
}

// Actualizar validación del formulario
function updateValidation() {
    const validationInput = document.getElementById('validation_required');
    if (validationInput) {
        if (selectedBranchesSet.size > 0) {
            validationInput.value = '1';
        } else {
            validationInput.value = '';
        }
    }
}

// Función de búsqueda de sucursales
document.getElementById('searchBranches')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const branchItems = document.querySelectorAll('.branch-item');
    
    branchItems.forEach(item => {
        const branchName = item.getAttribute('data-branch-name').toLowerCase();
        if (branchName.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// ===== Gestión de Servicios =====
const selectedServicesSet = new Set();

// Función para agregar servicio
function addService(serviceId, serviceName, categoryName) {
    if (selectedServicesSet.has(serviceId)) {
        return; // Ya está agregado
    }
    
    selectedServicesSet.add(serviceId);
    
    // Ocultar mensaje vacío
    const emptyMessage = document.getElementById('emptyServicesMessage');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
    
    // Crear elemento en servicios seleccionados
    const container = document.getElementById('selectedServices');
    const div = document.createElement('div');
    div.id = `selected-service-${serviceId}`;
    div.className = 'flex items-center justify-between p-2.5 bg-blue-50 border border-blue-200 rounded-lg';
    div.innerHTML = `
        <div class="flex-1">
            <span class="text-sm text-gray-700 font-medium">${serviceName}</span>
            <span class="text-xs text-gray-500 block">${categoryName}</span>
        </div>
        <button type="button" 
                onclick="removeService(${serviceId})"
                class="w-6 h-6 flex items-center justify-center text-red-500 hover:text-red-700 transition ml-2">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
    
    // Agregar input hidden para el formulario
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'servicios[]';
    input.value = serviceId;
    input.id = `service-input-${serviceId}`;
    container.appendChild(input);
    
    // Ocultar botón de agregar en la lista
    const serviceItem = document.querySelector(`.service-item[data-service-id="${serviceId}"]`);
    if (serviceItem) {
        const addBtn = serviceItem.querySelector('.add-service-btn');
        if (addBtn) {
            addBtn.style.display = 'none';
        }
    }
}

// Función para eliminar servicio
function removeService(serviceId) {
    selectedServicesSet.delete(serviceId);
    
    // Eliminar elemento visual
    const element = document.getElementById(`selected-service-${serviceId}`);
    if (element) {
        element.remove();
    }
    
    // Eliminar input hidden
    const input = document.getElementById(`service-input-${serviceId}`);
    if (input) {
        input.remove();
    }
    
    // Mostrar botón de agregar en la lista
    const serviceItem = document.querySelector(`.service-item[data-service-id="${serviceId}"]`);
    if (serviceItem) {
        const addBtn = serviceItem.querySelector('.add-service-btn');
        if (addBtn) {
            addBtn.style.display = 'flex';
        }
    }
    
    // Mostrar mensaje vacío si no hay seleccionados
    if (selectedServicesSet.size === 0) {
        const emptyMessage = document.getElementById('emptyServicesMessage');
        if (emptyMessage) {
            emptyMessage.style.display = 'block';
        }
    }
}

// Función de búsqueda de servicios
document.getElementById('searchServices')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const serviceItems = document.querySelectorAll('.service-item');
    const categoryGroups = document.querySelectorAll('.category-group');
    
    // Primero ocultar todos los items
    serviceItems.forEach(item => {
        const serviceName = item.getAttribute('data-service-name').toLowerCase();
        const categoryName = item.getAttribute('data-service-category').toLowerCase();
        
        if (serviceName.includes(searchTerm) || categoryName.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Ocultar categorías sin servicios visibles
    categoryGroups.forEach(group => {
        const visibleItems = group.querySelectorAll('.service-item[style="display: flex;"], .service-item:not([style*="display: none"])');
        if (visibleItems.length === 0) {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    });
});

// Inicializar con las sucursales y servicios actuales del especialista
document.addEventListener('DOMContentLoaded', function() {
    // Cargar sucursales actuales
    <?php if (!empty($specialistBranches)): ?>
        <?php foreach ($specialistBranches as $branch): ?>
            addBranch(<?= $branch['id'] ?>, '<?= addslashes(e($branch['nombre'])) ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    
    // Cargar servicios actuales
    <?php if (!empty($currentServiceIds)): ?>
        <?php foreach ($services as $service): ?>
            <?php if (in_array($service['id'], $currentServiceIds)): ?>
                addService(<?= $service['id'] ?>, '<?= addslashes(e($service['nombre'])) ?>', '<?= addslashes(e($service['categoria_nombre'])) ?>');
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    updateValidation();
});
</script>