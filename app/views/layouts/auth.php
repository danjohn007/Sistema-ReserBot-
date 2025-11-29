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
    
    <style>
        :root {
            --color-primary: <?= getConfig('color_primario', '#3B82F6') ?>;
            --color-secondary: <?= getConfig('color_secundario', '#1E40AF') ?>;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= getConfig('color_primario', '#3B82F6') ?>',
                        secondary: '<?= getConfig('color_secundario', '#1E40AF') ?>'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <?php if (getConfig('logotipo')): ?>
                <img src="<?= asset(getConfig('logotipo')) ?>" alt="Logo" class="h-16 mx-auto mb-4">
            <?php else: ?>
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <i class="fas fa-calendar-check text-4xl text-primary"></i>
                    <span class="text-3xl font-bold text-gray-800"><?= e(getConfig('nombre_sitio', 'ReserBot')) ?></span>
                </div>
            <?php endif; ?>
            <p class="text-gray-600">Sistema de Reservaciones y Citas Profesionales</p>
        </div>
        
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <?php include $content; ?>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date('Y') ?> <?= e(getConfig('nombre_sitio', 'ReserBot')) ?>. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>
