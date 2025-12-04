<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/sucursales') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Sucursales
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Nueva Sucursal</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/sucursales/crear') ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Nombre de la sucursal">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Calle, número, colonia">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Ciudad">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" name="estado" value="Querétaro"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Estado">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                    <input type="text" name="codigo_postal"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="76000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="sucursal@reserbot.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Apertura</label>
                    <input type="time" name="horario_apertura" value="08:00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario Cierre</label>
                    <input type="time" name="horario_cierre" value="20:00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= url('/sucursales') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>
