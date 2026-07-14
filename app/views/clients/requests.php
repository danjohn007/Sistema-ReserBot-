<?php
$statusStyles = [
    'pendiente' => 'bg-amber-100 text-amber-800',
    'aprobada' => 'bg-green-100 text-green-800',
    'rechazada' => 'bg-red-100 text-red-800'
];
$statusLabels = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];
$filters = [
    'pendiente' => 'Pendientes',
    'aprobada' => 'Aprobadas',
    'rechazada' => 'Rechazadas',
    'todos' => 'Todas'
];
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Solicitudes de registro</h2>
    <p class="text-sm text-gray-500">Autoriza sucursales y profesionistas en una sola revision.</p>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="border-l-4 border-amber-400 bg-white px-5 py-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Pendientes</p>
        <p class="mt-1 text-2xl font-bold text-gray-800"><?= (int) $counts['pendiente'] ?></p>
    </div>
    <div class="border-l-4 border-green-500 bg-white px-5 py-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Aprobadas</p>
        <p class="mt-1 text-2xl font-bold text-gray-800"><?= (int) $counts['aprobada'] ?></p>
    </div>
    <div class="border-l-4 border-red-500 bg-white px-5 py-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Rechazadas</p>
        <p class="mt-1 text-2xl font-bold text-gray-800"><?= (int) $counts['rechazada'] ?></p>
    </div>
</div>

<div class="mb-6 border border-gray-200 bg-white p-4">
    <form method="GET" action="<?= url('/clientes') ?>" class="flex flex-col gap-3 lg:flex-row lg:items-end">
        <div class="min-w-0 flex-1">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Buscar</label>
            <input type="search" name="search" value="<?= e($search) ?>"
                   placeholder="Sucursal, profesionista o correo"
                   class="h-11 w-full rounded-lg border border-gray-300 px-4 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div class="w-full lg:w-48">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Estado</label>
            <select name="estado" class="h-11 w-full rounded-lg border border-gray-300 px-3 focus:border-primary focus:ring-2 focus:ring-blue-100">
                <?php foreach ($filters as $value => $label): ?>
                <option value="<?= $value ?>" <?= $status === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-5 text-sm font-semibold text-white hover:bg-secondary transition">
            <i class="fas fa-search mr-2"></i>Buscar
        </button>
        <?php if ($search !== '' || $status !== 'pendiente'): ?>
        <a href="<?= url('/clientes') ?>" class="inline-flex h-11 items-center justify-center px-3 text-sm font-medium text-gray-600 hover:text-gray-900">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($requests)): ?>
<div class="border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
    <i class="fas fa-clipboard-check mb-4 text-5xl text-gray-300"></i>
    <h3 class="text-lg font-semibold text-gray-700">No hay solicitudes con estos filtros</h3>
    <p class="mt-1 text-sm text-gray-500">Las nuevas capturas apareceran aqui para su revision.</p>
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
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Revision</th>
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
                        <?php if ($request['fecha_revision']): ?>
                        <p class="mt-2 text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($request['fecha_revision'])) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-right">
                        <a href="<?= url('/clientes/solicitud?id=' . $request['id']) ?>"
                           class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-sm font-semibold <?= $request['estado'] === 'pendiente' ? 'bg-primary text-white hover:bg-secondary' : 'text-primary hover:bg-blue-50' ?> transition">
                            <i class="fas fa-eye mr-2"></i><?= $request['estado'] === 'pendiente' ? 'Revisar' : 'Ver' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
