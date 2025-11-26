<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Días Feriados</h2>
            <button onclick="document.getElementById('addHolidayForm').classList.toggle('hidden')" 
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition">
                <i class="fas fa-plus mr-2"></i>Agregar Feriado
            </button>
        </div>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <!-- Add Holiday Form -->
        <div id="addHolidayForm" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
            <form method="POST" action="<?= url('/configuraciones/feriados') ?>">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="nombre" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               placeholder="Ej: Día de la Independencia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                        <select name="sucursal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Todas las sucursales</option>
                            <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="recurrente" value="1" class="rounded border-gray-300 text-primary">
                        <span class="ml-2 text-sm text-gray-700">Se repite cada año</span>
                    </label>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-plus mr-2"></i>Agregar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Holidays List -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sucursal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recurrente</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($holidays as $holiday): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"><?= formatDate($holiday['fecha']) ?></td>
                        <td class="px-4 py-3"><?= e($holiday['nombre']) ?></td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= $holiday['sucursal_nombre'] ?? 'Todas' ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($holiday['recurrente']): ?>
                            <span class="text-green-600"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                            <span class="text-gray-400"><i class="fas fa-times"></i></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="<?= url('/configuraciones/feriados') ?>" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="holiday_id" value="<?= $holiday['id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('¿Eliminar este día feriado?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($holidays)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>No hay días feriados configurados</p>
        </div>
        <?php endif; ?>
    </div>
</div>
