<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Configuración de PayPal</h2>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/configuraciones/paypal') ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modo</label>
                    <select name="paypal_modo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="sandbox" <?= ($settings['paypal_modo'] ?? '') == 'sandbox' ? 'selected' : '' ?>>Sandbox (Pruebas)</option>
                        <option value="live" <?= ($settings['paypal_modo'] ?? '') == 'live' ? 'selected' : '' ?>>Live (Producción)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                    <input type="text" name="paypal_client_id" value="<?= e($settings['paypal_client_id'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret</label>
                    <input type="password" name="paypal_secret" value="<?= e($settings['paypal_secret'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
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
