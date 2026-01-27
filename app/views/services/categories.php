<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Categor&iacute;as de Servicios</h2>
        <p class="text-gray-500 text-sm">Organiza los servicios por categor&iacute;as</p>
    </div>
    <a href="<?= url('/categorias/crear') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nueva Categor&iacute;a
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($categories as $cat): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" style="background-color: <?= e($cat['color']) ?>">
                        <i class="<?= e($cat['icono']) ?> text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= e($cat['nombre']) ?></h3>
                        <p class="text-sm text-gray-500"><?= $cat['total_servicios'] ?> servicios</p>
                    </div>
                </div>
                <span class="px-2 py-1 text-xs rounded-full <?= $cat['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= $cat['activo'] ? 'Activa' : 'Inactiva' ?>
                </span>
            </div>
            
            <?php if ($cat['descripcion']): ?>
            <p class="text-sm text-gray-600 mb-4"><?= e($cat['descripcion']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
            <a href="<?= url('/categorias/editar?id=' . $cat['id']) ?>" 
               class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($categories)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay categor&iacute;as</h3>
    <p class="text-gray-500 mt-2">Comienza agregando la primera categor&iacute;a</p>
</div>
<?php endif; ?>
