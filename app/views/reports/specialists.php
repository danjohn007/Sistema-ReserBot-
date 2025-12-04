<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/reportes') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Reporte de Especialistas</h2>
        
        <!-- Filtros -->
        <form method="GET" action="<?= url('/reportes/especialistas') ?>" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Resumen General -->
        <?php
        $totalCitas = 0;
        $totalCompletadas = 0;
        $totalCanceladas = 0;
        $totalIngresos = 0;
        $totalEspecialistas = count($specialists);
        
        foreach ($specialists as $spec) {
            $totalCitas += $spec['total_citas'];
            $totalCompletadas += $spec['completadas'];
            $totalCanceladas += $spec['canceladas'];
            $totalIngresos += $spec['ingresos'];
        }
        
        $promedioCalificacion = 0;
        if ($totalEspecialistas > 0) {
            $sumaCalificaciones = 0;
            foreach ($specialists as $spec) {
                $sumaCalificaciones += $spec['calificacion_promedio'];
            }
            $promedioCalificacion = $sumaCalificaciones / $totalEspecialistas;
        }
        ?>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="p-4 bg-blue-100 text-blue-800 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-user-md text-2xl"></i>
                    <span class="text-2xl font-bold"><?= $totalEspecialistas ?></span>
                </div>
                <p class="text-sm font-medium">Especialistas</p>
            </div>
            
            <div class="p-4 bg-purple-100 text-purple-800 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-calendar-check text-2xl"></i>
                    <span class="text-2xl font-bold"><?= $totalCitas ?></span>
                </div>
                <p class="text-sm font-medium">Total Citas</p>
            </div>
            
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-check-double text-2xl"></i>
                    <span class="text-2xl font-bold"><?= $totalCompletadas ?></span>
                </div>
                <p class="text-sm font-medium">Completadas</p>
            </div>
            
            <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-star text-2xl"></i>
                    <span class="text-2xl font-bold"><?= number_format($promedioCalificacion, 1) ?></span>
                </div>
                <p class="text-sm font-medium">Calif. Promedio</p>
            </div>
            
            <div class="p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                    <span class="text-xl font-bold"><?= formatMoney($totalIngresos) ?></span>
                </div>
                <p class="text-sm font-medium">Ingresos Totales</p>
            </div>
        </div>
        
        <!-- Período -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Período de Análisis</p>
                    <p class="text-lg font-semibold text-gray-800">
                        <?= formatDate($filters['fecha_inicio']) ?> - <?= formatDate($filters['fecha_fin']) ?>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                    </button>
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-print mr-2"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Especialistas -->
        <?php if (empty($specialists)): ?>
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-user-md-slash text-4xl mb-4"></i>
            <p>No hay datos de especialistas para mostrar</p>
        </div>
        <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="specialistsTable">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Especialista
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Sucursal
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Total Citas
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Completadas
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Canceladas
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tasa Éxito
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Calificación
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Ingresos
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($specialists as $index => $spec): 
                        $tasaExito = $spec['total_citas'] > 0 ? ($spec['completadas'] / $spec['total_citas']) * 100 : 0;
                        
                        // Determinar color según tasa de éxito
                        if ($tasaExito >= 80) {
                            $tasaColor = 'text-green-600 bg-green-50';
                        } elseif ($tasaExito >= 60) {
                            $tasaColor = 'text-yellow-600 bg-yellow-50';
                        } else {
                            $tasaColor = 'text-red-600 bg-red-50';
                        }
                        
                        // Determinar estrellas de calificación
                        $rating = $spec['calificacion_promedio'];
                        $ratingColor = $rating >= 4.5 ? 'text-yellow-500' : ($rating >= 3.5 ? 'text-yellow-400' : 'text-gray-400');
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-semibold text-gray-600"><?= $index + 1 ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold mr-3">
                                    <?= strtoupper(substr($spec['nombre'], 0, 1) . substr($spec['apellidos'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= e($spec['nombre'] . ' ' . $spec['apellidos']) ?></p>
                                    <p class="text-xs text-gray-500">ID: <?= $spec['id'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700"><?= e($spec['sucursal']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                <?= $spec['total_citas'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                <?= $spec['completadas'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                <?= $spec['canceladas'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?= $tasaColor ?>">
                                <?= number_format($tasaExito, 1) ?>%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-star <?= $ratingColor ?> mr-1"></i>
                                <span class="font-semibold <?= $ratingColor ?>"><?= number_format($rating, 1) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-green-600"><?= formatMoney($spec['ingresos']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <!-- Fila de totales -->
                    <tr class="bg-primary text-white font-bold">
                        <td class="px-4 py-4" colspan="3">TOTALES</td>
                        <td class="px-4 py-4 text-center"><?= $totalCitas ?></td>
                        <td class="px-4 py-4 text-center"><?= $totalCompletadas ?></td>
                        <td class="px-4 py-4 text-center"><?= $totalCanceladas ?></td>
                        <td class="px-4 py-4 text-center">
                            <?= $totalCitas > 0 ? number_format(($totalCompletadas / $totalCitas) * 100, 1) : 0 ?>%
                        </td>
                        <td class="px-4 py-4 text-center">
                            <?= number_format($promedioCalificacion, 1) ?>
                        </td>
                        <td class="px-4 py-4 text-right"><?= formatMoney($totalIngresos) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php endif; ?>
    </div>
    
    <!-- Gráficas -->
    <?php if (!empty($specialists)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Top 5 por Ingresos -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Especialistas por Ingresos</h3>
            <canvas id="ingresosChart"></canvas>
        </div>
        
        <!-- Top 5 por Citas Completadas -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Especialistas por Citas Completadas</h3>
            <canvas id="citasChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
<?php if (!empty($specialists)): ?>
// Preparar datos para gráficas - Top 5
const top5Ingresos = <?= json_encode(array_slice($specialists, 0, 5)) ?>;
const top5Citas = [...<?= json_encode($specialists) ?>].sort((a, b) => b.completadas - a.completadas).slice(0, 5);

// Gráfica de Ingresos
const ctxIngresos = document.getElementById('ingresosChart');
new Chart(ctxIngresos, {
    type: 'bar',
    data: {
        labels: top5Ingresos.map(s => s.nombre + ' ' + s.apellidos.substring(0, 1) + '.'),
        datasets: [{
            label: 'Ingresos',
            data: top5Ingresos.map(s => parseFloat(s.ingresos)),
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '$' + context.parsed.y.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(0);
                    }
                }
            }
        }
    }
});

// Gráfica de Citas
const ctxCitas = document.getElementById('citasChart');
new Chart(ctxCitas, {
    type: 'bar',
    data: {
        labels: top5Citas.map(s => s.nombre + ' ' + s.apellidos.substring(0, 1) + '.'),
        datasets: [{
            label: 'Citas Completadas',
            data: top5Citas.map(s => parseInt(s.completadas)),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
<?php endif; ?>

// Exportar a Excel
function exportToExcel() {
    const table = document.getElementById('specialistsTable');
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
    const csvContent = '\ufeff' + csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'reporte_especialistas_<?= date('Y-m-d') ?>.csv');
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
        canvas {
            max-height: 300px;
        }
    }
`;
document.head.appendChild(style);
</script>
