<h2 class="text-2xl font-bold text-gray-800 mb-6">Logs de Seguridad</h2>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
            <select name="accion" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todas</option>
                <?php foreach ($actions as $action): ?>
                <option value="<?= e($action['accion']) ?>" <?= ($filters['accion'] ?? '') == $action['accion'] ? 'selected' : '' ?>>
                    <?= e($action['accion']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
            <select name="usuario_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Todos</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= ($filters['usuario_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                    <?= e($user['nombre'] . ' ' . $user['apellidos']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" name="fecha" value="<?= $filters['fecha'] ?? '' ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        <a href="<?= url('/logs') ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            Limpiar
        </a>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripci&oacute;n</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($logs as $log): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">
                    <?= formatDate($log['created_at'], 'd/m/Y H:i:s') ?>
                </td>
                <td class="px-6 py-4">
                    <?php if ($log['nombre']): ?>
                    <p class="font-medium text-gray-800"><?= e($log['nombre'] . ' ' . $log['apellidos']) ?></p>
                    <p class="text-xs text-gray-500"><?= e($log['email']) ?></p>
                    <?php else: ?>
                    <span class="text-gray-400">Sistema</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                        <?= e($log['accion']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                    <?= e($log['descripcion']) ?>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <?= e($log['ip_address']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($pagination['total'] > 1): ?>
<div class="mt-6 flex justify-between items-center">
    <p class="text-sm text-gray-600">
        Mostrando <?= ($pagination['current'] - 1) * $pagination['perPage'] + 1 ?> - 
        <?= min($pagination['current'] * $pagination['perPage'], $pagination['totalRecords']) ?> 
        de <?= $pagination['totalRecords'] ?> registros
    </p>
    <div class="flex space-x-2">
        <?php if ($pagination['current'] > 1): ?>
        <a href="<?= url('/logs?page=' . ($pagination['current'] - 1)) ?>" 
           class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-left"></i>
        </a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['total'], $pagination['current'] + 2); $i++): ?>
        <a href="<?= url('/logs?page=' . $i) ?>" 
           class="px-3 py-1 border rounded-lg <?= $i == $pagination['current'] ? 'bg-primary text-white border-primary' : 'border-gray-300 hover:bg-gray-50' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php if ($pagination['current'] < $pagination['total']): ?>
        <a href="<?= url('/logs?page=' . ($pagination['current'] + 1)) ?>" 
           class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
