<?php
$statusStyles = [
    'pendiente' => 'bg-amber-100 text-amber-800',
    'aprobada' => 'bg-green-100 text-green-800',
    'rechazada' => 'bg-red-100 text-red-800'
];
$statusLabels = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];
?>

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <a href="<?= url('/clientes') ?>" class="mb-2 inline-flex items-center text-sm text-gray-500 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Volver a solicitudes
            </a>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">Solicitud #<?= (int) $request['id'] ?></h2>
                <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $statusStyles[$request['estado']] ?? 'bg-gray-100 text-gray-700' ?>">
                    <?= $statusLabels[$request['estado']] ?? e($request['estado']) ?>
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">Capturada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($request['created_at'])) ?></p>
        </div>

        <?php if ($request['estado'] === 'pendiente'): ?>
        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="button" onclick="openRejectModal()"
                    class="inline-flex h-11 items-center justify-center rounded-lg border border-red-300 px-5 text-sm font-semibold text-red-700 hover:bg-red-50 transition">
                <i class="fas fa-times mr-2"></i>Rechazar solicitud
            </button>
            <form method="POST" action="<?= url('/clientes/solicitud/aprobar') ?>"
                  onsubmit="return confirm('Se activaran todas las sucursales y sus profesionistas. Deseas aprobar la solicitud completa?');">
                <input type="hidden" name="id" value="<?= (int) $request['id'] ?>">
                <button type="submit"
                        class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-green-600 px-5 text-sm font-semibold text-white hover:bg-green-700 transition">
                    <i class="fas fa-check-double mr-2"></i>Aprobar todo
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($request['estado'] === 'pendiente'): ?>
    <div class="mb-6 border-l-4 border-amber-400 bg-amber-50 p-4 text-sm text-amber-800">
        <strong>Revision conjunta:</strong> esta accion autorizara <?= count($branches) ?> sucursal<?= count($branches) === 1 ? '' : 'es' ?>, <?= count($professionals) ?> profesionista<?= count($professionals) === 1 ? '' : 's' ?> y sus accesos en una sola operacion.
    </div>
    <?php elseif ($request['estado'] === 'rechazada'): ?>
    <div class="mb-6 border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Motivo del rechazo</p>
        <p class="mt-1"><?= nl2br(e($request['motivo_rechazo'])) ?></p>
    </div>
    <?php else: ?>
    <div class="mb-6 border-l-4 border-green-500 bg-green-50 p-4 text-sm text-green-800">
        <i class="fas fa-circle-check mr-2"></i>Sucursales, profesionistas y accesos autorizados correctamente.
    </div>
    <?php endif; ?>

    <?php if ($request['fecha_revision']): ?>
    <p class="mb-5 text-sm text-gray-500">
        Revisada por <?= e($request['revisado_por_nombre'] ?: 'Administrador') ?> el <?= date('d/m/Y \a \l\a\s H:i', strtotime($request['fecha_revision'])) ?>
    </p>
    <?php endif; ?>

    <section class="mb-7">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Sucursales incluidas (<?= count($branches) ?>)</h3>
        <div class="space-y-4">
            <?php foreach ($branches as $branch): ?>
            <article class="border border-gray-200 bg-white p-5 sm:p-6">
                <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-building text-xl text-blue-600"></i>
                        <div>
                            <h4 class="font-semibold text-gray-800"><?= e($branch['nombre']) ?></h4>
                            <p class="text-xs text-gray-500">Se activara junto con toda la solicitud.</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold <?= $branch['autorizado'] ? 'text-green-700' : 'text-amber-700' ?>">
                        <?= $branch['autorizado'] ? 'Autorizada' : 'Sin autorizar' ?>
                    </span>
                </div>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div><dt class="text-gray-500">Correo</dt><dd class="mt-1 text-gray-800"><?= e($branch['email']) ?></dd></div>
                    <div><dt class="text-gray-500">Telefono</dt><dd class="mt-1 text-gray-800"><?= e($branch['telefono']) ?></dd></div>
                    <div><dt class="text-gray-500">Horario</dt><dd class="mt-1 text-gray-800"><?= formatTime($branch['horario_apertura']) ?> - <?= formatTime($branch['horario_cierre']) ?></dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Direccion</dt><dd class="mt-1 text-gray-800"><?= e($branch['direccion']) ?></dd></div>
                    <div><dt class="text-gray-500">Ubicacion</dt><dd class="mt-1 text-gray-800"><?= e(trim($branch['ciudad'] . ', ' . $branch['estado'] . ' ' . $branch['codigo_postal'])) ?></dd></div>
                </dl>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Profesionistas incluidos (<?= count($professionals) ?>)</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <?php foreach ($professionals as $professional): ?>
            <article class="border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-green-100 font-bold text-green-700">
                            <?= strtoupper(mb_substr($professional['nombre'], 0, 1, 'UTF-8')) ?>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-semibold text-gray-800"><?= e($professional['nombre'] . ' ' . $professional['apellidos']) ?></h4>
                            <p class="text-sm text-gray-500"><?= e($professional['profesion']) ?><?= $professional['especialidad'] ? ' - ' . e($professional['especialidad']) : '' ?></p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold <?= $professional['autorizado'] ? 'text-green-700' : 'text-amber-700' ?>">
                        <?= $professional['autorizado'] ? 'Autorizado' : 'Pendiente' ?>
                    </span>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><i class="fas fa-building mr-2 w-4 text-gray-400"></i><?= e(str_replace('|||', ', ', $professional['sucursales_nombres'])) ?></p>
                    <p><i class="fas fa-envelope mr-2 w-4 text-gray-400"></i><?= e($professional['email']) ?></p>
                    <p><i class="fas fa-phone mr-2 w-4 text-gray-400"></i><?= e($professional['telefono']) ?></p>
                    <p><i class="fas fa-briefcase mr-2 w-4 text-gray-400"></i><?= (int) $professional['experiencia_anos'] ?> anos de experiencia</p>
                    <p><i class="fas fa-dollar-sign mr-2 w-4 text-gray-400"></i><?= formatMoney($professional['tarifa_base']) ?></p>
                </div>
                <?php if (!empty($professional['descripcion'])): ?>
                <p class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-600"><?= nl2br(e($professional['descripcion'])) ?></p>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php if ($request['estado'] === 'pendiente'): ?>
