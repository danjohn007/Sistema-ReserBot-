<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Servicios</h1>
            <p class="text-gray-600 mt-2">Administra los precios y duraci&oacute;n de tus servicios</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openAddServiceModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Agregar Existente
            </button>
            <button onclick="openCreateServiceModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                Crear Nuevo
            </button>
        </div>
    </div>

    <?php if (count($allSpecialists) > 1): ?>
    <!-- Tabs para múltiples sucursales -->
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <div class="mb-6 border-b border-gray-200 overflow-x-auto hide-scrollbar" style="max-width: 100%;">
        <nav class="-mb-px flex space-x-2" aria-label="Tabs" style="min-width: min-content;">
            <?php foreach ($allSpecialists as $spec): ?>
            <a href="<?= url('/especialistas/mis-servicios?specialist_id=' . $spec['id']) ?>" 
               class="<?= $spec['id'] == $currentSpecialistId ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex-shrink-0">
                <i class="fas fa-building mr-1 text-xs"></i><?= e($spec['sucursal_nombre']) ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>
    
    <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
        <p class="text-sm text-blue-700">
            <i class="fas fa-info-circle mr-2"></i>
            Est&aacute;s editando los servicios para: <strong><?= e($specialist['sucursal_nombre']) ?></strong>
        </p>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?php 
            echo htmlspecialchars($_SESSION['flash']['message']); 
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($services)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No tienes servicios asignados. Contacta al administrador para que te asigne servicios.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" action="<?= url('/especialistas/mis-servicios') ?>" class="space-y-6">
            <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Servicio
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Categor&iacute;a
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Base
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tu Precio
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duraci&oacute;n Base
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tu Duraci&oacute;n
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Emergencia
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            $currentCategory = null;
                            $categoryIndex = 0;
                            foreach ($services as $service): 
                                // Mostrar encabezado de categoría si cambió
                                if ($currentCategory !== $service['categoria_nombre']):
                                    $currentCategory = $service['categoria_nombre'];
                                    $categoryIndex++;
                                    $categorySlug = 'cat-' . $categoryIndex;
                            ?>
                                <tr class="bg-gray-100 cursor-pointer hover:bg-gray-200 transition-colors" onclick="toggleCategory('<?= $categorySlug ?>')">
                                    <td colspan="8" class="px-6 py-2 text-sm font-semibold text-gray-700">
                                        <div class="flex items-center">
                                            <i id="icon-<?= $categorySlug ?>" class="fas fa-chevron-down mr-2 transition-transform"></i>
                                            <?php echo htmlspecialchars($currentCategory ?: 'Sin categor&iacute;a'); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr class="category-<?= $categorySlug ?>" data-category="<?= $categorySlug ?>">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($service['nombre']); ?>
                                    </div>
                                    <?php if ($service['descripcion']): ?>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars(substr($service['descripcion'], 0, 60)); ?>
                                            <?php if (strlen($service['descripcion']) > 60) echo '...'; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($service['categoria_nombre'] ?: 'Sin categor&iacute;a'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    $<?php echo number_format($service['precio_default'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-gray-700 mr-2">$</span>
                                        <input 
                                            type="number" 
                                            name="servicios[<?php echo $service['id']; ?>][precio]" 
                                            value="<?php echo $service['precio_personalizado'] ?? ''; ?>"
                                            placeholder="<?php echo number_format($service['precio_default'], 2); ?>"
                                            step="0.01"
                                            min="0"
                                            class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>
                                    <?php if ($service['precio_personalizado']): ?>
                                        <div class="text-xs text-green-600 mt-1">
                                            Precio personalizado activo
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo $service['duracion_default']; ?> min
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <input 
                                            type="number" 
                                            name="servicios[<?php echo $service['id']; ?>][duracion]" 
                                            value="<?php echo $service['duracion_personalizada'] ?? ''; ?>"
                                            placeholder="<?php echo $service['duracion_default']; ?>"
                                            min="5"
                                            max="480"
                                            step="5"
                                            class="w-20 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                        <span class="text-gray-700 ml-2">min</span>
                                    </div>
                                    <?php if ($service['duracion_personalizada']): ?>
                                        <div class="text-xs text-green-600 mt-1">
                                            Duraci&oacute;n personalizada activa
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="servicios[<?php echo $service['id']; ?>][es_emergencia]" 
                                                value="1"
                                                <?php echo ($service['es_emergencia'] ?? 0) ? 'checked' : ''; ?>
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                        </label>
                                    </div>
                                    <div class="text-xs text-center mt-1 <?php echo ($service['es_emergencia'] ?? 0) ? 'text-green-600' : 'text-gray-500'; ?>">
                                        <?php echo ($service['es_emergencia'] ?? 0) ? 'Activo' : 'Inactivo'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="servicios[<?php echo $service['id']; ?>][activo]" 
                                                value="1"
                                                <?php echo ($service['activo'] ?? 1) ? 'checked' : ''; ?>
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                        </label>
                                    </div>
                                    <div class="text-xs text-center mt-1 <?php echo ($service['activo'] ?? 1) ? 'text-green-600' : 'text-gray-500'; ?>">
                                        <?php echo ($service['activo'] ?? 1) ? 'Activo' : 'Inactivo'; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Nota:</strong> Deja el campo vac&iacute;o para usar el precio o duraci&oacute;n base del servicio. 
                            Los valores personalizados solo aplican para tus reservas. <strong>Emergencia:</strong> Agrega horas FUERA del horario normal para casos urgentes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/dashboard" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                >
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- Modal: Agregar Servicio Existente -->
<div id="addServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Agregar Servicio Existente</h3>
                <button onclick="closeAddServiceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="<?= url('/especialistas/asignar-servicio') ?>">
            <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
            <div class="p-6">
                <?php if (empty($availableServices)): ?>
                    <p class="text-gray-600">Ya tienes todos los servicios disponibles asignados.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php 
                        $currentCat = null;
                        foreach ($availableServices as $service): 
                            if ($currentCat !== $service['categoria_nombre']):
                                $currentCat = $service['categoria_nombre'];
                        ?>
                            <div class="font-semibold text-gray-700 mt-4 mb-2">
                                <?php if ($currentCat): ?>
                                    <?= e($currentCat) ?>
                                <?php else: ?>
                                    Sin categor&iacute;a
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="servicio_id" value="<?= $service['id'] ?>" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900"><?= e($service['nombre']) ?></div>
                                <div class="text-sm text-gray-600">
                                    $<?= number_format($service['precio'], 2) ?> &bull; <?= $service['duracion_minutos'] ?> min
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($availableServices)): ?>
            <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeAddServiceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Agregar Servicio
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Modal: Crear Servicio Nuevo -->
<div id="createServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Crear Servicio Nuevo</h3>
                <button onclick="closeCreateServiceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="<?= url('/especialistas/crear-servicio') ?>">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Servicio *</label>
                    <input type="text" name="nombre" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej: Consulta Especializada">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categor&iacute;a *</label>
                    <select name="categoria_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecciona una categor&iacute;a</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (strtolower($cat['nombre']) == 'otro' || strtolower($cat['nombre']) == 'otros') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripci&oacute;n</label>
                    <textarea name="descripcion" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Descripci&oacute;n del servicio"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio *</label>
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">$</span>
                            <input type="number" name="precio" required step="0.01" min="0"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duraci&oacute;n (min) *</label>
                        <input type="number" name="duracion" required min="5" max="480" step="5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="30">
                    </div>
                </div>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 p-3">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Este servicio se crear&aacute; y se asignar&aacute; autom&aacute;ticamente a tu perfil.
                    </p>
                </div>
            </div>
            <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeCreateServiceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus-circle mr-2"></i>Crear Servicio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddServiceModal() {
    document.getElementById('addServiceModal').classList.remove('hidden');
}

function closeAddServiceModal() {
    document.getElementById('addServiceModal').classList.add('hidden');
}

function openCreateServiceModal() {
    document.getElementById('createServiceModal').classList.remove('hidden');
}

function closeCreateServiceModal() {
    document.getElementById('createServiceModal').classList.add('hidden');
}

// Función para colapsar/expandir categorías
function toggleCategory(categorySlug) {
    const rows = document.querySelectorAll(`.category-${categorySlug}`);
    const icon = document.getElementById(`icon-${categorySlug}`);
    
    rows.forEach(row => {
        if (row.style.display === 'none') {
            row.style.display = '';
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-down');
        } else {
            row.style.display = 'none';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        }
    });
}

// Cerrar modales al hacer clic fuera
document.getElementById('addServiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAddServiceModal();
});

document.getElementById('createServiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateServiceModal();
});
</script>
