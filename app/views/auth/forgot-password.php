<h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Recuperar Contraseña</h2>

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

<p class="text-gray-600 text-sm mb-6 text-center">
    Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.
</p>

<form method="POST" action="<?= url('/recuperar-password') ?>">
    <div class="mb-6">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" id="email" name="email" required
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="correo@ejemplo.com">
        </div>
    </div>
    
    <button type="submit" 
            class="w-full bg-primary hover:bg-secondary text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        <i class="fas fa-paper-plane mr-2"></i>Enviar Instrucciones
    </button>
</form>

<hr class="my-6">

<p class="text-center text-gray-600 text-sm">
    <a href="<?= url('/login') ?>" class="text-primary hover:underline">
        <i class="fas fa-arrow-left mr-1"></i>Volver al inicio de sesión
    </a>
</p>
