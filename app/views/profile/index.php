<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center mb-6">
            <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                <?= strtoupper(substr($userData['nombre'], 0, 1) . substr($userData['apellidos'], 0, 1)) ?>
            </div>
            <h2 class="text-xl font-bold text-gray-800"><?= e($userData['nombre'] . ' ' . $userData['apellidos']) ?></h2>
            <p class="text-gray-500"><?= e(getRoleName($userData['rol_id'])) ?></p>
        </div>
        
        <div class="space-y-4">
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Email</span>
                <span class="font-medium"><?= e($userData['email']) ?></span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Teléfono</span>
                <span class="font-medium"><?= e($userData['telefono'] ?: 'No especificado') ?></span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Miembro desde</span>
                <span class="font-medium"><?= formatDate($userData['created_at'], 'd/m/Y') ?></span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-600">Último acceso</span>
                <span class="font-medium"><?= $userData['ultimo_acceso'] ? formatDate($userData['ultimo_acceso'], 'd/m/Y H:i') : 'N/A' ?></span>
            </div>
        </div>
        
        <div class="mt-6 flex justify-center space-x-4">
            <a href="<?= url('/perfil/editar') ?>" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <i class="fas fa-edit mr-2"></i>Editar Perfil
            </a>
            <a href="<?= url('/perfil/cambiar-password') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-key mr-2"></i>Cambiar Contraseña
            </a>
        </div>
    </div>
</div>
