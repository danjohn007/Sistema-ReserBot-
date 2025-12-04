<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/especialistas') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Especialistas
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Especialista</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/especialistas/editar?id=' . $specialist['id']) ?>">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información Personal</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= e($specialist['nombre']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" required value="<?= e($specialist['apellidos']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required value="<?= e($specialist['email']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= e($specialist['telefono']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información Profesional</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal *</label>
                    <select name="sucursal_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $specialist['sucursal_id'] ? 'selected' : '' ?>>
                            <?= e($branch['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                    <input type="text" name="profesion" value="<?= e($specialist['profesion']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" name="especialidad" value="<?= e($specialist['especialidad']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia</label>
                    <input type="number" name="experiencia_anos" min="0" value="<?= $specialist['experiencia_anos'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"><?= e($specialist['descripcion']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa Base</label>
                    <input type="number" name="tarifa_base" step="0.01" min="0" value="<?= $specialist['tarifa_base'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" <?= $specialist['activo'] ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Especialista activo</span>
                    </label>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Servicios que Ofrece</h3>
            
            <div class="mb-6">
                <?php 
                $currentCategory = '';
                foreach ($services as $service): 
                    if ($currentCategory != $service['categoria_nombre']):
                        if ($currentCategory != ''): ?>
            </div>
                        <?php endif;
                        $currentCategory = $service['categoria_nombre'];
                ?>
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2 border-b pb-1"><?= e($currentCategory) ?></h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <?php endif; ?>
                
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                            <input type="checkbox" name="servicios[]" value="<?= $service['id'] ?>"
                                   <?= in_array($service['id'], $currentServiceIds) ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700"><?= e($service['nombre']) ?></span>
                        </label>
                
                <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="<?= url('/especialistas') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
