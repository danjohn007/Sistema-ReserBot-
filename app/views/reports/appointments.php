<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/reportes') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Reporte de Citas</h2>
        
        <!-- Filtros -->
        <form method="GET" action="<?= url('/reportes/citas') ?>" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= e($filters['fecha_inicio']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="<?= e($filters['fecha_fin']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <?php if (!empty($branches)): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <select name="sucursal_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Todas las sucursales</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= $filters['sucursal_id'] == $branch['id'] ? 'selected' : '' ?>>
                            <?= e($branch['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Resumen por Estado -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <?php
            $totalCitas = 0;
            $estadosMap = [
                'pendiente' => ['label' => 'Pendientes', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
                'confirmada' => ['label' => 'Confirmadas', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-circle'],
                'completada' => ['label' => 'Completadas', 'color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-double'],
                'cancelada' => ['label' => 'Canceladas', 'color' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
                'no_show' => ['label' => 'No Asistió', 'color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-user-slash']
            ];
            
            $summaryData = [];
            foreach ($summary as $item) {
                $summaryData[$item['estado']] = $item['total'];
                $totalCitas += $item['total'];
            }
            ?>
            
            <?php foreach ($estadosMap as $estado => $info): ?>
            <div class="p-4 <?= $info['color'] ?> rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas <?= $info['icon'] ?> text-2xl"></i>
                    <span class="text-2xl font-bold"><?= $summaryData[$estado] ?? 0 ?></span>
                </div>
                <p class="text-sm font-medium"><?= $info['label'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Total -->
        <div class="bg-gradient-to-r from-primary to-secondary text-white p-4 rounded-lg mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Total de Citas</p>
                    <p class="text-3xl font-bold"><?= $totalCitas ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Período</p>
                    <p class="font-semibold"><?= formatDate($filters['fecha_inicio']) ?> - <?= formatDate($filters['fecha_fin']) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Botones de Exportación -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Exportar Excel
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-print mr-2"></i>Imprimir
            </button>
        </div>
        
        <!-- Tabla de Datos -->
        <?php if (empty($data)): ?>
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-4"></i>
            <p>No hay datos para mostrar en el período seleccionado</p>
        </div>
        <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="appointmentsTable">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Total Citas
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Ingresos
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $currentDate = '';
                    $dateTotal = 0;
                    $dateIncome = 0;
                    
                    foreach ($data as $index => $row):
                        if ($currentDate != $row['fecha_cita'] && $currentDate != '') {
                            // Mostrar total de la fecha anterior
                            ?>
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-4 py-3" colspan="2">Total <?= formatDate($currentDate) ?></td>
                                <td class="px-4 py-3 text-center"><?= $dateTotal ?></td>
                                <td class="px-4 py-3 text-right text-green-600"><?= formatMoney($dateIncome) ?></td>
                            </tr>
                            <?php
                            $dateTotal = 0;
                            $dateIncome = 0;
                        }
                        
                        $currentDate = $row['fecha_cita'];
                        $dateTotal += $row['total'];
                        $dateIncome += $row['ingresos'];
                        
                        $estadoInfo = $estadosMap[$row['estado']] ?? ['label' => ucfirst($row['estado']), 'color' => 'bg-gray-100 text-gray-800'];
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-gray-900"><?= formatDate($row['fecha_cita']) ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $estadoInfo['color'] ?>">
                                <?= $estadoInfo['label'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-medium">
                            <?= $row['total'] ?>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-green-600">
                            <?= formatMoney($row['ingresos']) ?>
                        </td>
                    </tr>
                    <?php 
                    // Si es el último registro, mostrar su total
                    if ($index == count($data) - 1):
                    ?>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-4 py-3" colspan="2">Total <?= formatDate($currentDate) ?></td>
                        <td class="px-4 py-3 text-center"><?= $dateTotal ?></td>
                        <td class="px-4 py-3 text-right text-green-600"><?= formatMoney($dateIncome) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <!-- Total General -->
                    <tr class="bg-primary text-white font-bold">
                        <td class="px-4 py-4" colspan="2">TOTAL GENERAL</td>
                        <td class="px-4 py-4 text-center"><?= $totalCitas ?></td>
                        <td class="px-4 py-4 text-right">
                            <?php
                            $totalIngresos = 0;
                            foreach ($data as $row) {
                                $totalIngresos += $row['ingresos'];
                            }
                            echo formatMoney($totalIngresos);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php endif; ?>
    </div>
    
    <!-- Gráfica de Citas por Estado -->
    <?php if (!empty($summary)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Distribución por Estado</h3>
        <div class="max-w-md mx-auto">
            <canvas id="estadoChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Gráfica de estados
<?php if (!empty($summary)): ?>
const ctx = document.getElementById('estadoChart');
const estadoChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            foreach ($summary as $item) {
                $info = $estadosMap[$item['estado']] ?? ['label' => ucfirst($item['estado'])];
                echo "'" . $info['label'] . "',";
            }
            ?>
        ],
        datasets: [{
            data: [<?php foreach ($summary as $item) echo $item['total'] . ','; ?>],
            backgroundColor: [
                '#FCD34D', // Amarillo - Pendiente
                '#60A5FA', // Azul - Confirmada
                '#34D399', // Verde - Completada
                '#F87171', // Rojo - Cancelada
                '#9CA3AF'  // Gris - No show
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// Exportar a Excel
function exportToExcel() {
    const table = document.getElementById('appointmentsTable');
    let csv = [];
    
    // Encabezados
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Datos
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
        });
        if (row.length > 0) {
            csv.push(row.join(','));
        }
    });
    
    // Descargar
    const csvContent = '\ufeff' + csv.join('\n'); // BOM para Excel
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'reporte_citas_<?= date('Y-m-d') ?>.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Estilos para impresión
const style = document.createElement('style');
style.textContent = `
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-7xl, .max-w-7xl * {
            visibility: visible;
        }
        .max-w-7xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button, .mb-6 a {
            display: none !important;
        }
    }
`;
document.head.appendChild(style);
</script>
