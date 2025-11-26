<h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Crear Cuenta</h2>

<?php if (!empty($error)): ?>
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('/registro') ?>">
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="Juan">
        </div>
        <div>
            <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
            <input type="text" id="apellidos" name="apellidos" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="García">
        </div>
    </div>
    
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" id="email" name="email" required
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="correo@ejemplo.com">
        </div>
    </div>
    
    <div class="mb-4">
        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-phone"></i>
            </span>
            <input type="tel" id="telefono" name="telefono"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="+52 442 123 4567">
        </div>
    </div>
    
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
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
        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
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
        <i class="fas fa-user-plus mr-2"></i>Crear Cuenta
    </button>
</form>

<hr class="my-6">

<p class="text-center text-gray-600 text-sm">
    ¿Ya tienes cuenta? 
    <a href="<?= url('/login') ?>" class="text-primary font-semibold hover:underline">
        Inicia sesión aquí
    </a>
</p>
