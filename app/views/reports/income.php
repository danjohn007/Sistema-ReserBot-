<div class="mb-6">
    <a href="<?= url('/reportes') ?>" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Reporte de Ingresos</h2>
    
    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" name="fecha_inicio" value="<?= $filters['fecha_inicio'] ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" name="fecha_fin" value="<?= $filters['fecha_fin'] ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <?php if (!empty($branches)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
            <select name="sucursal_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>" <?= $filters['sucursal_id'] == $branch['id'] ? 'selected' : '' ?>>
                    <?= e($branch['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
        </div>
    </form>
    
    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total de Ingresos</p>
            <p class="text-3xl font-bold text-green-600"><?= formatMoney($totals['total'] ?? 0) ?></p>
        </div>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Citas Completadas</p>
            <p class="text-3xl font-bold text-blue-600"><?= $totals['cantidad'] ?? 0 ?></p>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Ingresos por Día</h3>
        <canvas id="dailyChart" height="200"></canvas>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Ingresos por Servicio</h3>
        <canvas id="serviceChart" height="200"></canvas>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm p-6 mt-6">
    <h3 class="font-semibold text-gray-800 mb-4">Detalle por Servicio</h3>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($incomeByService as $row): ?>
            <tr>
                <td class="px-4 py-3"><?= e($row['servicio']) ?></td>
                <td class="px-4 py-3 text-right"><?= $row['cantidad'] ?></td>
                <td class="px-4 py-3 text-right font-semibold text-green-600"><?= formatMoney($row['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Chart
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($dailyIncome, 'fecha')) ?>,
            datasets: [{
                label: 'Ingresos',
                data: <?= json_encode(array_column($dailyIncome, 'total')) ?>,
                borderColor: 'rgb(16, 185, 129)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
    
    // Service Chart
    new Chart(document.getElementById('serviceChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($incomeByService, 'servicio')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($incomeByService, 'total')) ?>,
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
            }]
        },
        options: { responsive: true }
    });
});
</script>
