<article class="border border-gray-200 bg-white p-5" data-professional-card data-professional-index="<?= e($index) ?>">
    <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center bg-green-100 font-bold text-green-700" data-professional-number>
                <?= is_numeric($index) ? ((int) $index + 1) : 1 ?>
            </div>
            <h4 class="font-semibold text-gray-800">Datos del profesionista</h4>
        </div>
        <button type="button" onclick="removeProfessional(this)" data-remove-professional
                class="flex h-9 w-9 items-center justify-center text-red-500 hover:bg-red-50 hover:text-red-700 transition"
                title="Quitar profesionista">
            <i class="fas fa-trash"></i>
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700">Sucursales donde atendera *</label>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                 data-professional-branches
                 data-selected-branches="<?= e(json_encode(array_values($professional['sucursales'] ?? []))) ?>">
            </div>
            <p class="mt-1 text-xs text-gray-500">Puedes seleccionar una o varias sucursales.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre *</label>
            <input type="text" name="profesionistas[<?= $index ?>][nombre]" required maxlength="100" value="<?= e($professional['nombre']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos *</label>
            <input type="text" name="profesionistas[<?= $index ?>][apellidos]" required maxlength="100" value="<?= e($professional['apellidos']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Correo de acceso *</label>
            <input type="email" name="profesionistas[<?= $index ?>][email]" required maxlength="150" value="<?= e($professional['email']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Telefono (opcional)</label>
            <input type="tel" name="profesionistas[<?= $index ?>][telefono]" maxlength="20" value="<?= e($professional['telefono']) ?>"
                   inputmode="tel"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Contraseña inicial *</label>
            <input type="password" name="profesionistas[<?= $index ?>][password]" required minlength="8" autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
            <p class="mt-1 text-xs text-gray-500">El acceso se habilitara solo despues de aprobar la solicitud.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Profesion *</label>
            <input type="text" name="profesionistas[<?= $index ?>][profesion]" required maxlength="100" value="<?= e($professional['profesion']) ?>"
                   placeholder="Ej. Medico, Abogada, Fisioterapeuta"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Especialidad</label>
            <input type="text" name="profesionistas[<?= $index ?>][especialidad]" maxlength="150" value="<?= e($professional['especialidad']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Experiencia</label>
                <input type="number" name="profesionistas[<?= $index ?>][experiencia_anos]" min="0" max="80" value="<?= (int) $professional['experiencia_anos'] ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tarifa base (opcional)</label>
                <input type="number" name="profesionistas[<?= $index ?>][tarifa_base]" min="0" step="0.01" value="<?= e($professional['tarifa_base']) ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-gray-700">Descripcion profesional</label>
            <textarea name="profesionistas[<?= $index ?>][descripcion]" rows="3" maxlength="1000"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100"><?= e($professional['descripcion']) ?></textarea>
        </div>
    </div>
</article>
