<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/servicios') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Servicios
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Servicio</h2>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/servicios/editar?id=' . $service['id']) ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categor&iacute;a *</label>
                    <select name="categoria_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $service['categoria_id'] ? 'selected' : '' ?>>
                            <?= e($cat['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= e($service['nombre']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripci&oacute;n</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"><?= e($service['descripcion']) ?></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duración (minutos)</label>
                        <input type="number" id="duracion_minutos" name="duracion_minutos" value="<?= $service['duracion_minutos'] ?>" min="15" step="15"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Debe ser múltiplo de 15 (ej: 15, 30, 45, 60)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <input type="number" name="precio" step="0.01" value="<?= $service['precio'] ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio de oferta</label>
                    <input type="number" name="precio_oferta" step="0.01" value="<?= $service['precio_oferta'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" <?= $service['activo'] ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Servicio activo</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= url('/servicios') ?>" 
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

<script>
// Validar que la duración sea múltiplo de 15
document.querySelector('form').addEventListener('submit', function(e) {
    const duracion = parseInt(document.getElementById('duracion_minutos').value);
    
    if (duracion % 15 !== 0) {
        e.preventDefault();
        alert('⚠️ La duración debe ser un múltiplo de 15 minutos.\n\nEjemplos válidos: 15, 30, 45, 60, 75, 90, etc.');
        document.getElementById('duracion_minutos').focus();
        return false;
    }
});

// Redondear automáticamente al múltiplo de 15 más cercano al perder foco
document.getElementById('duracion_minutos').addEventListener('blur', function() {
    let duracion = parseInt(this.value);
    if (isNaN(duracion) || duracion < 15) {
        this.value = 15;
        return;
    }
    
    const resto = duracion % 15;
    if (resto !== 0) {
        // Redondear al múltiplo de 15 más cercano
        const redondeado = resto < 8 ? duracion - resto : duracion + (15 - resto);
        this.value = Math.max(15, redondeado);
        
        // Mostrar mensaje
        const mensaje = document.createElement('div');
        mensaje.className = 'text-xs text-blue-600 mt-1';
        mensaje.textContent = `Ajustado automáticamente a ${this.value} minutos`;
        this.parentElement.appendChild(mensaje);
        setTimeout(() => mensaje.remove(), 3000);
    }
});
</script>
