<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/perfil') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Mi Perfil
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Perfil</h2>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/perfil/editar') ?>" enctype="multipart/form-data">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="nombre" required value="<?= e($userData['nombre']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" required value="<?= e($userData['apellidos']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="<?= e($userData['email']) ?>" disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">El email no puede ser modificado</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= e($userData['telefono']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                           title="Ingrese un número de 10 dígitos">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                    <input type="file" name="avatar" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= url('/perfil') ?>" 
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
