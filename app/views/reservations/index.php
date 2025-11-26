<div class="flex flex-wrap justify-between items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Reservaciones</h2>
        <p class="text-gray-500 text-sm">Gestiona las citas y reservaciones</p>
    </div>
    <a href="<?= url('/reservaciones/nueva') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nueva Reservación
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todos</option>
                <option value="pendiente" <?= ($currentFilters['estado'] ?? '') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="confirmada" <?= ($currentFilters['estado'] ?? '') == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="completada" <?= ($currentFilters['estado'] ?? '') == 'completada' ? 'selected' : '' ?>>Completada</option>
                <option value="cancelada" <?= ($currentFilters['estado'] ?? '') == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" name="fecha" value="<?= $currentFilters['fecha'] ?? '' ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <?php if (!empty($branches)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
            <select name="sucursal_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todas</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>" <?= ($currentFilters['sucursal_id'] ?? '') == $branch['id'] ? 'selected' : '' ?>>
                    <?= e($branch['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        
        <a href="<?= url('/reservaciones') ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            Limpiar filtros
        </a>
    </form>
</div>

<!-- Reservations Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialista</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($reservations as $res): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-primary">
                        <?= e($res['codigo']) ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800"><?= e($res['cliente_nombre'] . ' ' . $res['cliente_apellidos']) ?></p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <?= e($res['servicio_nombre']) ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <?= e($res['especialista_nombre'] . ' ' . $res['especialista_apellidos']) ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-800"><?= formatDate($res['fecha_cita']) ?></p>
                        <p class="text-sm text-gray-500"><?= formatTime($res['hora_inicio']) ?> - <?= formatTime($res['hora_fin']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full <?= getStatusBadgeClass($res['estado']) ?>">
                            <?= getStatusText($res['estado']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="<?= url('/reservaciones/ver?id=' . $res['id']) ?>" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($res['estado'] == 'pendiente'): ?>
                        <a href="<?= url('/reservaciones/confirmar?id=' . $res['id']) ?>" 
                           class="text-green-600 hover:text-green-800"
                           onclick="return confirm('¿Confirmar esta cita?')">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($res['estado'] != 'cancelada' && $res['estado'] != 'completada'): ?>
                        <a href="<?= url('/reservaciones/cancelar?id=' . $res['id']) ?>" 
                           class="text-red-600 hover:text-red-800"
                           onclick="return confirm('¿Cancelar esta cita?')">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (empty($reservations)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center mt-6">
    <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay reservaciones</h3>
    <p class="text-gray-500 mt-2">No se encontraron reservaciones con los filtros seleccionados</p>
</div>
<?php endif; ?>
