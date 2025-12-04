<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/sucursales') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Sucursales
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Sucursal</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/sucursales/editar?id=' . $branch['id']) ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= e($branch['nombre']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="<?= e($branch['direccion']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad" value="<?= e($branch['ciudad']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" name="estado" value="<?= e($branch['estado']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                    <input type="text" name="codigo_postal" value="<?= e($branch['codigo_postal']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= e($branch['telefono']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= e($branch['email']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Apertura</label>
                    <input type="time" name="horario_apertura" value="<?= substr($branch['horario_apertura'], 0, 5) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Cierre</label>
                    <input type="time" name="horario_cierre" value="<?= substr($branch['horario_cierre'], 0, 5) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" <?= $branch['activo'] ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Sucursal activa</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= url('/sucursales') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