<div id="rejectRequestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="w-full max-w-lg rounded-lg bg-white shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h3 class="font-semibold text-gray-800">Rechazar solicitud completa</h3>
            <button type="button" onclick="closeRejectModal()" class="flex h-9 w-9 items-center justify-center text-gray-500 hover:text-gray-800" aria-label="Cerrar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="<?= url('/clientes/solicitud/rechazar') ?>" class="p-5">
            <input type="hidden" name="id" value="<?= (int) $request['id'] ?>">
            <label for="motivo_rechazo" class="mb-2 block text-sm font-medium text-gray-700">Motivo del rechazo *</label>
            <textarea id="motivo_rechazo" name="motivo_rechazo" required rows="4" maxlength="2000"
                      placeholder="Indica que informacion debe corregirse antes de volver a registrar."
                      class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-100"></textarea>
            <p class="mt-2 text-xs text-gray-500">Todas las sucursales y profesionistas permaneceran inactivos.</p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="h-10 px-4 text-sm font-semibold text-gray-600 hover:text-gray-900">Cancelar</button>
                <button type="submit" class="h-10 rounded-lg bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700 transition">
                    <i class="fas fa-times-circle mr-2"></i>Rechazar todo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    const modal = document.getElementById('rejectRequestModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    setTimeout(function() { document.getElementById('motivo_rechazo').focus(); }, 0);
}

function closeRejectModal() {
    const modal = document.getElementById('rejectRequestModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

document.getElementById('rejectRequestModal').addEventListener('click', closeRejectModal);
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeRejectModal();
});
</script>
<?php endif; ?>
