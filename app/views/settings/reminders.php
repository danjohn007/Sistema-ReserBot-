<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fab fa-whatsapp text-green-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Recordatorios autom&aacute;ticos por WhatsApp</h2>
                <p class="text-sm text-gray-500">Env&iacute;a recordatorios autom&aacute;ticos a los pacientes antes de su cita.</p>
            </div>
        </div>
    </div>

    <?php if ($canSelectSpecialist && !empty($specialists)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="<?= url('/configuraciones/recordatorios') ?>" class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialista</label>
                <select name="especialista_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                        onchange="this.form.submit()">
                    <?php foreach ($specialists as $sp): ?>
                        <option value="<?= $sp['id'] ?>" <?= $selectedId == $sp['id'] ? 'selected' : '' ?>>
                            <?= e($sp['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($selectedId > 0): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form id="reminderForm" onsubmit="return saveReminderConfig(event)">
            <input type="hidden" name="especialista_id" value="<?= (int)$selectedId ?>">

            <!-- Toggle activar/desactivar -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-6">
                <div>
                    <h3 class="font-semibold text-gray-800">Recordatorios autom&aacute;ticos</h3>
                    <p class="text-sm text-gray-500">Activa el env&iacute;o autom&aacute;tico de recordatorios.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="enabledToggle" name="enabled" value="1"
                           class="sr-only peer" <?= !empty($config['enabled']) ? 'checked' : '' ?>
                           onchange="toggleHoursControl()">
                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer
                                peer-checked:after:translate-x-7 peer-checked:after:border-white
                                after:content-[''] after:absolute after:top-0.5 after:left-[4px]
                                after:bg-white after:border after:rounded-full after:h-6 after:w-6
                                after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>

            <!-- Horas antes -->
            <div id="hoursContainer" class="p-4 bg-gray-50 rounded-lg mb-6 <?= empty($config['enabled']) ? 'opacity-50' : '' ?>">
                <label class="block font-semibold text-gray-800 mb-1">
                    Horas antes del recordatorio:
                    <span id="hoursValue" class="text-green-600"><?= (int)$config['hours_before'] ?></span> hora(s)
                </label>
                <p class="text-sm text-gray-500 mb-3">Se enviar&aacute; el recordatorio esta cantidad de horas antes de la cita.</p>
                <input type="range" id="hoursSlider" name="hours_before"
                       min="1" max="24" step="1"
                       value="<?= (int)$config['hours_before'] ?>"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600"
                       <?= empty($config['enabled']) ? 'disabled' : '' ?>
                      oninput="document.getElementById('hoursValue').textContent = this.value; refreshReminderTimes();">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>1h</span><span>6h</span><span>12h</span><span>18h</span><span>24h</span>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Guardar configuraci&oacute;n
                </button>
            </div>

            <div id="reminderMsg" class="mt-4 hidden p-3 rounded-lg text-sm"></div>
        </form>
    </div>

    <!-- ¿Cómo funciona? -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>&iquest;C&oacute;mo funciona?</h3>
        <ul class="text-sm text-blue-900 list-disc list-inside space-y-1">
            <li>Un proceso autom&aacute;tico (cron) revisa cada 15 minutos las pr&oacute;ximas citas.</li>
            <li>Si una cita est&aacute; programada dentro de las horas configuradas, se env&iacute;a un mensaje al paciente por WhatsApp.</li>
            <li>Solo se env&iacute;a si la cita est&aacute; en estado <b>pendiente</b> o <b>confirmada</b> y tiene tel&eacute;fono v&aacute;lido.</li>
            <li>Cada cita recibe el recordatorio una sola vez.</li>
        </ul>
        <div class="mt-3 text-sm">
            <a href="<?= url('/configuraciones/recordatorios/estado') ?>" target="_blank" class="text-blue-700 hover:text-blue-900 font-semibold">
                <i class="fas fa-stethoscope mr-1"></i>Ver diagn&oacute;stico del cron
            </a>
            <span class="mx-2 text-blue-400">|</span>
            <a href="<?= url('/configuraciones/recordatorios/estado/ejecutar') ?>" target="_blank" class="text-blue-700 hover:text-blue-900 font-semibold">
                <i class="fas fa-play mr-1"></i>Ejecutar cron ahora
            </a>
        </div>
    </div>

    <!-- Probar envío manual -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800"><i class="fas fa-vial mr-2 text-purple-600"></i>Probar env&iacute;o manual</h3>
                <p class="text-sm text-gray-500">Env&iacute;a un recordatorio inmediatamente para cualquier cita futura (no afecta el flag de la cita).</p>
            </div>
        </div>

        <?php if (empty($upcoming)): ?>
            <p class="text-sm text-gray-500">No hay citas pendientes/confirmadas futuras.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">C&oacute;digo</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tel&eacute;fono</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha / Hora</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recordatorio aprox.</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($upcoming as $u): ?>
                    <?php
                        $citaTs = strtotime($u['fecha_cita'] . ' ' . $u['hora_inicio']);
                        $hoursBefore = (int)($config['hours_before'] ?? 3);
                        $reminderTs = $citaTs - ($hoursBefore * 3600);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-mono text-xs"><?= e($u['codigo']) ?></td>
                        <td class="px-3 py-2"><?= e($u['nombre_cliente'] ?: '—') ?></td>
                        <td class="px-3 py-2">
                            <?php if (!empty($u['telefono'])): ?>
                                <span class="text-gray-700"><?= e($u['telefono']) ?></span>
                            <?php else: ?>
                                <span class="text-red-500 text-xs">Sin tel&eacute;fono</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-gray-600">
                            <?= date('d/m/Y', strtotime($u['fecha_cita'])) ?>
                            <span class="text-blue-600 font-semibold"><?= date('H:i', strtotime($u['hora_inicio'])) ?></span>
                        </td>
                        <td class="px-3 py-2 text-gray-600 js-reminder-time" data-cita-ts="<?= (int)$citaTs ?>">
                            <?= date('d/m/Y H:i', $reminderTs) ?>
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded text-xs <?= $u['estado'] === 'confirmada' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= e($u['estado']) ?>
                            </span>
                            <?php if (!empty($u['recordatorio_enviado'])): ?>
                                <span class="ml-1 text-xs text-gray-400" title="Ya se envió recordatorio">
                                    <i class="fas fa-check-double"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <button type="button"
                                    onclick="testReminder(<?= (int)$u['id'] ?>, this)"
                                    <?= empty($u['telefono']) ? 'disabled' : '' ?>
                                    class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <i class="fab fa-whatsapp mr-1"></i>Enviar prueba
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="testResult" class="mt-4 hidden p-3 rounded-lg text-sm"></div>
        <?php endif; ?>
    </div>

    <!-- Historial -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Historial reciente</h3>
        <?php if (empty($logs)): ?>
            <p class="text-sm text-gray-500">Todav&iacute;a no se han enviado recordatorios para este especialista.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha de citas</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Enviados</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Procesado el</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium"><?= formatDate($log['target_date']) ?></td>
                        <td class="px-4 py-2 text-green-700"><?= (int)$log['sent_count'] ?></td>
                        <td class="px-4 py-2"><?= (int)$log['total_count'] ?></td>
                        <td class="px-4 py-2 text-gray-500"><?= date('d/m/Y H:i', strtotime($log['sent_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
            No hay especialistas disponibles para configurar.
        </div>
    <?php endif; ?>
</div>

<script>
function toggleHoursControl() {
    var enabled = document.getElementById('enabledToggle').checked;
    var slider  = document.getElementById('hoursSlider');
    var box     = document.getElementById('hoursContainer');
    slider.disabled = !enabled;
    box.classList.toggle('opacity-50', !enabled);
}

function pad2(n) {
    return n < 10 ? '0' + n : String(n);
}

function formatDateTime(tsSeconds) {
    var d = new Date(tsSeconds * 1000);
    return pad2(d.getDate()) + '/' + pad2(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad2(d.getHours()) + ':' + pad2(d.getMinutes());
}

function refreshReminderTimes() {
    var slider = document.getElementById('hoursSlider');
    if (!slider) return;

    var hours = parseInt(slider.value || '0', 10);
    var cells = document.querySelectorAll('.js-reminder-time[data-cita-ts]');

    cells.forEach(function(cell) {
        var citaTs = parseInt(cell.getAttribute('data-cita-ts') || '0', 10);
        if (!citaTs) return;
        var reminderTs = citaTs - (hours * 3600);
        cell.textContent = formatDateTime(reminderTs);
    });
}

function saveReminderConfig(e) {
    e.preventDefault();
    var form = document.getElementById('reminderForm');
    var msg  = document.getElementById('reminderMsg');
    var fd   = new FormData(form);
    // Asegurar que enabled siempre se envíe (incluso si está apagado)
    if (!fd.has('enabled')) fd.set('enabled', '0');

    fetch('<?= url('/configuraciones/recordatorios/guardar') ?>', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        msg.classList.remove('hidden','bg-red-100','text-red-700','bg-green-100','text-green-700');
        if (data.success) {
            msg.classList.add('bg-green-100','text-green-700');
            msg.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
        } else {
            msg.classList.add('bg-red-100','text-red-700');
            msg.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + (data.message || 'Error');
        }
    })
    .catch(function(err){
        msg.classList.remove('hidden');
        msg.classList.add('bg-red-100','text-red-700');
        msg.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error de red: ' + err;
    });
    return false;
}

function testReminder(reservacionId, btn) {
    var result = document.getElementById('testResult');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Enviando...';

    var fd = new FormData();
    fd.append('reservacion_id', reservacionId);

    fetch('<?= url('/configuraciones/recordatorios/probar') ?>', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        result.classList.remove('hidden','bg-red-100','text-red-700','bg-green-100','text-green-700');
        if (data.success) {
            result.classList.add('bg-green-100','text-green-700');
            result.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
        } else {
            result.classList.add('bg-red-100','text-red-700');
            result.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + (data.message || 'Error') +
                (data.raw ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.raw + '</pre>' : '');
        }
    })
    .catch(function(err){
        result.classList.remove('hidden');
        result.classList.add('bg-red-100','text-red-700');
        result.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error de red: ' + err;
    })
    .finally(function(){
        btn.disabled = false;
        btn.innerHTML = original;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    refreshReminderTimes();
});
</script>
