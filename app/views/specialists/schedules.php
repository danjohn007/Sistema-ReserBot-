<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/especialistas') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Especialistas
        </a>
    </div>
    
    <div class="grid grid-cols-1 gap-6">
        <!-- Schedule Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Horarios Semanales - <?= e($specialist['nombre'] . ' ' . $specialist['apellidos']) ?></h2>
            
            <?php if (count($allSpecialists) > 1): ?>
            <!-- Tabs para múltiples sucursales -->
            <style>
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                .hide-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>
            <div class="mb-6 border-b border-gray-200 overflow-x-auto hide-scrollbar" style="max-width: 100%;">
                <nav class="-mb-px flex space-x-2" aria-label="Tabs" style="min-width: min-content;">
                    <?php foreach ($allSpecialists as $spec): ?>
                    <a href="<?= url('/especialistas/horarios?specialist_id=' . $spec['id']) ?>" 
                       class="<?= $spec['id'] == $currentSpecialistId ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex-shrink-0">
                        <i class="fas fa-building mr-1 text-xs"></i><?= e($spec['sucursal_nombre']) ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            
            <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Est&aacute;s editando los horarios para: <strong><?= e($specialist['sucursal_nombre']) ?></strong>
                </p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= url('/especialistas/horarios') ?>">
                <input type="hidden" name="action" value="save_schedule">
                <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700 border-b">D&iacute;a</th>
                                <th class="px-2 py-3 text-center text-sm font-semibold text-gray-700 border-b w-16">Activo</th>
                                <th class="px-2 py-3 text-left text-sm font-semibold text-gray-700 border-b w-28">Hora Inicio</th>
                                <th class="px-2 py-3 text-left text-sm font-semibold text-gray-700 border-b w-28">Hora Fin</th>
                                <th class="px-2 py-3 text-center text-sm font-semibold text-gray-700 border-b w-20">Bloquear</th>
                                <th class="px-2 py-3 text-left text-sm font-semibold text-gray-700 border-b">Horario Bloqueo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daysOfWeek as $dayNum => $dayName): ?>
                            <?php $daySchedule = $schedules[$dayNum] ?? null; ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-3">
                                    <span class="font-medium text-gray-700"><?= $dayName ?></span>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <input type="checkbox" name="activo_<?= $dayNum ?>" value="1" 
                                           <?= $daySchedule ? 'checked' : '' ?>
                                           class="rounded border-gray-300 text-primary focus:ring-primary w-5 h-5"
                                           onchange="toggleDay(<?= $dayNum ?>)">
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="hora_inicio_<?= $dayNum ?>" 
                                           id="hora_inicio_<?= $dayNum ?>"
                                           value="<?= $daySchedule ? substr($daySchedule['hora_inicio'], 0, 5) : '09:00' ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="hora_fin_<?= $dayNum ?>" 
                                           id="hora_fin_<?= $dayNum ?>"
                                           value="<?= $daySchedule ? substr($daySchedule['hora_fin'], 0, 5) : '18:00' ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <input type="checkbox" name="bloqueo_activo_<?= $dayNum ?>" value="1" 
                                           <?= ($daySchedule && $daySchedule['bloqueo_activo']) ? 'checked' : '' ?>
                                           class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 w-5 h-5"
                                           onchange="toggleBlock(<?= $dayNum ?>)">
                                </td>
                                <td class="px-2 py-3">
                                    <div id="block_<?= $dayNum ?>_times" class="<?= ($daySchedule && $daySchedule['bloqueo_activo']) ? '' : 'hidden' ?>">
                                        <div class="flex items-center gap-2 bg-orange-50 border border-orange-200 rounded px-2 py-1">
                                            <input type="time" name="hora_inicio_bloqueo_<?= $dayNum ?>" 
                                                   value="<?= ($daySchedule && $daySchedule['hora_inicio_bloqueo']) ? substr($daySchedule['hora_inicio_bloqueo'], 0, 5) : '13:00' ?>"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-xs">
                                            <span class="text-xs text-gray-500">a</span>
                                            <input type="time" name="hora_fin_bloqueo_<?= $dayNum ?>" 
                                                   value="<?= ($daySchedule && $daySchedule['hora_fin_bloqueo']) ? substr($daySchedule['hora_fin_bloqueo'], 0, 5) : '14:00' ?>"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-xs">
                                        </div>
                                    </div>
                                    <div id="block_<?= $dayNum ?>_placeholder" class="<?= ($daySchedule && $daySchedule['bloqueo_activo']) ? 'hidden' : '' ?>">
                                        <span class="text-xs text-gray-400 italic">Sin bloqueo</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Activo:</strong> Marca los d&iacute;as que trabajas. 
                        <strong>Bloquear:</strong> Define horas de descanso (ej: comida).
                    </p>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-save mr-2"></i>Guardar Horarios
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Blocks -->
        <!-- FUNCIONALIDAD COMENTADA: Se mantiene código por si se requiere en el futuro
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Bloqueos de Horario</h2>
                <button onclick="document.getElementById('blockForm').classList.toggle('hidden')" 
                        class="text-primary hover:text-secondary">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
            
            <div id="blockForm" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
                <form method="POST" action="<?= url('/especialistas/horarios') ?>">
                    <input type="hidden" name="action" value="add_block">
                    <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha/Hora Inicio</label>
                                <input type="datetime-local" name="fecha_inicio" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha/Hora Fin</label>
                                <input type="datetime-local" name="fecha_fin" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="vacaciones">Vacaciones</option>
                                <option value="pausa">Pausa/Descanso</option>
                                <option value="personal">Personal</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                            <input type="text" name="motivo"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   placeholder="Descripción opcional">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                            <i class="fas fa-plus mr-2"></i>Agregar Bloqueo
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if (empty($blocks)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-calendar-check text-4xl mb-3"></i>
                <p>No hay bloqueos programados</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($blocks as $block): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800"><?= ucfirst($block['tipo']) ?></p>
                        <p class="text-sm text-gray-500">
                            <?= formatDate($block['fecha_inicio'], 'd/m/Y H:i') ?> - <?= formatDate($block['fecha_fin'], 'd/m/Y H:i') ?>
                        </p>
                        <?php if ($block['motivo']): ?>
                        <p class="text-xs text-gray-400"><?= e($block['motivo']) ?></p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?= url('/especialistas/horarios') ?>" class="inline">
                        <input type="hidden" name="action" value="delete_block">
                        <input type="hidden" name="specialist_id" value="<?= $currentSpecialistId ?>">
                        <input type="hidden" name="block_id" value="<?= $block['id'] ?>">
                        <button type="submit" class="text-red-500 hover:text-red-700"
                                onclick="return confirm('¿Eliminar este bloqueo?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        -->
    </div>
</div>

<script>
function toggleDay(dayNum) {
    const checkbox = document.querySelector(`input[name="activo_${dayNum}"]`);
    const horaInicio = document.getElementById(`hora_inicio_${dayNum}`);
    const horaFin = document.getElementById(`hora_fin_${dayNum}`);
    
    if (checkbox.checked) {
        horaInicio.disabled = false;
        horaFin.disabled = false;
        horaInicio.classList.remove('opacity-50', 'bg-gray-100');
        horaFin.classList.remove('opacity-50', 'bg-gray-100');
    } else {
        horaInicio.disabled = true;
        horaFin.disabled = true;
        horaInicio.classList.add('opacity-50', 'bg-gray-100');
        horaFin.classList.add('opacity-50', 'bg-gray-100');
    }
}

function toggleBlock(dayNum) {
    const checkbox = document.querySelector(`input[name="bloqueo_activo_${dayNum}"]`);
    const blockTimes = document.getElementById(`block_${dayNum}_times`);
    const placeholder = document.getElementById(`block_${dayNum}_placeholder`);
    
    if (checkbox.checked) {
        blockTimes.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    } else {
        blockTimes.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
    }
}

// Inicializar estado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($daysOfWeek as $dayNum => $dayName): ?>
    toggleDay(<?= $dayNum ?>);
    <?php endforeach; ?>
});
</script>
