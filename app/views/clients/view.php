<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/clientes') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Clientes
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                        <?= strtoupper(substr($client['nombre'], 0, 1) . substr($client['apellidos'], 0, 1)) ?>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800"><?= e($client['nombre'] . ' ' . $client['apellidos']) ?></h2>
                    <span class="px-2 py-1 text-xs rounded-full <?= $client['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= $client['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope w-6"></i>
                        <span><?= e($client['email']) ?></span>
                    </div>
                    <?php if ($client['telefono']): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone w-6"></i>
                        <span><?= e($client['telefono']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar w-6"></i>
                        <span>Miembro desde <?= formatDate($client['created_at'], 'd/m/Y') ?></span>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $stats['total_citas'] ?></p>
                            <p class="text-xs text-gray-500">Total Citas</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600"><?= formatMoney($stats['total_gastado']) ?></p>
                            <p class="text-xs text-gray-500">Total Gastado</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="<?= url('/clientes/editar?id=' . $client['id']) ?>" 
                       class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-edit mr-2"></i>Editar Cliente
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Appointments History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Citas</h3>
                
                <?php if (empty($appointments)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>Este cliente no tiene citas registradas</p>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($appointments as $appt): ?>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800"><?= e($appt['servicio_nombre']) ?></p>
                            <p class="text-sm text-gray-500">
                                <?= formatDate($appt['fecha_cita']) ?> - <?= formatTime($appt['hora_inicio']) ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <?= e($appt['especialista_nombre'] . ' ' . $appt['especialista_apellidos']) ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full <?= getStatusBadgeClass($appt['estado']) ?>">
                                <?= getStatusText($appt['estado']) ?>
                            </span>
                            <p class="text-sm font-medium text-gray-700 mt-1"><?= formatMoney($appt['precio_total']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
