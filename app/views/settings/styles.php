<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Configuración de Estilos</h2>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/configuraciones/estilos') ?>">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color Primario</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" name="color_primario" value="<?= e($settings['color_primario']) ?>"
                               class="h-12 w-24 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" value="<?= e($settings['color_primario']) ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg" readonly>
                        <div class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_primario']) ?>">
                            Vista previa
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Usado en botones principales, enlaces y elementos activos</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color Secundario</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" name="color_secundario" value="<?= e($settings['color_secundario']) ?>"
                               class="h-12 w-24 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" value="<?= e($settings['color_secundario']) ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg" readonly>
                        <div class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_secundario']) ?>">
                            Vista previa
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Usado en estados hover y elementos secundarios</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color de Acento</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" name="color_acento" value="<?= e($settings['color_acento']) ?>"
                               class="h-12 w-24 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" value="<?= e($settings['color_acento']) ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg" readonly>
                        <div class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_acento']) ?>">
                            Vista previa
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Usado en indicadores de éxito y elementos destacados</p>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Vista Previa</h3>
                <div class="flex space-x-3">
                    <button type="button" class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_primario']) ?>">
                        Botón Primario
                    </button>
                    <button type="button" class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_secundario']) ?>">
                        Botón Secundario
                    </button>
                    <button type="button" class="px-4 py-2 rounded-lg text-white" style="background-color: <?= e($settings['color_acento']) ?>">
                        Botón Acento
                    </button>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
        this.nextElementSibling.nextElementSibling.style.backgroundColor = this.value;
    });
});
</script>
