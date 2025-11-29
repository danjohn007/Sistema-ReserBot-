<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Servicios</h2>
        <p class="text-gray-500 text-sm">Gestiona los servicios disponibles</p>
    </div>
    <a href="<?= url('/servicios/crear') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nuevo Servicio
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($services as $service): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div>
                        <p class="font-medium text-gray-800"><?= e($service['nombre']) ?></p>
                        <?php if ($service['descripcion']): ?>
                        <p class="text-sm text-gray-500 truncate max-w-xs"><?= e($service['descripcion']) ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: <?= e($service['categoria_color']) ?>">
                        <?= e($service['categoria_nombre']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <?= $service['duracion_minutos'] ?> min
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <?= formatMoney($service['precio']) ?>
                    <?php if ($service['precio_oferta']): ?>
                    <span class="text-green-600 text-sm block"><?= formatMoney($service['precio_oferta']) ?></span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full <?= $service['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= $service['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="<?= url('/servicios/editar?id=' . $service['id']) ?>" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= url('/servicios/eliminar?id=' . $service['id']) ?>" 
                       class="text-red-600 hover:text-red-800"
                       onclick="return confirm('¿Está seguro de eliminar este servicio?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($services)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-concierge-bell text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay servicios</h3>
    <p class="text-gray-500 mt-2">Comienza agregando el primer servicio</p>
</div>
<?php endif; ?>
