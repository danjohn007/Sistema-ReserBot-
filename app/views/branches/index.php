<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Sucursales</h2>
        <p class="text-gray-500 text-sm">Gestiona las sucursales del sistema</p>
    </div>
    <?php if (hasRole(ROLE_SUPERADMIN)): ?>
    <a href="<?= url('/sucursales/crear') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nueva Sucursal
    </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($branches as $branch): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800"><?= e($branch['nombre']) ?></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i><?= e($branch['ciudad']) ?>, <?= e($branch['estado']) ?>
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full <?= $branch['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= $branch['activo'] ? 'Activa' : 'Inactiva' ?>
                </span>
            </div>
            
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <?php if ($branch['direccion']): ?>
                <p><i class="fas fa-location-dot w-5"></i><?= e($branch['direccion']) ?></p>
                <?php endif; ?>
                <?php if ($branch['telefono']): ?>
                <p><i class="fas fa-phone w-5"></i><?= e($branch['telefono']) ?></p>
                <?php endif; ?>
                <?php if ($branch['email']): ?>
                <p><i class="fas fa-envelope w-5"></i><?= e($branch['email']) ?></p>
                <?php endif; ?>
                <p><i class="fas fa-clock w-5"></i><?= formatTime($branch['horario_apertura']) ?> - <?= formatTime($branch['horario_cierre']) ?></p>
            </div>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
            <a href="<?= url('/sucursales/editar?id=' . $branch['id']) ?>" 
               class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <?php if (hasRole(ROLE_SUPERADMIN)): ?>
            <a href="<?= url('/sucursales/eliminar?id=' . $branch['id']) ?>" 
               class="text-red-600 hover:text-red-800 text-sm"
               onclick="return confirm('¿Está seguro de desactivar esta sucursal?')">
                <i class="fas fa-trash"></i> Eliminar
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($branches)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay sucursales</h3>
    <p class="text-gray-500 mt-2">Comienza agregando la primera sucursal</p>
    <?php if (hasRole(ROLE_SUPERADMIN)): ?>
    <a href="<?= url('/sucursales/crear') ?>" 
       class="inline-block mt-4 bg-primary text-white px-6 py-2 rounded-lg hover:bg-secondary transition">
        <i class="fas fa-plus mr-2"></i>Agregar Sucursal
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>
