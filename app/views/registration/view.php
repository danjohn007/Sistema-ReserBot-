<?php
$statusStyles = [
    'pendiente' => 'bg-amber-100 text-amber-800',
    'aprobada' => 'bg-green-100 text-green-800',
    'rechazada' => 'bg-red-100 text-red-800'
];
$statusLabels = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];
?>

<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <a href="<?= url('/solicitudes-registro') ?>" class="mb-2 inline-flex items-center text-sm text-gray-500 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a solicitudes
        </a>
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="text-2xl font-bold text-gray-800">Solicitud #<?= (int) $request['id'] ?></h2>
            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $statusStyles[$request['estado']] ?? 'bg-gray-100 text-gray-700' ?>">
                <?= $statusLabels[$request['estado']] ?? e($request['estado']) ?>
            </span>
        </div>
        <p class="mt-1 text-sm text-gray-500">Enviada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($request['created_at'])) ?></p>
    </div>

    <?php if ($request['estado'] === 'pendiente'): ?>
    <div class="mb-6 border-l-4 border-amber-400 bg-amber-50 p-4 text-sm text-amber-800">
        <i class="fas fa-clock mr-2"></i>Las sucursales, los profesionistas y sus accesos permanecen inactivos hasta la autorizacion conjunta.
    </div>
    <?php elseif ($request['estado'] === 'rechazada' && !empty($request['motivo_rechazo'])): ?>
    <div class="mb-6 border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Motivo del rechazo</p>
        <p class="mt-1"><?= nl2br(e($request['motivo_rechazo'])) ?></p>
    </div>
    <?php endif; ?>

    <section class="mb-7">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Sucursales (<?= count($branches) ?>)</h3>
        <div class="space-y-4">
            <?php foreach ($branches as $branch): ?>
            <article class="border border-gray-200 bg-white p-5 sm:p-6">
                <div class="mb-4 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <i class="fas fa-building text-xl text-blue-600"></i>
                    <h4 class="font-semibold text-gray-800"><?= e($branch['nombre']) ?></h4>
                </div>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <?php if (!empty($branch['email'])): ?>
                    <div><dt class="text-gray-500">Correo</dt><dd class="mt-1 text-gray-800"><?= e($branch['email']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($branch['telefono'])): ?>
                    <div><dt class="text-gray-500">Telefono</dt><dd class="mt-1 text-gray-800"><?= e($branch['telefono']) ?></dd></div>
                    <?php endif; ?>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Direccion</dt><dd class="mt-1 text-gray-800"><?= e($branch['direccion']) ?></dd></div>
                    <div><dt class="text-gray-500">Ubicacion</dt><dd class="mt-1 text-gray-800"><?= e(trim($branch['ciudad'] . ', ' . $branch['estado'] . ' ' . $branch['codigo_postal'])) ?></dd></div>
                </dl>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Profesionistas (<?= count($professionals) ?>)</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <?php foreach ($professionals as $professional): ?>
            <article class="border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-start gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-green-100 font-bold text-green-700">
                        <?= strtoupper(mb_substr($professional['nombre'], 0, 1, 'UTF-8')) ?>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-semibold text-gray-800"><?= e($professional['nombre'] . ' ' . $professional['apellidos']) ?></h4>
                        <p class="text-sm text-gray-500"><?= e($professional['profesion']) ?><?= $professional['especialidad'] ? ' - ' . e($professional['especialidad']) : '' ?></p>
                    </div>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><i class="fas fa-building mr-2 w-4 text-gray-400"></i><?= e(str_replace('|||', ', ', $professional['sucursales_nombres'])) ?></p>
                    <p><i class="fas fa-envelope mr-2 w-4 text-gray-400"></i><?= e($professional['email']) ?></p>
                    <?php if (!empty($professional['telefono'])): ?>
                    <p><i class="fas fa-phone mr-2 w-4 text-gray-400"></i><?= e($professional['telefono']) ?></p>
                    <?php endif; ?>
                    <p><i class="fas fa-briefcase mr-2 w-4 text-gray-400"></i><?= (int) $professional['experiencia_anos'] ?> anos de experiencia</p>
                    <?php if ($professional['tarifa_base'] !== null): ?>
                    <p><i class="fas fa-dollar-sign mr-2 w-4 text-gray-400"></i><?= formatMoney($professional['tarifa_base']) ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($professional['descripcion'])): ?>
                <p class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-600"><?= nl2br(e($professional['descripcion'])) ?></p>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>
