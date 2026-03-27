<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Especialistas</h2>
        <p class="text-gray-500 text-sm">Gestiona los especialistas del sistema</p>
    </div>
    <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
    <a href="<?= url('/especialistas/crear') ?>" 
       class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>Nuevo Especialista
    </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($specialists as $spec): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold">
                        <?= strtoupper(substr($spec['nombre'], 0, 1) . substr($spec['apellidos'], 0, 1)) ?>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= e($spec['nombre'] . ' ' . $spec['apellidos']) ?></h3>
                        <p class="text-sm text-gray-500"><?= e($spec['profesion']) ?></p>
                        <?php if ($spec['especialidad']): ?>
                        <p class="text-xs text-primary"><?= e($spec['especialidad']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <?php 
                $sucursales = !empty($spec['sucursales_nombres']) ? explode('|', $spec['sucursales_nombres']) : [];
                $totalSucursales = count($sucursales);
                ?>
                
                <?php if ($totalSucursales > 0): ?>
                <div class="flex items-start">
                    <i class="fas fa-building w-5 mt-0.5"></i>
                    <div class="flex-1">
                        <?php if ($totalSucursales == 1): ?>
                            <?= e($sucursales[0]) ?>
                        <?php else: ?>
                            <span><?= e($sucursales[0]) ?></span>
                            <span class="relative group ml-1">
                                <span class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium">ver más</span>
                                <div class="hidden group-hover:block absolute left-0 top-5 bg-white border border-gray-200 rounded-lg shadow-lg p-3 z-10 min-w-[200px]">
                                    <div class="text-xs font-semibold text-gray-500 mb-2">Sucursales (<?= $totalSucursales ?>):</div>
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <div class="py-1 text-gray-700">&bull; <?= e($sucursal) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <p><i class="fas fa-envelope w-5"></i><?= e($spec['email']) ?></p>
                <?php if ($spec['telefono']): ?>
                <p><i class="fas fa-phone w-5"></i><?= e($spec['telefono']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center text-yellow-500">
                    <i class="fas fa-star mr-1"></i>
                    <span class="font-medium"><?= number_format($spec['calificacion_promedio'], 1) ?></span>
                    <span class="text-gray-400 text-sm ml-1">(<?= $spec['total_resenas'] ?>)</span>
                </div>
                <span class="text-sm text-gray-500"><?= $spec['experiencia_anos'] ?> a&ntilde;os exp.</span>
            </div>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 flex justify-between">
            <a href="<?= url('/especialistas/perfil?id=' . $spec['id']) ?>" 
               class="text-gray-600 hover:text-gray-800 text-sm">
                <i class="fas fa-user"></i> Perfil
            </a>
            <div class="space-x-2 flex flex-wrap items-center">
                <button type="button" onclick='copiarLiga(<?= json_encode(getWhatsAppUrl("Hola quiero reservar con " . $spec["nombre"] . " " . $spec["apellidos"])) ?>, this)'
                        class="text-green-600 hover:text-green-800 text-sm whitespace-nowrap cursor-pointer"
                        title="Copiar Liga 1">
                    <i class="fab fa-whatsapp"></i> Liga 1
                </button>
                <button type="button" onclick='copiarLiga(<?= json_encode(getWhatsAppUrl("Hola me gustaria reservar con " . $spec["nombre"] . " " . $spec["apellidos"])) ?>, this)'
                        class="text-green-500 hover:text-green-700 text-sm whitespace-nowrap cursor-pointer"
                        title="Copiar Liga 2">
                    <i class="fab fa-whatsapp"></i> Liga 2
                </button>
                <button type="button" onclick='copiarLiga(<?= json_encode(getWhatsAppUrl("Hola deseo reservar con " . $spec["nombre"] . " " . $spec["apellidos"])) ?>, this)'
                        class="text-emerald-600 hover:text-emerald-800 text-sm whitespace-nowrap cursor-pointer"
                        title="Copiar Liga 3">
                    <i class="fab fa-whatsapp"></i> Liga 3
                </button>
                <a href="<?= url('/especialistas/horarios?id=' . $spec['id']) ?>" 
                   class="text-blue-600 hover:text-blue-800 text-sm whitespace-nowrap">
                    <i class="fas fa-clock"></i> Horarios
                </a>
                <a href="<?= url('/especialistas/editar?id=' . $spec['id']) ?>" 
                   class="text-blue-600 hover:text-blue-800 text-sm whitespace-nowrap">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
                <a href="<?= url('/especialistas/eliminar?id=' . $spec['id']) ?>" 
                   class="text-red-600 hover:text-red-800 text-sm whitespace-nowrap"
                   onclick="return confirm('&iquest;Est&aacute;s seguro de eliminar a este especialista? Esta acci&oacute;n no se puede deshacer.')">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($specialists)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay especialistas</h3>
    <p class="text-gray-500 mt-2">Comienza agregando el primer especialista</p>
</div>
<?php endif; ?>

<script>
function copiarLiga(url, btn) {
    navigator.clipboard.writeText(url).then(function() {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.classList.add('text-white', 'bg-green-500', 'px-2', 'rounded');
        setTimeout(function() {
            btn.innerHTML = original;
            btn.classList.remove('text-white', 'bg-green-500', 'px-2', 'rounded');
        }, 1800);
    }).catch(function() {
        // Fallback: seleccionar texto
        const ta = document.createElement('textarea');
        ta.value = url;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        setTimeout(function() { btn.innerHTML = original; }, 1800);
    });
}
</script>
