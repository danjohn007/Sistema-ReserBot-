<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Clientes</h2>
        <p class="text-gray-500 text-sm">Gestiona los clientes del sistema</p>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="<?= e($search ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                   placeholder="Buscar por nombre, email o teléfono...">
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
            <i class="fas fa-search mr-2"></i>Buscar
        </button>
        <?php if ($search): ?>
        <a href="<?= url('/clientes') ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            Limpiar
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Citas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Cita</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($clients as $client): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold mr-3">
                            <?= strtoupper(substr($client['nombre'], 0, 1)) ?>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800"><?= e($client['nombre'] . ' ' . $client['apellidos']) ?></p>
                            <p class="text-sm text-gray-500">Desde <?= formatDate($client['created_at'], 'd/m/Y') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-gray-600"><?= e($client['email']) ?></p>
                    <?php if ($client['telefono']): ?>
                    <p class="text-sm text-gray-500"><?= e($client['telefono']) ?></p>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <span class="font-medium text-gray-800"><?= $client['total_citas'] ?></span>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <?= $client['ultima_cita'] ? formatDate($client['ultima_cita']) : 'Sin citas' ?>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full <?= $client['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= $client['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="<?= url('/clientes/ver?id=' . $client['id']) ?>" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= url('/clientes/editar?id=' . $client['id']) ?>" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($clients)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center mt-6">
    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No se encontraron clientes</h3>
</div>
<?php endif; ?>
