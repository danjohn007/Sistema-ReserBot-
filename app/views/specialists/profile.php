<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-primary text-white p-6 text-center">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl font-bold text-primary">
                    <?= strtoupper(substr($specialist['nombre'], 0, 1) . substr($specialist['apellidos'], 0, 1)) ?>
                </span>
            </div>
            <h2 class="text-2xl font-bold"><?= e($specialist['nombre'] . ' ' . $specialist['apellidos']) ?></h2>
            <p class="text-blue-100"><?= e($specialist['profesion']) ?></p>
            <?php if ($specialist['especialidad']): ?>
            <p class="text-sm text-blue-200"><?= e($specialist['especialidad']) ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-3 border-b">
            <div class="p-4 text-center border-r">
                <div class="flex items-center justify-center text-yellow-500 mb-1">
                    <i class="fas fa-star mr-1"></i>
                    <span class="font-bold text-lg"><?= number_format($specialist['calificacion_promedio'], 1) ?></span>
                </div>
                <p class="text-xs text-gray-500"><?= $specialist['total_resenas'] ?> reseñas</p>
            </div>
            <div class="p-4 text-center border-r">
                <p class="font-bold text-lg text-gray-800"><?= $specialist['experiencia_anos'] ?></p>
                <p class="text-xs text-gray-500">Años exp.</p>
            </div>
            <div class="p-4 text-center">
                <p class="font-bold text-lg text-green-600"><?= formatMoney($specialist['tarifa_base']) ?></p>
                <p class="text-xs text-gray-500">Tarifa base</p>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <!-- About -->
            <?php if ($specialist['descripcion']): ?>
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Acerca de</h3>
                <p class="text-gray-600"><?= e($specialist['descripcion']) ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Location -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Ubicación</h3>
                <p class="text-gray-600">
                    <i class="fas fa-building text-primary mr-2"></i><?= e($specialist['sucursal_nombre']) ?>
                </p>
                <?php if ($specialist['sucursal_direccion']): ?>
                <p class="text-gray-500 text-sm ml-6"><?= e($specialist['sucursal_direccion']) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Services -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Servicios</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($services as $service): ?>
                    <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800"><?= e($service['nombre']) ?></p>
                            <p class="text-xs text-gray-500"><?= $service['duracion_personalizada'] ?? $service['duracion_minutos'] ?> min</p>
                        </div>
                        <span class="font-bold text-primary">
                            <?= formatMoney($service['precio_personalizado'] ?? $service['precio']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Schedule -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Horarios</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <?php foreach ($schedules as $schedule): ?>
                    <div class="p-2 bg-gray-50 rounded text-center text-sm">
                        <p class="font-medium text-gray-700"><?= $daysOfWeek[$schedule['dia_semana']] ?></p>
                        <p class="text-gray-500"><?= formatTime($schedule['hora_inicio']) ?> - <?= formatTime($schedule['hora_fin']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Book Button -->
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('/reservaciones/nueva?especialista_id=' . $specialist['id']) ?>" 
               class="block w-full text-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <i class="fas fa-calendar-plus mr-2"></i>Agendar Cita
            </a>
            <?php else: ?>
            <a href="<?= url('/login') ?>" 
               class="block w-full text-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Inicia sesión para agendar
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
