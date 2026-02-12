<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ReserBot') ?> - <?= e(getConfig('nombre_sitio', 'ReserBot')) ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/es.global.min.js"></script>
    
    <style>
        :root {
            --color-primary: <?= getConfig('color_primario', '#3B82F6') ?>;
            --color-secondary: <?= getConfig('color_secundario', '#1E40AF') ?>;
            --color-accent: <?= getConfig('color_acento', '#10B981') ?>;
        }
        
        .bg-primary { background-color: var(--color-primary); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .bg-secondary { background-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        .bg-accent { background-color: var(--color-accent); }
        .text-accent { color: var(--color-accent); }
        
        .sidebar-link.active {
            background-color: var(--color-primary);
            color: white;
        }
        
        .sidebar-link:hover:not(.active) {
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        /* Scrollbar personalizada para el sidebar */
        aside nav::-webkit-scrollbar,
        #mobile-sidebar nav::-webkit-scrollbar {
            width: 8px;
        }
        
        aside nav::-webkit-scrollbar-track,
        #mobile-sidebar nav::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        aside nav::-webkit-scrollbar-thumb,
        #mobile-sidebar nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        aside nav::-webkit-scrollbar-thumb:hover,
        #mobile-sidebar nav::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Para Firefox */
        aside nav,
        #mobile-sidebar nav {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f1f1;
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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg fixed h-screen z-30 hidden md:flex md:flex-col">
            <div class="p-4 border-b flex-shrink-0">
                <a href="<?= url('/dashboard') ?>" class="flex items-center space-x-2">
                    <?php if (getConfig('logotipo')): ?>
                        <img src="<?= asset(getConfig('logotipo')) ?>" alt="Logo" class="h-8">
                    <?php else: ?>
                        <i class="fas fa-calendar-check text-2xl text-primary"></i>
                    <?php endif; ?>
                    <span class="text-xl font-bold text-gray-800"><?= e(getConfig('nombre_sitio', 'ReserBot')) ?></span>
                </a>
            </div>
            
            <nav class="p-4 overflow-y-auto flex-1" style="max-height: calc(100vh - 80px);">
                <ul class="space-y-2">
                    <li>
                        <a href="<?= url('/dashboard') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST, ROLE_SPECIALIST])): ?>
                    <li>
                        <a href="<?= url('/reservaciones') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/reservaciones') !== false ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt w-5"></i>
                            <span>Reservaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/calendario') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/calendario') !== false ? 'active' : '' ?>">
                            <i class="fas fa-calendar-week w-5"></i>
                            <span>Calendario</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_CLIENT)): ?>
                    <li>
                        <a href="<?= url('/mis-citas') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/mis-citas') !== false ? 'active' : '' ?>">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span>Mis Citas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/reservaciones/nueva') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Nueva Cita</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
                    <li class="pt-4">
                        <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Administraci&oacute;n</span>
                    </li>
                    
                    <li>
                        <a href="<?= url('/sucursales') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/sucursales') !== false ? 'active' : '' ?>">
                            <i class="fas fa-building w-5"></i>
                            <span>Sucursales</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/especialistas') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/especialistas') !== false ? 'active' : '' ?>">
                            <i class="fas fa-user-md w-5"></i>
                            <span>Especialistas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/servicios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/servicios') !== false ? 'active' : '' ?>">
                            <i class="fas fa-concierge-bell w-5"></i>
                            <span>Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/categorias') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/categorias') !== false ? 'active' : '' ?>">
                            <i class="fas fa-tags w-5"></i>
                            <span>Categor&iacute;as</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/clientes') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/clientes') !== false ? 'active' : '' ?>">
                            <i class="fas fa-users w-5"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
                    <li class="pt-4">
                        <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Reportes</span>
                    </li>
                    <li>
                        <a href="<?= url('/reportes') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Reportes</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_SUPERADMIN)): ?>
                    <li class="pt-4">
                        <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Sistema</span>
                    </li>
                    <li>
                        <a href="<?= url('/configuraciones') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/configuraciones') !== false ? 'active' : '' ?>">
                            <i class="fas fa-cog w-5"></i>
                            <span>Configuraciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/logs') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/logs') !== false ? 'active' : '' ?>">
                            <i class="fas fa-history w-5"></i>
                            <span>Logs</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_SPECIALIST)): ?>
                    <li class="pt-4">
                        <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Mi Configuraci&oacute;n</span>
                    </li>
                    <li>
                        <a href="<?= url('/especialistas/horarios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/horarios') !== false ? 'active' : '' ?>">
                            <i class="fas fa-clock w-5"></i>
                            <span>Mis Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/especialistas/mis-servicios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/mis-servicios') !== false ? 'active' : '' ?>">
                            <i class="fas fa-concierge-bell w-5"></i>
                            <span>Mis Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/pagos') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/pagos') !== false ? 'active' : '' ?>">
                            <i class="fas fa-money-bill-wave w-5"></i>
                            <span>Pagos</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 md:ml-64">
            <!-- Top Header -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex justify-between items-center px-6 py-4">
                    <div class="flex items-center">
                        <button id="mobile-menu-btn" class="md:hidden mr-4 text-gray-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800"><?= e($title ?? 'Dashboard') ?></h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <a href="<?= url('/notificaciones') ?>" class="relative text-gray-600 hover:text-primary">
                            <i class="fas fa-bell text-xl"></i>
                            <?php
                            $unreadCount = 0;
                            if (isLoggedIn()) {
                                try {
                                    $unreadCount = Database::getInstance()->fetch(
                                        "SELECT COUNT(*) as count FROM notificaciones WHERE usuario_id = ? AND leida = 0",
                                        [currentUser()['id']]
                                    )['count'];
                                } catch (Exception $e) {}
                            }
                            if ($unreadCount > 0):
                            ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button id="user-menu-btn" onclick="toggleUserMenu()" class="flex items-center space-x-2 text-gray-700 hover:text-primary">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white">
                                    <?php $user = currentUser(); ?>
                                    <?= strtoupper(substr($user['nombre'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="hidden sm:block"><?= e($user['nombre'] ?? 'Usuario') ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-semibold text-gray-700"><?= e(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? '')) ?></p>
                                    <p class="text-xs text-gray-500"><?= e(getRoleName($user['rol_id'] ?? 0)) ?></p>
                                </div>
                                <a href="<?= url('/perfil') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Mi Perfil
                                </a>
                                <a href="<?= url('/perfil/cambiar-password') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-key mr-2"></i> Cambiar Contrase&ntilde;a
                                </a>
                                <hr class="my-2">
                                <a href="<?= url('/logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesi&oacute;n
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-6">
                <?php 
                $flash = getFlashMessage();
                if ($flash): 
                ?>
                <div class="mb-4 p-4 rounded-lg <?= $flash['type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <div class="flex items-center">
                        <i class="fas <?= $flash['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                        <?= e($flash['message']) ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php include $content; ?>
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t py-4 px-6 text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> <?= e(getConfig('nombre_sitio', 'ReserBot')) ?>. Todos los derechos reservados.
            </footer>
        </div>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 transform -translate-x-full transition-transform duration-300 md:hidden flex flex-col">
        <div class="p-4 border-b flex justify-between items-center flex-shrink-0">
            <span class="text-xl font-bold text-gray-800"><?= e(getConfig('nombre_sitio', 'ReserBot')) ?></span>
            <button id="close-mobile-menu" class="text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="p-4 overflow-y-auto flex-1" style="max-height: calc(100vh - 80px);">
            <ul class="space-y-2">
                <li>
                    <a href="<?= url('/dashboard') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_RECEPTIONIST, ROLE_SPECIALIST])): ?>
                <li>
                    <a href="<?= url('/reservaciones') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/reservaciones') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span>Reservaciones</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/calendario') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/calendario') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-week w-5"></i>
                        <span>Calendario</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (hasRole(ROLE_CLIENT)): ?>
                <li>
                    <a href="<?= url('/mis-citas') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/mis-citas') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>Mis Citas</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/reservaciones/nueva') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700">
                        <i class="fas fa-plus-circle w-5"></i>
                        <span>Nueva Cita</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Administraci&oacute;n</span>
                </li>
                
                <li>
                    <a href="<?= url('/sucursales') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/sucursales') !== false ? 'active' : '' ?>">
                        <i class="fas fa-building w-5"></i>
                        <span>Sucursales</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/especialistas') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/especialistas') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-md w-5"></i>
                        <span>Especialistas</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/servicios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/servicios') !== false ? 'active' : '' ?>">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span>Servicios</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/categorias') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/categorias') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tags w-5"></i>
                        <span>Categor&iacute;as</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/clientes') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/clientes') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users w-5"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (hasAnyRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN])): ?>
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Reportes</span>
                </li>
                <li>
                    <a href="<?= url('/reportes') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (hasRole(ROLE_SUPERADMIN)): ?>
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Sistema</span>
                </li>
                <li>
                    <a href="<?= url('/configuraciones') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/configuraciones') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog w-5"></i>
                        <span>Configuraciones</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/logs') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/logs') !== false ? 'active' : '' ?>">
                        <i class="fas fa-history w-5"></i>
                        <span>Logs</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (hasRole(ROLE_SPECIALIST)): ?>
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase">Mi Configuraci&oacute;n</span>
                </li>
                <li>
                    <a href="<?= url('/especialistas/horarios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/horarios') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clock w-5"></i>
                        <span>Mis Horarios</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/especialistas/mis-servicios') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/mis-servicios') !== false ? 'active' : '' ?>">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span>Mis Servicios</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/pagos') ?>" class="sidebar-link flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/pagos') !== false ? 'active' : '' ?>">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span>Pagos</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    
    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('user-menu');
            const menuBtn = document.getElementById('user-menu-btn');
            
            // Close if click is outside both the menu and the button
            if (menu && menuBtn && !menu.contains(e.target) && !menuBtn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
        
        // Mobile menu functions
        function closeMobileMenu() {
            document.getElementById('mobile-sidebar').classList.add('-translate-x-full');
            document.getElementById('mobile-menu-overlay').classList.add('hidden');
        }
        
        function openMobileMenu() {
            document.getElementById('mobile-sidebar').classList.remove('-translate-x-full');
            document.getElementById('mobile-menu-overlay').classList.remove('hidden');
        }
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', openMobileMenu);
        
        document.getElementById('close-mobile-menu')?.addEventListener('click', closeMobileMenu);
        
        document.getElementById('mobile-menu-overlay')?.addEventListener('click', closeMobileMenu);
        
        // Close mobile menu when clicking any link inside it
        document.querySelectorAll('#mobile-sidebar a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    </script>
</body>
</html>
