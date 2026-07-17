<?php
$landing = $landingStatus['landing'];
$isPublished = !empty($landingStatus['public_url']);
$baseParts = parse_url(BASE_URL);
$landingBaseUrl = ($baseParts['scheme'] ?? 'https') . '://' . ($baseParts['host'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost'));
if (!empty($baseParts['port'])) $landingBaseUrl .= ':' . $baseParts['port'];
$landingBaseUrl .= '/';
?>

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="<?= url('/dashboard') ?>" class="mb-2 inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al dashboard</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Mi landing</h2>
            <p class="mt-1 text-sm text-gray-500">P&aacute;gina p&uacute;blica de reservas</p>
        </div>

        <?php if ($isPublished): ?>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="copyLandingUrl(this)"
                    class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-copy"></i>
                <span>Copiar liga</span>
            </button>
            <a href="<?= e($landingStatus['public_url']) ?>" target="_blank" rel="noopener noreferrer"
               class="inline-flex h-10 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-semibold text-white hover:bg-secondary">
                <i class="fas fa-arrow-up-right-from-square"></i>
                <span>Ver landing</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!$landingStatus['available']): ?>
    <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900">
        <i class="fas fa-plug-circle-exclamation mt-0.5"></i>
        <div>
            <p class="font-semibold">Conexi&oacute;n pendiente</p>
            <p class="mt-1 text-sm"><?= e($landingStatus['message']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

        <section class="border-b border-gray-200 p-5 sm:p-7">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center bg-blue-50 text-blue-700">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Presentaci&oacute;n</h3>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="nombre" class="mb-1.5 block text-sm font-semibold text-gray-700">Nombre visible *</label>
                    <input id="nombre" name="nombre" type="text" maxlength="200" required value="<?= e($form['nombre']) ?>"
                           class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="titulo_pagina" class="mb-1.5 block text-sm font-semibold text-gray-700">T&iacute;tulo del navegador</label>
                    <input id="titulo_pagina" name="titulo_pagina" type="text" maxlength="200" value="<?= e($form['titulo_pagina']) ?>"
                           class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="md:col-span-2">
                    <label for="slug" class="mb-1.5 block text-sm font-semibold text-gray-700">Nombre de la carpeta y URL *</label>
                    <div class="flex overflow-hidden rounded-lg border border-gray-300 bg-white focus-within:border-primary focus-within:ring-2 focus-within:ring-blue-100">
                        <span class="hidden items-center border-r border-gray-200 bg-gray-50 px-3 text-sm text-gray-500 sm:flex"><?= e($landingBaseUrl) ?></span>
                        <input id="slug" name="slug" type="text" maxlength="100" required value="<?= e($form['slug']) ?>"
                               spellcheck="false" autocomplete="off" inputmode="url"
                               class="h-11 min-w-0 flex-1 border-0 px-3 text-sm focus:outline-none focus:ring-0">
                        <span class="flex items-center pr-3 text-sm text-gray-400">/</span>
                    </div>
                    <p class="mt-1.5 break-all text-xs text-gray-500 sm:hidden"><?= e($landingBaseUrl) ?><span id="slugPreviewMobile"><?= e($form['slug']) ?></span>/</p>
                </div>
                <div class="md:col-span-2">
                    <label for="texto_encabezado" class="mb-1.5 block text-sm font-semibold text-gray-700">Profesi&oacute;n o especialidad</label>
                    <input id="texto_encabezado" name="texto_encabezado" type="text" maxlength="300" value="<?= e($form['texto_encabezado']) ?>"
                           class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
        </section>

        <section class="border-b border-gray-200 p-5 sm:p-7">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center bg-emerald-50 text-emerald-700">
                    <i class="fas fa-image"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Logo e identidad</h3>
            </div>

            <div class="grid gap-6 lg:grid-cols-[180px_1fr]">
                <div class="flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-dashed border-gray-300 bg-gray-50">
                    <?php if (!empty($landingStatus['logo_url'])): ?>
                    <img src="<?= e($landingStatus['logo_url']) ?>" alt="Logo actual" class="h-full w-full object-contain p-3">
                    <?php else: ?>
                    <i class="fas fa-image text-4xl text-gray-300"></i>
                    <?php endif; ?>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="logo_file" class="mb-1.5 block text-sm font-semibold text-gray-700">Subir logo</label>
                        <input id="logo_file" name="logo_file" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                               class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                    <div class="md:col-span-2">
                        <label for="logo_url" class="mb-1.5 block text-sm font-semibold text-gray-700">O usar URL del logo</label>
                        <input id="logo_url" name="logo_url" type="url" maxlength="500" value="<?= e($form['logo_url']) ?>" placeholder="https://..."
                               class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="color_primario" class="mb-1.5 block text-sm font-semibold text-gray-700">Color principal</label>
                        <div class="flex h-11 overflow-hidden rounded-lg border border-gray-300 bg-white">
                            <input id="color_primario" name="color_primario" type="color" value="<?= e($form['color_primario']) ?>"
                                   class="h-full w-14 cursor-pointer border-0 bg-transparent p-1">
                            <span id="primaryColorValue" class="flex items-center px-3 text-sm text-gray-600"><?= e($form['color_primario']) ?></span>
                        </div>
                    </div>
                    <div>
                        <label for="color_fondo" class="mb-1.5 block text-sm font-semibold text-gray-700">Color de fondo</label>
                        <div class="flex h-11 overflow-hidden rounded-lg border border-gray-300 bg-white">
                            <input id="color_fondo" name="color_fondo" type="color" value="<?= e($form['color_fondo']) ?>"
                                   class="h-full w-14 cursor-pointer border-0 bg-transparent p-1">
                            <span id="backgroundColorValue" class="flex items-center px-3 text-sm text-gray-600"><?= e($form['color_fondo']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-gray-200 p-5 sm:p-7">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center bg-pink-50 text-pink-700">
                    <i class="fas fa-share-nodes"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Redes sociales</h3>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="instagram_url" class="mb-1.5 block text-sm font-semibold text-gray-700">
                        <i class="fab fa-instagram mr-1 text-pink-600"></i> Instagram
                    </label>
                    <input id="instagram_url" name="instagram_url" type="url" maxlength="500" value="<?= e($form['instagram_url']) ?>" placeholder="https://instagram.com/..."
                           class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="facebook_url" class="mb-1.5 block text-sm font-semibold text-gray-700">
                        <i class="fab fa-facebook mr-1 text-blue-600"></i> Facebook
                    </label>
                    <input id="facebook_url" name="facebook_url" type="url" maxlength="500" value="<?= e($form['facebook_url']) ?>" placeholder="https://facebook.com/..."
                           class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
        </section>

        <section class="p-5 sm:p-7">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center bg-violet-50 text-violet-700">
                    <i class="fas fa-rotate"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Contenido sincronizado</h3>
                    <p class="text-sm text-gray-500">Se actualizar&aacute; al publicar</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="border-l-4 border-blue-500 bg-blue-50 px-4 py-3">
                    <p class="text-2xl font-bold text-blue-900"><?= count($services) ?></p>
                    <p class="text-sm text-blue-700">Servicios</p>
                </div>
                <div class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3">
                    <p class="text-2xl font-bold text-emerald-900"><?= count($branches) ?></p>
                    <p class="text-sm text-emerald-700">Ubicaciones</p>
                </div>
                <div class="border-l-4 border-violet-500 bg-violet-50 px-4 py-3">
                    <p class="text-2xl font-bold text-violet-900"><?= count($schedules) ?></p>
                    <p class="text-sm text-violet-700">Horarios</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <?php foreach ($branchContent as $group):
                    $branch = $group['branch'];
                    $branchId = (int)$branch['id'];
                    $address = trim(implode(', ', array_filter([
                        $branch['direccion'] ?? '',
                        $branch['ciudad'] ?? '',
                        $branch['estado'] ?? '',
                    ])));
                ?>
                <article class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <h4 class="font-semibold text-gray-900"><?= e($branch['nombre']) ?></h4>
                            <?php if ($address !== ''): ?>
                            <p class="mt-0.5 truncate text-xs text-gray-500"><?= e($address) ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">
                            <?= count($group['services']) ?> servicios &middot; <?= count($group['schedules']) ?> horarios
                        </span>
                    </div>

                    <div class="space-y-5 p-4">
                        <div>
                            <label for="ubicacion_url_<?= $branchId ?>" class="mb-1.5 block text-sm font-semibold text-gray-700">
                                <i class="fas fa-location-dot mr-1 text-red-500"></i>
                                Liga de ubicaci&oacute;n
                            </label>
                            <input id="ubicacion_url_<?= $branchId ?>" name="ubicacion_url[<?= $branchId ?>]" type="url" maxlength="500"
                                   value="<?= e($form['ubicacion_urls'][$branchId] ?? $group['location_url']) ?>"
                                   placeholder="https://maps.google.com/..."
                                   class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <h5 class="mb-2 text-sm font-semibold text-gray-800">Servicios y precios</h5>
                                <?php if ($group['services']): ?>
                                <div class="divide-y divide-gray-100 rounded-lg border border-gray-200">
                                    <?php foreach ($group['services'] as $service):
                                        $details = [];
                                        if ((float)($service['precio'] ?? 0) > 0) $details[] = '$' . number_format((float)$service['precio'], 2);
                                        if ((int)($service['duracion'] ?? 0) > 0) $details[] = (int)$service['duracion'] . ' min';
                                    ?>
                                    <div class="flex items-start justify-between gap-3 px-3 py-2 text-sm">
                                        <span class="min-w-0 text-gray-700"><?= e($service['nombre']) ?></span>
                                        <span class="flex-shrink-0 font-semibold text-gray-900"><?= e(implode(' / ', $details)) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="rounded-lg border border-dashed border-gray-200 px-3 py-3 text-sm text-gray-500">Sin servicios visibles para esta sucursal.</p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h5 class="mb-2 text-sm font-semibold text-gray-800">Horarios de atenci&oacute;n</h5>
                                <?php if ($group['schedules']): ?>
                                <div class="divide-y divide-gray-100 rounded-lg border border-gray-200">
                                    <?php foreach ($group['schedules'] as $schedule): ?>
                                    <div class="flex items-start justify-between gap-3 px-3 py-2 text-sm">
                                        <span class="text-gray-700"><?= e($schedule['etiqueta']) ?></span>
                                        <span class="flex-shrink-0 font-semibold text-gray-900"><?= e($schedule['horario']) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="rounded-lg border border-dashed border-gray-200 px-3 py-3 text-sm text-gray-500">Sin horarios configurados para esta sucursal.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
            <a href="<?= url('/dashboard') ?>" class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                Cancelar
            </a>
            <button type="submit" <?= !$landingStatus['available'] ? 'disabled' : '' ?>
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 text-sm font-semibold text-white hover:bg-secondary disabled:cursor-not-allowed disabled:bg-gray-300">
                <i class="fas fa-cloud-arrow-up"></i>
                <span><?= $landing ? 'Guardar y publicar' : 'Crear y publicar' ?></span>
            </button>
        </div>
    </form>

    <?php if ($landing): ?>
    <section class="mt-6 rounded-lg border border-red-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-semibold text-red-800">Eliminar landing</h3>
                <p class="mt-1 text-sm text-gray-600">Se borrar&aacute;n la configuraci&oacute;n, las im&aacute;genes y la carpeta p&uacute;blica <strong>/<?= e($landingStatus['slug']) ?>/</strong>.</p>
            </div>
            <form method="post" action="<?= url('/mi-landing/eliminar') ?>"
                  onsubmit="return confirm('¿Eliminar definitivamente esta landing, sus imágenes y toda su carpeta pública? Esta acción no se puede deshacer.');">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-5 text-sm font-semibold text-red-700 hover:bg-red-50">
                    <i class="fas fa-trash-can"></i>
                    <span>Eliminar landing</span>
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
const landingPublicUrl = <?= json_encode($landingStatus['public_url'], JSON_UNESCAPED_SLASHES) ?>;
const slugInput = document.getElementById('slug');

function normalizeLandingSlug(value) {
    return value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 100);
}

slugInput.addEventListener('input', function() {
    const normalized = normalizeLandingSlug(this.value);
    if (this.value !== normalized) this.value = normalized;
    const mobilePreview = document.getElementById('slugPreviewMobile');
    if (mobilePreview) mobilePreview.textContent = normalized;
});

function copyLandingUrl(button) {
    if (!landingPublicUrl) return;
    navigator.clipboard.writeText(landingPublicUrl).then(function() {
        const original = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-green-600"></i><span>Copiada</span>';
        setTimeout(function() { button.innerHTML = original; }, 1600);
    });
}

document.getElementById('color_primario').addEventListener('input', function() {
    document.getElementById('primaryColorValue').textContent = this.value;
});
document.getElementById('color_fondo').addEventListener('input', function() {
    document.getElementById('backgroundColorValue').textContent = this.value;
});
</script>
