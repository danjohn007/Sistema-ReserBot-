<?php
$statusStyles = [
    'pendiente' => 'bg-amber-100 text-amber-800',
    'aprobada' => 'bg-green-100 text-green-800',
    'rechazada' => 'bg-red-100 text-red-800'
];
$statusLabels = [
    'pendiente' => 'Pendiente',
    'aprobada' => 'Aprobada',
    'rechazada' => 'Rechazada'
];
?>

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Solicitudes de registro</h2>
        <p class="text-sm text-gray-500">Captura sucursales y profesionistas para revision del administrador.</p>
    </div>
    <a href="<?= url('/solicitudes-registro/crear') ?>"
       class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-5 text-sm font-semibold text-white hover:bg-secondary transition">
        <i class="fas fa-plus mr-2"></i>Nueva solicitud
    </a>
</div>

<?php if (empty($requests)): ?>
<div class="border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
    <i class="fas fa-file-circle-plus mb-4 text-5xl text-gray-300"></i>
    <h3 class="text-lg font-semibold text-gray-700">Aun no hay solicitudes</h3>
    <p class="mt-1 text-sm text-gray-500">Registra una sucursal junto con sus profesionistas para comenzar.</p>
</div>
<?php else: ?>
<div class="overflow-hidden border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Solicitud</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Sucursales</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Profesionistas</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Estado</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Accion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($requests as $request): ?>
                <tr class="hover:bg-gray-50">
                    <td class="whitespace-nowrap px-5 py-4">
                        <p class="font-semibold text-gray-800">#<?= (int) $request['id'] ?></p>
                        <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></p>
                    </td>
                    <td class="px-5 py-4">
                        <?php $branchNames = array_filter(explode('|||', (string) $request['sucursales_nombres'])); ?>
                        <p class="font-medium text-gray-800"><?= e(implode(', ', $branchNames)) ?></p>
                        <p class="text-sm text-gray-500"><?= (int) $request['total_sucursales'] ?> sucursal<?= (int) $request['total_sucursales'] === 1 ? '' : 'es' ?></p>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                        <i class="fas fa-user-doctor mr-2 text-gray-400"></i><?= (int) $request['total_profesionistas'] ?>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $statusStyles[$request['estado']] ?? 'bg-gray-100 text-gray-700' ?>">
                            <?= $statusLabels[$request['estado']] ?? e($request['estado']) ?>
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-right">
                        <a href="<?= url('/solicitudes-registro/ver?id=' . $request['id']) ?>"
                           class="inline-flex h-9 w-9 items-center justify-center text-primary hover:text-secondary"
                           title="Ver solicitud">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
