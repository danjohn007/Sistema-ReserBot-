<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Sucursales</h2>
        <p class="text-sm text-gray-500">Gestiona las sucursales del sistema</p>
    </div>
    <?php if (hasRole(ROLE_SUPERADMIN)): ?>
    <a href="<?= url('/sucursales/crear') ?>"
       class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-4 text-sm font-semibold text-white transition hover:bg-secondary">
        <i class="fas fa-plus mr-2"></i>Nueva Sucursal
    </a>
    <?php endif; ?>
</div>

<?php if (hasRole(ROLE_SUPERADMIN)): ?>
<div class="mb-6 flex flex-col gap-3 border border-gray-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <p class="text-sm font-semibold text-gray-800">Visibilidad del listado</p>
        <p class="text-xs text-gray-500">Ocultar una sucursal aqui no la desactiva ni afecta sus reservaciones.</p>
    </div>
    <div class="inline-flex w-full rounded-lg border border-gray-300 bg-gray-50 p-1 sm:w-auto" role="tablist" aria-label="Visibilidad de sucursales">
        <a href="<?= url('/sucursales?vista=visibles') ?>"
           class="inline-flex h-9 flex-1 items-center justify-center rounded-md px-4 text-sm font-semibold transition sm:flex-none <?= $visibilityView === 'visibles' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>"
           aria-current="<?= $visibilityView === 'visibles' ? 'page' : 'false' ?>">
            <i class="fas fa-eye mr-2"></i>Visibles
            <span class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700"><?= (int) $visibilityCounts['visibles'] ?></span>
        </a>
        <a href="<?= url('/sucursales?vista=ocultas') ?>"
           class="inline-flex h-9 flex-1 items-center justify-center rounded-md px-4 text-sm font-semibold transition sm:flex-none <?= $visibilityView === 'ocultas' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>"
           aria-current="<?= $visibilityView === 'ocultas' ? 'page' : 'false' ?>">
            <i class="fas fa-eye-slash mr-2"></i>Ocultas
            <span class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700"><?= (int) $visibilityCounts['ocultas'] ?></span>
        </a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($branches)): ?>
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($branches as $branch): ?>
    <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="break-words text-lg font-semibold text-gray-800"><?= e($branch['nombre']) ?></h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <i class="fas fa-map-marker-alt mr-1"></i><?= e($branch['ciudad']) ?>, <?= e($branch['estado']) ?>
                    </p>
                </div>
                <span class="flex-shrink-0 rounded-full px-2 py-1 text-xs <?= $branch['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
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
                <p class="break-all"><i class="fas fa-envelope w-5"></i><?= e($branch['email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($branch['horario_apertura']) || !empty($branch['horario_cierre'])): ?>
                <p><i class="fas fa-clock w-5"></i><?= formatTime($branch['horario_apertura']) ?> - <?= formatTime($branch['horario_cierre']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3 bg-gray-50 px-6 py-3">
            <a href="<?= url('/sucursales/editar?id=' . $branch['id']) ?>"
               class="inline-flex h-9 items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                <i class="fas fa-edit mr-1.5"></i>Editar
            </a>
            <?php if (hasRole(ROLE_SUPERADMIN)): ?>
            <form method="POST" action="<?= url('/sucursales/visibilidad') ?>"
                  onsubmit="return confirm('<?= $visibilityView === 'ocultas' ? 'La sucursal volvera a aparecer en el listado. Continuar?' : 'La sucursal se movera a la vista de ocultas sin desactivarse. Continuar?' ?>');">
                <input type="hidden" name="id" value="<?= (int) $branch['id'] ?>">
                <input type="hidden" name="oculta" value="<?= $visibilityView === 'ocultas' ? 0 : 1 ?>">
                <input type="hidden" name="vista" value="<?= e($visibilityView) ?>">
                <button type="submit"
                        class="inline-flex h-9 items-center text-sm font-medium <?= $visibilityView === 'ocultas' ? 'text-green-700 hover:text-green-900' : 'text-gray-600 hover:text-gray-900' ?>">
                    <i class="fas <?= $visibilityView === 'ocultas' ? 'fa-eye' : 'fa-eye-slash' ?> mr-1.5"></i><?= $visibilityView === 'ocultas' ? 'Mostrar' : 'Ocultar' ?>
                </button>
            </form>
            <a href="<?= url('/sucursales/eliminar?id=' . $branch['id']) ?>"
               class="inline-flex h-9 items-center text-sm font-medium text-red-600 hover:text-red-800"
               onclick="return confirm('Esta accion desactivara la sucursal. Continuar?')">
                <i class="fas fa-trash mr-1.5"></i>Eliminar
            </a>
            <?php endif; ?>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="border border-dashed border-gray-300 bg-white p-12 text-center">
    <i class="fas <?= hasRole(ROLE_SUPERADMIN) && $visibilityView === 'ocultas' ? 'fa-eye-slash' : 'fa-building' ?> mb-4 text-6xl text-gray-300"></i>
    <h3 class="text-xl font-semibold text-gray-700">
        <?= hasRole(ROLE_SUPERADMIN) && $visibilityView === 'ocultas' ? 'No hay sucursales ocultas' : 'No hay sucursales visibles' ?>
    </h3>
    <p class="mt-2 text-gray-500">
        <?= hasRole(ROLE_SUPERADMIN) && $visibilityView === 'ocultas'
            ? 'Las sucursales que ocultes apareceran en esta seccion.'
            : 'Puedes agregar una sucursal nueva o mostrar alguna que este oculta.' ?>
    </p>
    <?php if (hasRole(ROLE_SUPERADMIN) && $visibilityView !== 'ocultas'): ?>
    <a href="<?= url('/sucursales/crear') ?>"
       class="mt-4 inline-flex h-10 items-center rounded-lg bg-primary px-6 text-sm font-semibold text-white transition hover:bg-secondary">
        <i class="fas fa-plus mr-2"></i>Agregar Sucursal
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>
