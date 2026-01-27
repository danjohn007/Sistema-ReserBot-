<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Configuraciones del Sistema</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="<?= url('/configuraciones/general') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-cog text-blue-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Configuraci&oacute;n General</h3>
            <p class="text-sm text-gray-500 mt-1">Nombre del sitio, logotipo, contacto</p>
        </a>
        
        <a href="<?= url('/configuraciones/estilos') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-palette text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Estilos y Colores</h3>
            <p class="text-sm text-gray-500 mt-1">Personaliza los colores del sistema</p>
        </a>
        
        <a href="<?= url('/configuraciones/correo') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-envelope text-green-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Configuraci&oacute;n de Correo</h3>
            <p class="text-sm text-gray-500 mt-1">Servidor SMTP y notificaciones</p>
        </a>
        
        <a href="<?= url('/configuraciones/paypal') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fab fa-paypal text-yellow-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Integración PayPal</h3>
            <p class="text-sm text-gray-500 mt-1">Configura los pagos con PayPal</p>
        </a>
        
        <a href="<?= url('/configuraciones/feriados') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-calendar-times text-red-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">D&iacute;as Feriados</h3>
            <p class="text-sm text-gray-500 mt-1">Gestiona los d&iacute;as no laborables</p>
        </a>
        
        <a href="<?= url('/notificaciones/plantillas') ?>" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-bell text-indigo-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Plantillas de Notificaciones</h3>
            <p class="text-sm text-gray-500 mt-1">Personaliza los mensajes</p>
        </a>
    </div>
</div>
