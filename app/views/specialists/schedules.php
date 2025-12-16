<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/especialistas') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Especialistas
        </a>
    </div>
    
    <div class="grid grid-cols-1 gap-6">
        <!-- Schedule Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl mx-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Horarios Semanales</h2>
            
            <form method="POST" action="<?= url('/especialistas/horarios?id=' . $specialist['id']) ?>">
                <input type="hidden" name="action" value="save_schedule">
                
                <!-- Intervalo de Espacios -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2 text-blue-600"></i>Intervalo de espacios (minutos)
                    </label>
                    <select name="intervalo_espacios" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="30" <?= ($schedules && reset($schedules)['intervalo_espacios'] == 30) ? 'selected' : '' ?>>30 minutos</option>
                        <option value="60" <?= (!$schedules || reset($schedules)['intervalo_espacios'] == 60) ? 'selected' : '' ?>>60 minutos (1 hora)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Define el tiempo entre cada cita disponible</p>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($daysOfWeek as $dayNum => $dayName): ?>
                    <?php $daySchedule = $schedules[$dayNum] ?? null; ?>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="activo_<?= $dayNum ?>" value="1" 
                                       <?= $daySchedule ? 'checked' : '' ?>
                                       class="rounded border-gray-300 text-primary focus:ring-primary"
                                       onchange="toggleDay(<?= $dayNum ?>)">
                                <span class="ml-2 font-medium text-gray-700"><?= $dayName ?></span>
                            </label>
                        </div>
                        
                        <div id="day_<?= $dayNum ?>_times" class="space-y-3 <?= $daySchedule ? '' : 'opacity-50' ?>">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Hora Inicio</label>
                                    <input type="time" name="hora_inicio_<?= $dayNum ?>" 
                                           value="<?= $daySchedule ? substr($daySchedule['hora_inicio'], 0, 5) : '09:00' ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Hora Fin</label>
                                    <input type="time" name="hora_fin_<?= $dayNum ?>" 
                                           value="<?= $daySchedule ? substr($daySchedule['hora_fin'], 0, 5) : '18:00' ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                            </div>
                            
                            <!-- Bloqueo de Horario -->
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="flex items-center text-xs text-gray-600">
                                        <input type="checkbox" name="bloqueo_activo_<?= $dayNum ?>" value="1" 
                                               <?= ($daySchedule && $daySchedule['bloqueo_activo']) ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 mr-2"
                                               onchange="toggleBlock(<?= $dayNum ?>)">
                                        <span class="font-medium">Bloquear horas</span>
                                    </label>
                                </div>
                                
                                <div id="block_<?= $dayNum ?>_times" class="<?= ($daySchedule && $daySchedule['bloqueo_activo']) ? '' : 'hidden' ?>">
                                    <div class="bg-orange-50 border border-orange-200 rounded p-2">
                                        <label class="block text-xs text-gray-500 mb-1">Horario Bloqueo</label>
                                        <div class="flex items-center gap-2">
                                            <input type="time" name="hora_inicio_bloqueo_<?= $dayNum ?>" 
                                                   value="<?= ($daySchedule && $daySchedule['hora_inicio_bloqueo']) ? substr($daySchedule['hora_inicio_bloqueo'], 0, 5) : '13:00' ?>"
                                                   class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                            <span class="text-xs text-gray-500 font-semibold">a</span>
                                            <input type="time" name="hora_fin_bloqueo_<?= $dayNum ?>" 
                                                   value="<?= ($daySchedule && $daySchedule['hora_fin_bloqueo']) ? substr($daySchedule['hora_fin_bloqueo'], 0, 5) : '14:00' ?>"
                                                   class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                <form method="POST" action="<?= url('/especialistas/horarios?id=' . $specialist['id']) ?>">
                    <input type="hidden" name="action" value="add_block">
                    
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
                    <form method="POST" action="<?= url('/especialistas/horarios?id=' . $specialist['id']) ?>" class="inline">
                        <input type="hidden" name="action" value="delete_block">
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
    const times = document.getElementById(`day_${dayNum}_times`);
    if (checkbox.checked) {
        times.classList.remove('opacity-50');
    } else {
        times.classList.add('opacity-50');
    }
}

function toggleBlock(dayNum) {
    const checkbox = document.querySelector(`input[name="bloqueo_activo_${dayNum}"]`);
    const blockTimes = document.getElementById(`block_${dayNum}_times`);
    if (checkbox.checked) {
        blockTimes.classList.remove('hidden');
    } else {
        blockTimes.classList.add('hidden');
    }
}
</script>
