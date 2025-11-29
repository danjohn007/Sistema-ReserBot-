<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(getConfig('nombre_sitio', 'ReserBot')) ?> - Sistema de Reservaciones y Citas</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --color-primary: <?= getConfig('color_primario', '#3B82F6') ?>;
            --color-secondary: <?= getConfig('color_secundario', '#1E40AF') ?>;
            --color-accent: <?= getConfig('color_acento', '#10B981') ?>;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= getConfig('color_primario', '#3B82F6') ?>',
                        secondary: '<?= getConfig('color_secundario', '#1E40AF') ?>',
                        accent: '<?= getConfig('color_acento', '#10B981') ?>'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <?php if (getConfig('logotipo')): ?>
                        <img src="<?= asset(getConfig('logotipo')) ?>" alt="Logo" class="h-8">
                    <?php else: ?>
                        <i class="fas fa-calendar-check text-2xl text-primary mr-2"></i>
                        <span class="text-xl font-bold text-gray-800"><?= e(getConfig('nombre_sitio', 'ReserBot')) ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?= url('/login') ?>" class="text-gray-600 hover:text-primary">Iniciar Sesión</a>
                    <a href="<?= url('/registro') ?>" 
                       class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition">
                        Registrarse
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-blue-600 to-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Gestiona tus citas de forma inteligente
                    </h1>
                    <p class="text-xl text-blue-100 mb-8">
                        Sistema completo de reservaciones para profesionales, clínicas, salones y todo tipo de negocios que requieran gestión de citas.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="<?= url('/registro') ?>" 
                           class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            <i class="fas fa-rocket mr-2"></i>Comenzar Ahora
                        </a>
                        <a href="#features" 
                           class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition">
                            <i class="fas fa-info-circle mr-2"></i>Más Información
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white rounded-2xl shadow-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span class="ml-3 font-semibold text-gray-800">Próximas Citas</span>
                            </div>
                            <span class="text-sm text-gray-500">Hoy</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <div class="w-2 h-8 bg-blue-500 rounded mr-3"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">Consulta General</p>
                                    <p class="text-sm text-gray-500">10:00 AM - Dr. Martínez</p>
                                </div>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Confirmada</span>
                            </div>
                            <div class="flex items-center p-3 bg-purple-50 rounded-lg">
                                <div class="w-2 h-8 bg-purple-500 rounded mr-3"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">Asesoría Legal</p>
                                    <p class="text-sm text-gray-500">2:00 PM - Lic. García</p>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendiente</span>
                            </div>
                            <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                <div class="w-2 h-8 bg-green-500 rounded mr-3"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">Terapia Individual</p>
                                    <p class="text-sm text-gray-500">5:00 PM - Psic. Rodríguez</p>
                                </div>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Confirmada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">¿Por qué ReserBot?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Todo lo que necesitas para gestionar tus citas y hacer crecer tu negocio
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-2xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Reservaciones en Línea</h3>
                    <p class="text-gray-600">
                        Permite a tus clientes agendar citas 24/7 de forma fácil y rápida desde cualquier dispositivo.
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-bell text-2xl text-green-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Recordatorios Automáticos</h3>
                    <p class="text-gray-600">
                        Reduce las ausencias con recordatorios por correo, SMS o WhatsApp antes de cada cita.
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-users text-2xl text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Multi-Sucursales</h3>
                    <p class="text-gray-600">
                        Gestiona múltiples ubicaciones, especialistas y servicios desde un solo panel.
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-2xl text-yellow-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Reportes y Estadísticas</h3>
                    <p class="text-gray-600">
                        Analiza el rendimiento de tu negocio con reportes detallados y gráficas interactivas.
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fab fa-paypal text-2xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Pagos en Línea</h3>
                    <p class="text-gray-600">
                        Acepta pagos anticipados con PayPal y reduce cancelaciones de última hora.
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-lock text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Seguridad Garantizada</h3>
                    <p class="text-gray-600">
                        Protección de datos, bitácora de acciones y control de acceso por roles.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Para Todo Tipo de Negocios</h2>
                <p class="text-xl text-gray-600">
                    Adaptable a cualquier industria que requiera gestión de citas
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <?php foreach ($categories as $category): ?>
                <div class="bg-white p-4 rounded-xl text-center hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" 
                         style="background-color: <?= e($category['color']) ?>15;">
                        <i class="<?= e($category['icono']) ?>" style="color: <?= e($category['color']) ?>;"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700"><?= e($category['nombre']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Branches Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Nuestras Sucursales</h2>
                <p class="text-xl text-gray-600">
                    Ubicaciones disponibles para atenderte
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($branches as $branch): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                        <i class="fas fa-building text-6xl text-white opacity-50"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= e($branch['nombre']) ?></h3>
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                            <?= e($branch['direccion']) ?>
                        </p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>
                                <i class="fas fa-phone mr-1"></i>
                                <?= e($branch['telefono']) ?>
                            </span>
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                <?= substr($branch['horario_apertura'], 0, 5) ?> - <?= substr($branch['horario_cierre'], 0, 5) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20 bg-primary">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-6">
                ¿Listo para optimizar tu agenda?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Únete a miles de profesionales que ya gestionan sus citas con ReserBot
            </p>
            <a href="<?= url('/registro') ?>" 
               class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
                <i class="fas fa-calendar-plus mr-2"></i>Comenzar Gratis
            </a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-calendar-check text-2xl text-primary mr-2"></i>
                        <span class="text-xl font-bold"><?= e(getConfig('nombre_sitio', 'ReserBot')) ?></span>
                    </div>
                    <p class="text-gray-400">
                        Sistema profesional de gestión de reservaciones y citas.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white">Características</a></li>
                        <li><a href="<?= url('/login') ?>" class="hover:text-white">Iniciar Sesión</a></li>
                        <li><a href="<?= url('/registro') ?>" class="hover:text-white">Registrarse</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <i class="fas fa-phone mr-2"></i>
                            <?= e(getConfig('telefono_contacto', '+52 442 123 4567')) ?>
                        </li>
                        <li>
                            <i class="fas fa-envelope mr-2"></i>
                            <?= e(getConfig('email_sistema', 'contacto@reserbot.com')) ?>
                        </li>
                        <li>
                            <i class="fas fa-clock mr-2"></i>
                            <?= e(getConfig('horario_atencion', 'Lunes a Viernes 8:00 - 20:00')) ?>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Síguenos</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="my-8 border-gray-700">
            
            <div class="text-center text-gray-400 text-sm">
                &copy; <?= date('Y') ?> <?= e(getConfig('nombre_sitio', 'ReserBot')) ?>. Todos los derechos reservados.
            </div>
        </div>
    </footer>
    
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
