<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/reservaciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Reservaciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Reservación <?= e($reservation['codigo']) ?></h2>
                    <p class="text-gray-500">Creada el <?= formatDate($reservation['created_at'], 'd/m/Y H:i') ?></p>
                </div>
                <span class="px-4 py-2 text-sm rounded-full <?= getStatusBadgeClass($reservation['estado']) ?>">
                    <?= getStatusText($reservation['estado']) ?>
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Date and Time -->
            <div class="mb-8 text-center py-6 bg-blue-50 rounded-xl">
                <i class="fas fa-calendar-alt text-4xl text-primary mb-3"></i>
                <p class="text-2xl font-bold text-gray-800"><?= formatDate($reservation['fecha_cita'], 'l, d \d\e F \d\e Y') ?></p>
                <p class="text-xl text-primary"><?= formatTime($reservation['hora_inicio']) ?> - <?= formatTime($reservation['hora_fin']) ?></p>
            </div>
            
            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Info -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-user text-primary mr-2"></i>Cliente
                    </h3>
                    <p class="font-medium"><?= e($reservation['cliente_nombre'] . ' ' . $reservation['cliente_apellidos']) ?></p>
                    <p class="text-sm text-gray-600"><?= e($reservation['cliente_email']) ?></p>
                    <?php if ($reservation['cliente_telefono']): ?>
                    <p class="text-sm text-gray-600"><?= e($reservation['cliente_telefono']) ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Specialist Info -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-user-md text-primary mr-2"></i>Especialista
                    </h3>
                    <p class="font-medium"><?= e($reservation['especialista_nombre'] . ' ' . $reservation['especialista_apellidos']) ?></p>
                </div>
                
                <!-- Service Info -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-concierge-bell text-primary mr-2"></i>Servicio
                    </h3>
                    <p class="font-medium"><?= e($reservation['servicio_nombre']) ?></p>
                    <?php if ($reservation['servicio_descripcion']): ?>
                    <p class="text-sm text-gray-600 mt-1"><?= e($reservation['servicio_descripcion']) ?></p>
                    <?php endif; ?>
                    <p class="text-sm text-gray-600 mt-2">
                        <i class="fas fa-clock mr-1"></i><?= $reservation['duracion_minutos'] ?> minutos
                    </p>
                </div>
                
                <!-- Location Info -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-building text-primary mr-2"></i>Sucursal
                    </h3>
                    <p class="font-medium"><?= e($reservation['sucursal_nombre']) ?></p>
                    <?php if ($reservation['sucursal_direccion']): ?>
                    <p class="text-sm text-gray-600"><?= e($reservation['sucursal_direccion']) ?></p>
                    <?php endif; ?>
                    <?php if ($reservation['sucursal_telefono']): ?>
                    <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i><?= e($reservation['sucursal_telefono']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Price -->
            <div class="mt-6 p-4 bg-green-50 rounded-lg flex justify-between items-center">
                <span class="font-semibold text-gray-800">Total a Pagar:</span>
                <span class="text-2xl font-bold text-green-600"><?= formatMoney($reservation['precio_total']) ?></span>
            </div>
            
            <!-- Notes -->
            <?php if ($reservation['notas_cliente']): ?>
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-2">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>Notas del Cliente
                </h3>
                <p class="text-gray-600"><?= e($reservation['notas_cliente']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($reservation['notas_especialista']): ?>
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-2">
                    <i class="fas fa-comment-medical text-blue-600 mr-2"></i>Notas del Especialista
                </h3>
                <p class="text-gray-600"><?= e($reservation['notas_especialista']) ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Cancellation Info -->
            <?php if ($reservation['estado'] == 'cancelada'): ?>
            <div class="mt-4 p-4 bg-red-50 rounded-lg">
                <h3 class="font-semibold text-red-800 mb-2">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>Cancelación
                </h3>
                <p class="text-gray-600">Cancelada el <?= formatDate($reservation['fecha_cancelacion'], 'd/m/Y H:i') ?></p>
                <?php if ($reservation['motivo_cancelacion']): ?>
                <p class="text-gray-600 mt-1">Motivo: <?= e($reservation['motivo_cancelacion']) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Actions -->
        <div class="p-6 bg-gray-50 border-t flex justify-between items-center">
            <a href="<?= url('/reservaciones') ?>" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
            
            <div class="space-x-3">
                <?php if ($reservation['estado'] == 'pendiente'): ?>
                <a href="<?= url('/reservaciones/confirmar?id=' . $reservation['id']) ?>" 
                   class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                   onclick="return confirm('¿Confirmar esta cita?')">
                    <i class="fas fa-check mr-2"></i>Confirmar
                </a>
                <?php endif; ?>
                
                <?php if ($reservation['estado'] != 'cancelada' && $reservation['estado'] != 'completada'): ?>
                <a href="<?= url('/reservaciones/cancelar?id=' . $reservation['id']) ?>" 
                   class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                   onclick="return confirm('¿Cancelar esta cita?')">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
