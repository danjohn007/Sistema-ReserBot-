<?php
/**
 * Vista: Métricas de Origen de Reservas
 *
 * Variables recibidas:
 *  $bySource            array  – totales agrupados por source
 *  $byDay               array  – reservas por día y source
 *  $byPeriod            array  – serie temporal (día o mes según rango)
 *  $totalReservas       int
 *  $periodo             string – preset activo
 *  $fechaInicio         string Y-m-d
 *  $fechaFin            string Y-m-d
 *  $sourceFilter        string
 *  $especialistaId      int
 *  $especialistas       array
 *  $primeraVsRecurrente array
 *  $rolId               int
 */

// ──────────────────────────────────────────────────────────────────────────────
// Helpers de presentación
// ──────────────────────────────────────────────────────────────────────────────
$sourceLabels = [
    'liga1'   => 'WhatsApp de la landing',
    'liga2'   => 'Instagram',
    'liga3'   => 'Facebook',
    'directo' => 'Directo / Sin origen',
];

$sourceColors = [
    'liga1'   => ['bg' => '#0e7490', 'light' => '#cffafe', 'text' => '#0e7490'],
    'liga2'   => ['bg' => '#db2777', 'light' => '#fce7f3', 'text' => '#be185d'],
    'liga3'   => ['bg' => '#2563eb', 'light' => '#dbeafe', 'text' => '#1d4ed8'],
    'directo' => ['bg' => '#3b82f6', 'light' => '#dbeafe', 'text' => '#2563eb'],  // blue-500
];

$sourceIcons = [
    'liga1'   => 'fab fa-whatsapp',
    'liga2'   => 'fab fa-instagram',
    'liga3'   => 'fab fa-facebook-f',
    'directo' => 'fas fa-user',
];

function getSourceLabel($s, $map) {
    return $map[$s] ?? ucfirst($s);
}

// ──────────────────────────────────────────────────────────────────────────────
// Preparar datos para Chart.js
// ──────────────────────────────────────────────────────────────────────────────

// 1) Pie / Doughnut
$pieLabels  = [];
$pieData    = [];
$pieColors  = [];
foreach ($bySource as $row) {
    $s             = $row['source'];
    $pieLabels[]   = getSourceLabel($s, $sourceLabels);
    $pieData[]     = (int)$row['total'];
    $pieColors[]   = $sourceColors[$s]['bg'] ?? '#94a3b8';
}

// 2) Gráfica de barras apiladas – serie temporal
$allSources  = array_unique(array_column($byPeriod, 'source'));
$allPeriods  = array_unique(array_column($byPeriod, 'periodo'));
sort($allPeriods);

// Reconstruir como matriz [periodo][source] = total
$matrix = [];
foreach ($byPeriod as $row) {
    $matrix[$row['periodo']][$row['source']] = (int)$row['total'];
}

// Formatear etiquetas del eje X
$barLabels = array_map(function($p) {
    // Si formato es Y-m-d mostramos d/m, si es Y-m-01 mostramos Mes Año
    if (substr($p, 7) === '-01' && strlen($p) === 10) {
        return date('M Y', strtotime($p));
    }
    return date('d/m', strtotime($p));
}, $allPeriods);

$barDatasets = [];
foreach ($allSources as $s) {
    $data = [];
    foreach ($allPeriods as $p) {
        $data[] = $matrix[$p][$s] ?? 0;
    }
    $color = $sourceColors[$s]['bg'] ?? '#94a3b8';
    $barDatasets[] = [
        'label'           => getSourceLabel($s, $sourceLabels),
        'data'            => $data,
        'backgroundColor' => $color,
        'borderColor'     => $color,
        'borderWidth'     => 1,
        'borderRadius'    => 4,
    ];
}

// 3) Gráfica de barras Primera vs Recurrente
$pvLabels = [];
$pvPrimera = [];
$pvRecurrente = [];
foreach ($primeraVsRecurrente as $row) {
    $pvLabels[]     = getSourceLabel($row['source'], $sourceLabels);
    $pvPrimera[]    = (int)$row['primera'];
    $pvRecurrente[] = (int)$row['recurrente'];
}

