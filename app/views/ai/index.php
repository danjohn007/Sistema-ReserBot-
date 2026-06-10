<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/dashboard') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-robot text-indigo-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Configuraci&oacute;n de IA</h2>
                <p class="text-sm text-gray-500">Activa el chatbot y personaliza su contexto de conocimiento.</p>
            </div>
        </div>
    </div>

    <?php if ($isSuperAdmin && !empty($specialists)): ?>
    <!-- Selector de especialista (solo superadmin) -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="<?= url('/ia') ?>" class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialista</label>
                <select name="usuario_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400">
                    <?php foreach ($specialists as $sp): ?>
                        <option value="<?= $sp['id'] ?>" <?= $selectedId == $sp['id'] ? 'selected' : '' ?>>
                            <?= e($sp['nombre'] . ' ' . $sp['apellidos']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($target): ?>
    <!-- Formulario principal -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form id="aiForm" onsubmit="return saveAI(event)">
            <input type="hidden" name="usuario_id" value="<?= (int)$target['id'] ?>">

            <!-- Toggle ai_enabled -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-6">
                <div>
                    <h3 class="font-semibold text-gray-800">Chatbot de IA activo</h3>
                    <p class="text-sm text-gray-500">Activa o desactiva el asistente de IA para este especialista.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="aiToggle" name="ai_enabled" value="1"
                           class="sr-only peer" <?= !empty($target['ai_enabled']) ? 'checked' : '' ?>>
                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer
                                peer-checked:after:translate-x-7 peer-checked:after:border-white
                                after:content-[''] after:absolute after:top-0.5 after:left-[4px]
                                after:bg-white after:border after:rounded-full after:h-6 after:w-6
                                after:transition-all peer-checked:bg-indigo-500"></div>
                </label>
            </div>

            <!-- ai_contexto -->
            <div class="mb-6">
                <label class="block font-semibold text-gray-800 mb-1">
                    Contexto de la IA
                    <span id="charCount" class="text-sm font-normal text-gray-400 ml-2">
                        <?= mb_strlen($target['ai_contexto'] ?? '') ?>/5000
                    </span>
                </label>
                <p class="text-sm text-gray-500 mb-2">
                    Describe el negocio, servicios, horarios, pol&iacute;ticas y cualquier informaci&oacute;n
                    que deba conocer el chatbot para responder correctamente a los clientes.
                </p>
                <textarea id="aiContexto" name="ai_contexto" rows="12" maxlength="5000"
                          oninput="updateCharCount()"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 font-mono text-sm resize-y"
                          placeholder="Ejemplo: Somos un consultorio dental ubicado en... Ofrecemos servicios de... Nuestro horario es..."><?= e($target['ai_contexto'] ?? '') ?></textarea>
                <p class="text-xs text-gray-400 mt-1">M&aacute;ximo 5000 caracteres.</p>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Guardar configuraci&oacute;n
                </button>
                <span id="aiStatusBadge" class="hidden text-sm px-3 py-1 rounded-full"></span>
            </div>

            <div id="aiMsg" class="mt-4 hidden p-3 rounded-lg text-sm"></div>
        </form>
    </div>
    <?php elseif ($isSuperAdmin): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
        No hay especialistas disponibles para configurar.
    </div>
    <?php endif; ?>
</div>

<script>
function updateCharCount() {
    const ta = document.getElementById('aiContexto');
    const el = document.getElementById('charCount');
    if (ta && el) {
        const len = ta.value.length;
        el.textContent = len + '/5000';
        el.classList.toggle('text-red-500', len >= 4800);
    }
}

function saveAI(e) {
    e.preventDefault();
    const form = document.getElementById('aiForm');
    const msg  = document.getElementById('aiMsg');
    const fd   = new FormData(form);

    if (!fd.has('ai_enabled')) fd.set('ai_enabled', '0');

    msg.classList.add('hidden');

    fetch('<?= url('/ia/guardar') ?>', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        msg.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
        if (data.success) {
            msg.classList.add('bg-green-100', 'text-green-700');
            msg.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
        } else {
            msg.classList.add('bg-red-100', 'text-red-700');
            msg.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + (data.message || 'Error');
        }
    })
    .catch(err => {
        msg.classList.remove('hidden');
        msg.classList.add('bg-red-100', 'text-red-700');
        msg.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error de red: ' + err;
    });

    return false;
}
</script>
