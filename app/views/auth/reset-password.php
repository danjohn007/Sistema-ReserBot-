<h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Restablecer Contraseña</h2>

<?php if (!empty($error)): ?>
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('/reset-password?token=' . e($token)) ?>">
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" id="password" name="password" required minlength="6"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="Mínimo 6 caracteres">
        </div>
    </div>
    
    <div class="mb-6">
        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" id="password_confirm" name="password_confirm" required
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="Repita su contraseña">
        </div>
    </div>
    
    <button type="submit" 
            class="w-full bg-primary hover:bg-secondary text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        <i class="fas fa-save mr-2"></i>Cambiar Contraseña
    </button>
</form>