// Presets de período
$presets = [
    '7d'  => 'Últimos 7 días',
    '30d' => 'Últimos 30 días',
    'mes' => 'Este mes',
    '3m'  => 'Últimos 3 meses',
    '6m'  => 'Últimos 6 meses',
    'ano' => 'Este año',
    'custom' => 'Personalizado',
];
?>

<!-- Barra superior de título + acciones -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-chart-pie text-blue-500"></i>
            Métricas de Origen de Reservas
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            <?= date('d M Y', strtotime($fechaInicio)) ?> — <?= date('d M Y', strtotime($fechaFin)) ?>
        </p>
    </div>
    <a href="<?= url('/dashboard') ?>"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition shadow-sm">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     FILTROS
═════════════════════════════════════════════════════════════════════════════ -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <form method="GET" action="<?= url('/metricas/origen-reservas') ?>" id="filtrosForm">
        <div class="flex flex-wrap gap-3 items-end">

            <!-- Presets de período -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Período</label>
                <div class="flex flex-wrap gap-1">
                    <?php foreach ($presets as $key => $label): ?>
                        <?php if ($key === 'custom') continue; ?>
                        <button type="button"
                                onclick="setPeriodo('<?= $key ?>')"
                                class="px-3 py-1.5 text-xs rounded-lg border transition font-medium preset-btn
                                       <?= $periodo === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400' ?>"
                                data-periodo="<?= $key ?>">
                            <?= $label ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Rango personalizado -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Desde</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= e($fechaInicio) ?>"
                       class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       onchange="setPeriodo('custom')">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Hasta</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="<?= e($fechaFin) ?>"
                       class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       onchange="setPeriodo('custom')">
            </div>

            <!-- Filtro por origen -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Origen</label>
                <select name="source"
                        class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="document.getElementById('filtrosForm').submit()">
                    <option value="" <?= $sourceFilter === '' ? 'selected' : '' ?>>Todos los orígenes</option>
                    <option value="liga1"   <?= $sourceFilter === 'liga1'   ? 'selected' : '' ?>>WhatsApp de la landing</option>
                    <option value="liga2"   <?= $sourceFilter === 'liga2'   ? 'selected' : '' ?>>Instagram</option>
                    <option value="liga3"   <?= $sourceFilter === 'liga3'   ? 'selected' : '' ?>>Facebook</option>
                    <option value="directo" <?= $sourceFilter === 'directo' ? 'selected' : '' ?>>Directo / Sin origen</option>
                </select>
            </div>

            <?php if (!empty($especialistas)): ?>
            <!-- Filtro por especialista (solo admins) -->
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Especialista</label>
                <select name="especialista_id"
                        class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="document.getElementById('filtrosForm').submit()">
                    <option value="">Todos</option>
                    <?php foreach ($especialistas as $esp): ?>
                        <option value="<?= $esp['id'] ?>" <?= $especialistaId == $esp['id'] ? 'selected' : '' ?>>
                            <?= e($esp['nombre'] . ' ' . $esp['apellidos']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Input oculto del período -->
            <input type="hidden" name="periodo" id="periodo_input" value="<?= e($periodo) ?>">

            <button type="submit"
                    class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="fas fa-filter mr-1"></i> Aplicar
            </button>
        </div>
    </form>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     TARJETAS DE RESUMEN
═════════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-<?= min(4, count($bySource) + 1) ?> gap-4 mb-6">

    <!-- Total general -->
    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-calendar-check text-gray-500 text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total reservas</p>
            <p class="text-3xl font-bold text-gray-800"><?= number_format($totalReservas) ?></p>
        </div>
    </div>

    <?php foreach ($bySource as $row):
        $s       = $row['source'];
        $color   = $sourceColors[$s] ?? ['bg' => '#94a3b8', 'light' => '#f1f5f9', 'text' => '#64748b'];
        $icon    = $sourceIcons[$s] ?? 'fas fa-tag';
        $pct     = $totalReservas > 0 ? round($row['total'] / $totalReservas * 100, 1) : 0;
        $label   = getSourceLabel($s, $sourceLabels);
    ?>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: <?= $color['light'] ?>">
                <i class="<?= $icon ?> text-lg" style="color: <?= $color['bg'] ?>"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide truncate"><?= e($label) ?></p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($row['total']) ?></p>
            </div>
        </div>
        <!-- Barra de porcentaje -->
        <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
            <div class="h-2 rounded-full transition-all"
                 style="width: <?= $pct ?>%; background-color: <?= $color['bg'] ?>"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500">
            <span><?= $pct ?>% del total</span>
            <span class="text-green-600"><?= number_format($row['completadas']) ?> completadas</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     GRÁFICAS PRINCIPALES
═════════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Gráfica circular (Doughnut) -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Distribución por Origen</h3>
        <p class="text-xs text-gray-500 mb-4">Porcentaje del total de reservas</p>
        <?php if ($totalReservas > 0): ?>
        <div class="relative" style="height: 260px;">
            <canvas id="doughnutChart"></canvas>
        </div>
        <!-- Leyenda personalizada -->
        <div class="mt-4 space-y-2">
            <?php foreach ($bySource as $row):
                $s     = $row['source'];
                $color = $sourceColors[$s]['bg'] ?? '#94a3b8';
                $pct   = $totalReservas > 0 ? round($row['total'] / $totalReservas * 100, 1) : 0;
            ?>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?= $color ?>"></span>
                    <span class="text-gray-700"><?= e(getSourceLabel($s, $sourceLabels)) ?></span>
                </div>
                <span class="font-semibold text-gray-800"><?= $pct ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
            <i class="fas fa-chart-pie text-5xl mb-3"></i>
            <p class="text-sm">Sin datos en el período</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Gráfica de barras – serie temporal (ocupa 2 columnas) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Reservas por Período y Origen</h3>
        <p class="text-xs text-gray-500 mb-4">Barras apiladas agrupadas por fecha</p>
        <?php if (!empty($byPeriod)): ?>
        <div style="height: 300px;">
            <canvas id="barChart"></canvas>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
            <i class="fas fa-chart-bar text-5xl mb-3"></i>
            <p class="text-sm">Sin datos en el período</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     FILA INFERIOR: Primera Consulta + Tabla de detalle
═════════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Gráfica Primera vs Recurrente -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Primera Consulta vs Recurrente</h3>
        <p class="text-xs text-gray-500 mb-4">Desglose por origen</p>
        <?php if (!empty($primeraVsRecurrente)): ?>
        <div style="height: 240px;">
            <canvas id="pvChart"></canvas>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
            <i class="fas fa-users text-4xl mb-2"></i>
            <p class="text-sm">Sin datos</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabla de detalle por origen -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Detalle por Origen</h3>
        <?php if (!empty($bySource)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-3 py-2 text-left rounded-l-lg">Origen</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right">%</th>
                        <th class="px-3 py-2 text-right">Completas</th>
                        <th class="px-3 py-2 text-right">Canceladas</th>
                        <th class="px-3 py-2 text-right rounded-r-lg">1.ª Cons.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($bySource as $row):
                        $s     = $row['source'];
                        $color = $sourceColors[$s]['bg'] ?? '#94a3b8';
                        $pct   = $totalReservas > 0 ? round($row['total'] / $totalReservas * 100, 1) : 0;
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                      style="background-color: <?= $color ?>"></span>
                                <span class="font-medium text-gray-700">
                                    <?= e(getSourceLabel($s, $sourceLabels)) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-right font-semibold text-gray-800">
                            <?= number_format($row['total']) ?>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                  style="background-color: <?= $color ?>">
                                <?= $pct ?>%
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right text-green-600 font-medium">
                            <?= number_format($row['completadas']) ?>
                        </td>
                        <td class="px-3 py-3 text-right text-red-500 font-medium">
                            <?= number_format($row['canceladas']) ?>
                        </td>
                        <td class="px-3 py-3 text-right text-blue-500 font-medium">
                            <?= number_format($row['primera_consulta']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold text-gray-700 text-xs uppercase">
                        <td class="px-3 py-2 rounded-l-lg">Total</td>
                        <td class="px-3 py-2 text-right"><?= number_format($totalReservas) ?></td>
                        <td class="px-3 py-2 text-right">100%</td>
                        <td class="px-3 py-2 text-right text-green-600">
                            <?= number_format(array_sum(array_column($bySource, 'completadas'))) ?>
                        </td>
                        <td class="px-3 py-2 text-right text-red-500">
                            <?= number_format(array_sum(array_column($bySource, 'canceladas'))) ?>
                        </td>
                        <td class="px-3 py-2 text-right text-blue-500 rounded-r-lg">
                            <?= number_format(array_sum(array_column($bySource, 'primera_consulta'))) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
            <i class="fas fa-table text-4xl mb-2"></i>
            <p class="text-sm">Sin datos en el período seleccionado</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SCRIPTS
═════════════════════════════════════════════════════════════════════════════ -->
<script>
// ─── Datos del servidor ───────────────────────────────────────────────────────
const pieLabels  = <?= json_encode($pieLabels) ?>;
const pieData    = <?= json_encode($pieData) ?>;
const pieColors  = <?= json_encode($pieColors) ?>;

const barLabels   = <?= json_encode($barLabels) ?>;
const barDatasets = <?= json_encode($barDatasets) ?>;

const pvLabels     = <?= json_encode($pvLabels) ?>;
const pvPrimera    = <?= json_encode($pvPrimera) ?>;
const pvRecurrente = <?= json_encode($pvRecurrente) ?>;

// ─── Opciones comunes ─────────────────────────────────────────────────────────
const tooltipPlugin = {
    padding: 10,
    backgroundColor: 'rgba(17,24,39,0.9)',
    titleFont: { size: 13, weight: 'bold' },
    bodyFont: { size: 12 },
    cornerRadius: 8,
    displayColors: true,
};

document.addEventListener('DOMContentLoaded', function () {

    // 1) Doughnut
    const dCtx = document.getElementById('doughnutChart');
    if (dCtx && pieData.length > 0) {
        new Chart(dCtx, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipPlugin,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2) Barras apiladas por período
    const bCtx = document.getElementById('barChart');
    if (bCtx && barLabels.length > 0) {
        new Chart(bCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: barDatasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11 }, padding: 16 }
                    },
                    tooltip: tooltipPlugin,
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 11 } },
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            stepSize: 1,
                            callback: v => Number.isInteger(v) ? v : null,
                        }
                    }
                }
            }
        });
    }

    // 3) Primera vs Recurrente (barras agrupadas horizontales)
    const pvCtx = document.getElementById('pvChart');
    if (pvCtx && pvLabels.length > 0) {
        new Chart(pvCtx, {
            type: 'bar',
            data: {
                labels: pvLabels,
                datasets: [
                    {
                        label: 'Primera consulta',
                        data: pvPrimera,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                    },
                    {
                        label: 'Recurrente',
                        data: pvRecurrente,
                        backgroundColor: '#a3e635',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11 }, padding: 12 }
                    },
                    tooltip: tooltipPlugin,
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            stepSize: 1,
                            callback: v => Number.isInteger(v) ? v : null,
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    }
                }
            }
        });
    }
});

// ─── Filtros de período ───────────────────────────────────────────────────────
function setPeriodo(periodo) {
    document.getElementById('periodo_input').value = periodo;

    // Actualizar estilos de botones preset
    document.querySelectorAll('.preset-btn').forEach(btn => {
        const isActive = btn.dataset.periodo === periodo;
        btn.classList.toggle('bg-blue-600',   isActive);
        btn.classList.toggle('text-white',     isActive);
        btn.classList.toggle('border-blue-600',isActive);
        btn.classList.toggle('bg-white',       !isActive);
        btn.classList.toggle('text-gray-600',  !isActive);
        btn.classList.toggle('border-gray-300',!isActive);
    });

    if (periodo !== 'custom') {
        document.getElementById('filtrosForm').submit();
    }
}
</script>
