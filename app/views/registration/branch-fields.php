<article class="border border-gray-200 bg-white p-5" data-branch-card data-branch-index="<?= e($index) ?>">
    <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center bg-blue-100 font-bold text-blue-700" data-branch-number>
                <?= is_numeric($index) ? ((int) $index + 1) : 1 ?>
            </div>
            <h4 class="font-semibold text-gray-800">Datos de la sucursal</h4>
        </div>
        <button type="button" onclick="removeBranch(this)" data-remove-branch
                class="flex h-9 w-9 items-center justify-center text-red-500 hover:bg-red-50 hover:text-red-700 transition"
                title="Quitar sucursal" aria-label="Quitar sucursal">
            <i class="fas fa-trash"></i>
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre *</label>
            <input type="text" name="sucursales[<?= $index ?>][nombre]" required maxlength="150"
                   value="<?= e($branch['nombre']) ?>" data-branch-name
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Correo *</label>
            <input type="email" name="sucursales[<?= $index ?>][email]" required maxlength="150" value="<?= e($branch['email']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-gray-700">Direccion *</label>
            <input type="text" name="sucursales[<?= $index ?>][direccion]" required maxlength="500" value="<?= e($branch['direccion']) ?>"
                   placeholder="Calle, numero y colonia"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Ciudad *</label>
            <input type="text" name="sucursales[<?= $index ?>][ciudad]" required maxlength="100" value="<?= e($branch['ciudad']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Estado *</label>
            <input type="text" name="sucursales[<?= $index ?>][estado]" required maxlength="100" value="<?= e($branch['estado']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Codigo postal</label>
            <input type="text" name="sucursales[<?= $index ?>][codigo_postal]" maxlength="10" value="<?= e($branch['codigo_postal']) ?>"
                   inputmode="numeric"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Telefono *</label>
            <input type="tel" name="sucursales[<?= $index ?>][telefono]" required maxlength="20" value="<?= e($branch['telefono']) ?>"
                   inputmode="tel"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Horario de apertura</label>
            <input type="time" name="sucursales[<?= $index ?>][horario_apertura]" value="<?= e($branch['horario_apertura']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Horario de cierre</label>
            <input type="time" name="sucursales[<?= $index ?>][horario_cierre]" value="<?= e($branch['horario_cierre']) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
</article>
