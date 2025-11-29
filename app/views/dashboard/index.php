<?php 
$user = currentUser();
$rolId = $user['rol_id'];
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php if ($rolId == ROLE_SUPERADMIN): ?>
    <!-- Superadmin Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Sucursales</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalBranches ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Especialistas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalSpecialists ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Clientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalClients ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Ingresos del Mes</p>
                <p class="text-3xl font-bold text-gray-800"><?= formatMoney($monthlyIncome ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_BRANCH_ADMIN || $rolId == ROLE_RECEPTIONIST): ?>
    <!-- Branch Admin Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Especialistas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalSpecialists ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Citas Hoy</p>
                <p class="text-3xl font-bold text-gray-800"><?= $todayAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $pendingAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Ingresos del Mes</p>
                <p class="text-3xl font-bold text-gray-800"><?= formatMoney($monthlyIncome ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_SPECIALIST): ?>
    <!-- Specialist Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Citas Hoy</p>
                <p class="text-3xl font-bold text-gray-800"><?= $todayAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $pendingAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Calificación</p>
                <p class="text-3xl font-bold text-gray-800">
                    <?= number_format($specialist['calificacion_promedio'] ?? 0, 1) ?>
                    <i class="fas fa-star text-yellow-400 text-lg"></i>
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_CLIENT): ?>
    <!-- Client Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Citas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Próximas Citas</p>
                <p class="text-3xl font-bold text-gray-800"><?= count($upcomingAppointments ?? []) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="col-span-1 md:col-span-2">
        <a href="<?= url('/reservaciones/nueva') ?>" 
           class="block bg-primary hover:bg-secondary text-white rounded-xl shadow-sm p-6 text-center transition duration-200">
            <i class="fas fa-plus-circle text-4xl mb-2"></i>
            <p class="text-lg font-semibold">Agendar Nueva Cita</p>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upcoming Appointments -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <?= $rolId == ROLE_CLIENT ? 'Mis Próximas Citas' : 'Próximas Citas' ?>
            </h3>
            <a href="<?= url($rolId == ROLE_CLIENT ? '/mis-citas' : '/reservaciones') ?>" class="text-primary text-sm hover:underline">
                Ver todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($upcomingAppointments)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>No hay citas próximas</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach (array_slice($upcomingAppointments, 0, 5) as $appt): ?>
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white mr-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800"><?= e($appt['servicio_nombre']) ?></p>
                    <p class="text-sm text-gray-500">
                        <?= formatDate($appt['fecha_cita']) ?> a las <?= formatTime($appt['hora_inicio']) ?>
                    </p>
                    <?php if ($rolId != ROLE_CLIENT): ?>
                    <p class="text-sm text-gray-500">
                        Cliente: <?= e($appt['cliente_nombre'] . ' ' . $appt['cliente_apellidos']) ?>
                    </p>
                    <?php else: ?>
                    <p class="text-sm text-gray-500">
                        <?= e($appt['especialista_nombre'] . ' ' . $appt['especialista_apellidos']) ?>
                    </p>
                    <?php endif; ?>
                </div>
                <span class="px-3 py-1 text-xs rounded-full <?= getStatusBadgeClass($appt['estado']) ?>">
                    <?= getStatusText($appt['estado']) ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions or Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <?php if ($rolId == ROLE_SUPERADMIN || $rolId == ROLE_BRANCH_ADMIN): ?>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actividad del Mes</h3>
        <canvas id="monthlyChart" height="200"></canvas>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_map(function($s) { 
                            return date('M Y', strtotime($s['mes'] . '-01')); 
                        }, $monthlyStats ?? [])) ?>,
                        datasets: [{
                            label: 'Citas',
                            data: <?= json_encode(array_column($monthlyStats ?? [], 'total_citas')) ?>,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
        </script>
        <?php else: ?>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-2 gap-4">
            <?php if ($rolId == ROLE_CLIENT): ?>
            <a href="<?= url('/reservaciones/nueva') ?>" class="p-4 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition">
                <i class="fas fa-plus-circle text-2xl text-blue-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Nueva Cita</p>
            </a>
            <a href="<?= url('/mis-citas') ?>" class="p-4 bg-green-50 rounded-lg text-center hover:bg-green-100 transition">
                <i class="fas fa-calendar-alt text-2xl text-green-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Mis Citas</p>
            </a>
            <a href="<?= url('/perfil') ?>" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition">
                <i class="fas fa-user text-2xl text-purple-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Mi Perfil</p>
            </a>
            <a href="<?= url('/notificaciones') ?>" class="p-4 bg-yellow-50 rounded-lg text-center hover:bg-yellow-100 transition">
                <i class="fas fa-bell text-2xl text-yellow-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Notificaciones</p>
            </a>
            <?php elseif ($rolId == ROLE_SPECIALIST): ?>
            <a href="<?= url('/calendario') ?>" class="p-4 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition">
                <i class="fas fa-calendar-week text-2xl text-blue-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Calendario</p>
            </a>
            <a href="<?= url('/especialistas/horarios') ?>" class="p-4 bg-green-50 rounded-lg text-center hover:bg-green-100 transition">
                <i class="fas fa-clock text-2xl text-green-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Mis Horarios</p>
            </a>
            <a href="<?= url('/reservaciones') ?>" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition">
                <i class="fas fa-list text-2xl text-purple-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Mis Citas</p>
            </a>
            <a href="<?= url('/perfil') ?>" class="p-4 bg-yellow-50 rounded-lg text-center hover:bg-yellow-100 transition">
                <i class="fas fa-user text-2xl text-yellow-500 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Mi Perfil</p>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($rolId == ROLE_CLIENT && !empty($pastAppointments)): ?>
<!-- Past Appointments for Client -->
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Citas</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialista</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($pastAppointments as $appt): ?>
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <?= formatDate($appt['fecha_cita']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700"><?= e($appt['servicio_nombre']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <?= e($appt['especialista_nombre'] . ' ' . $appt['especialista_apellidos']) ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full <?= getStatusBadgeClass($appt['estado']) ?>">
                            <?= getStatusText($appt['estado']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
