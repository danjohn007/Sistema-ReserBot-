<h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Iniciar Sesión</h2>

<?php if (!empty($error)): ?>
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('/login') ?>">
    <div class="mb-4">
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
    
    <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" id="password" name="password" required
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="••••••••">
        </div>
    </div>
    
    <button type="submit" 
            class="w-full bg-primary hover:bg-secondary text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        <i class="fas fa-sign-in-alt mr-2"></i>Ingresar
    </button>
</form>

<div class="mt-6 text-center">
    <a href="<?= url('/recuperar-password') ?>" class="text-sm text-primary hover:underline">
        ¿Olvidaste tu contraseña?
    </a>
</div>

<hr class="my-6">

<p class="text-center text-gray-600 text-sm">
    ¿No tienes cuenta? 
    <a href="<?= url('/registro') ?>" class="text-primary font-semibold hover:underline">
        Regístrate aquí
    </a>
</p>

<!-- Demo credentials -->
<div class="mt-6 p-4 bg-gray-50 rounded-lg">
    <p class="text-xs text-gray-500 text-center mb-2">Credenciales de prueba:</p>
    <p class="text-xs text-gray-600 text-center"><strong>Email:</strong> admin@reserbot.com</p>
    <p class="text-xs text-gray-600 text-center"><strong>Password:</strong> password</p>
</div>
