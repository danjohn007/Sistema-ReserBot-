<div class="flex flex-wrap justify-between items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Reservaciones</h2>
        <p class="text-gray-500 text-sm">Gestiona las citas y reservaciones</p>
    </div>
    <a href="<?= url('/reservaciones/nueva') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nueva Reservaci&oacute;n
    </a>
</div>

<?php if (!empty($allSpecialists) && count($allSpecialists) > 1): ?>
<!-- Tabs para múltiples sucursales (especialistas) -->
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
<div class="mb-6 border-b border-gray-200 overflow-x-auto hide-scrollbar bg-white rounded-xl shadow-sm" style="max-width: 100%;">
    <nav class="-mb-px flex space-x-2 p-4" aria-label="Tabs" style="min-width: min-content;">
        <?php foreach ($allSpecialists as $spec): ?>
        <a href="<?= url('/reservaciones?specialist_id=' . $spec['id']) ?>" 
           class="<?= $spec['id'] == $currentSpecialistId ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex-shrink-0">
            <i class="fas fa-building mr-1 text-xs"></i><?= e($spec['sucursal_nombre']) ?>
        </a>
        <?php endforeach; ?>
    </nav>
</div>

<div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
    <p class="text-sm text-blue-700">
        <i class="fas fa-info-circle mr-2"></i>
        Viendo reservaciones de: <strong>
            <?php 
                foreach ($allSpecialists as $spec) {
                    if ($spec['id'] == $currentSpecialistId) {
                        echo e($spec['sucursal_nombre']);
                        break;
                    }
                }
            ?>
        </strong>
    </p>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <?php if (!empty($allSpecialists) && count($allSpecialists) > 1): ?>
        <!-- Hidden input para mantener el specialist_id en los filtros -->
        <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todos</option>
                <option value="pendiente" <?= ($currentFilters['estado'] ?? '') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="confirmada" <?= ($currentFilters['estado'] ?? '') == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="completada" <?= ($currentFilters['estado'] ?? '') == 'completada' ? 'selected' : '' ?>>Completada</option>
                <option value="cancelada" <?= ($currentFilters['estado'] ?? '') == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                <option value="no_asistio" <?= ($currentFilters['estado'] ?? '') == 'no_asistio' ? 'selected' : '' ?>>No Asisti&oacute;</option>
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
        
        <a href="<?= url('/reservaciones' . (!empty($currentSpecialistId) ? '?specialist_id=' . $currentSpecialistId : '')) ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">C&oacute;digo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tel&eacute;fono</th>
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
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-primary"><?= e($res['codigo']) ?></span>
                            <?php if (!empty($res['es_extraordinaria'])): ?>
                            <span class="mt-1 px-2 py-0.5 text-xs font-semibold bg-orange-500 text-white rounded w-fit">
                                <i class="fas fa-user-clock mr-1"></i>EXTRAORDINARIA
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800"><?= e($res['cliente_nombre_completo']) ?></p>
                        <?php if ($res['cliente_email']): ?>
                        <p class="text-sm text-gray-500"><?= e($res['cliente_email']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (!empty($res['cliente_telefono']) || !empty($res['telefono'])): ?>
                        <p class="text-gray-800">
                            <i class="fas fa-phone text-green-600 mr-1"></i>
                            <?= e($res['cliente_telefono'] ?? $res['telefono']) ?>
                        </p>
                        <?php else: ?>
                        <p class="text-gray-400 text-sm italic">Sin n&uacute;mero</p>
                        <?php endif; ?>
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
