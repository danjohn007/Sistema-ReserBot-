<h2 class="text-2xl font-bold text-gray-800 mb-6">Reportes y Estadísticas</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <a href="<?= url('/reportes/citas') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Reporte de Citas</h3>
        <p class="text-sm text-gray-500 mt-1">Estadísticas de citas por estado y fecha</p>
    </a>
    
    <a href="<?= url('/reportes/ingresos') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Reporte de Ingresos</h3>
        <p class="text-sm text-gray-500 mt-1">Ingresos por período y servicio</p>
    </a>
    
    <a href="<?= url('/reportes/especialistas') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-user-md text-purple-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Reporte de Especialistas</h3>
        <p class="text-sm text-gray-500 mt-1">Rendimiento por especialista</p>
    </a>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $db = Database::getInstance();
    $todayAppointments = $db->fetch("SELECT COUNT(*) as total FROM reservaciones WHERE fecha_cita = CURDATE()")['total'];
    $monthlyIncome = $db->fetch("SELECT COALESCE(SUM(precio_total), 0) as total FROM reservaciones WHERE estado = 'completada' AND MONTH(fecha_cita) = MONTH(CURDATE())")['total'];
    $pendingAppointments = $db->fetch("SELECT COUNT(*) as total FROM reservaciones WHERE estado = 'pendiente'")['total'];
    $cancelledAppointments = $db->fetch("SELECT COUNT(*) as total FROM reservaciones WHERE estado = 'cancelada' AND MONTH(fecha_cita) = MONTH(CURDATE())")['total'];
    ?>
    
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Citas Hoy</p>
        <p class="text-2xl font-bold text-gray-800"><?= $todayAppointments ?></p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Ingresos del Mes</p>
        <p class="text-2xl font-bold text-green-600"><?= formatMoney($monthlyIncome) ?></p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Pendientes</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $pendingAppointments ?></p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Canceladas (Mes)</p>
        <p class="text-2xl font-bold text-red-600"><?= $cancelledAppointments ?></p>
    </div>
</div>
