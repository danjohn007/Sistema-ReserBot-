<?php
/**
 * ReserBot - Vista Pública de Registros Interesados
 * Esta página es accesible sin autenticación
 */

// Establecer codificación UTF-8
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Obtener conexión a la base de datos
$db = Database::getInstance();

// Obtener todos los registros de interesados
try {
    $registros = $db->fetchAll(
        "SELECT id, nombre, nombre_restaurante, telefono, estado, notas, created_at, updated_at 
         FROM registros_interesados 
         ORDER BY created_at DESC"
    );
} catch (Exception $e) {
    $error = "Error al cargar los registros: " . $e->getMessage();
    $registros = [];
}

// Función para formatear fecha
function formatearFecha($fecha) {
    if (!$fecha) return 'N/A';
    $timestamp = strtotime($fecha);
    return date('d/m/Y H:i', $timestamp);
}

// Función para obtener clase CSS según el estado
function getEstadoClass($estado) {
    switch(strtolower($estado)) {
        case 'pendiente':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'contactado':
            return 'bg-blue-100 text-blue-800 border-blue-300';
        case 'completado':
            return 'bg-green-100 text-green-800 border-green-300';
        case 'rechazado':
            return 'bg-red-100 text-red-800 border-red-300';
        default:
            return 'bg-gray-100 text-gray-800 border-gray-300';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Interesados - <?= APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-users mr-3"></i>
                        Registros de Interesados
                    </h1>
                    <p class="text-blue-100 mt-1">Lista de personas interesadas en el sistema</p>
                </div>
                <div class="text-white text-right">
                    <p class="text-sm opacity-75">Total de registros</p>
                    <p class="text-3xl font-bold"><?= count($registros) ?></p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                    <div>
                        <p class="font-semibold">Error</p>
                        <p class="text-sm"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($registros)): ?>
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-700 mb-2">No hay registros</h3>
                <p class="text-gray-500">Aún no se han registrado personas interesadas.</p>
            </div>
        <?php else: ?>
            <!-- Tabla de Registros -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-hashtag mr-2"></i>ID
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-user mr-2"></i>Nombre
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-store mr-2"></i>Restaurante
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-phone mr-2"></i>Teléfono
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-flag mr-2"></i>Estado
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-2"></i>Fecha Registro
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <i class="fas fa-sticky-note mr-2"></i>Notas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($registros as $registro): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($registro['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($registro['nombre']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?= htmlspecialchars($registro['nombre_restaurante']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 flex items-center">
                                            <i class="fas fa-phone text-green-500 mr-2"></i>
                                            <?= htmlspecialchars($registro['telefono']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?= getEstadoClass($registro['estado']) ?>">
                                            <?= ucfirst(htmlspecialchars($registro['estado'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-700">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?= formatearFecha($registro['created_at']) ?>
                                            </span>
                                            <?php if ($registro['updated_at'] != $registro['created_at']): ?>
                                                <span class="text-xs text-gray-400 mt-1">
                                                    <i class="fas fa-sync-alt mr-1"></i>
                                                    Act: <?= formatearFecha($registro['updated_at']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php if (!empty($registro['notas'])): ?>
                                            <div class="max-w-xs truncate" title="<?= htmlspecialchars($registro['notas']) ?>">
                                                <?= htmlspecialchars($registro['notas']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Sin notas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estadísticas por Estado -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <?php
                $estadisticas = [
                    'pendiente' => 0,
                    'contactado' => 0,
                    'completado' => 0,
                    'rechazado' => 0
                ];
                
                foreach ($registros as $registro) {
                    $estado = strtolower($registro['estado']);
                    if (isset($estadisticas[$estado])) {
                        $estadisticas[$estado]++;
                    }
                }
                ?>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-800 text-sm font-semibold uppercase">Pendientes</p>
                            <p class="text-3xl font-bold text-yellow-900 mt-1"><?= $estadisticas['pendiente'] ?></p>
                        </div>
                        <i class="fas fa-clock text-4xl text-yellow-400"></i>
                    </div>
                </div>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-800 text-sm font-semibold uppercase">Contactados</p>
                            <p class="text-3xl font-bold text-blue-900 mt-1"><?= $estadisticas['contactado'] ?></p>
                        </div>
                        <i class="fas fa-phone-alt text-4xl text-blue-400"></i>
                    </div>
                </div>
                
                <div class="bg-green-50 border-l-4 border-green-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-800 text-sm font-semibold uppercase">Completados</p>
                            <p class="text-3xl font-bold text-green-900 mt-1"><?= $estadisticas['completado'] ?></p>
                        </div>
                        <i class="fas fa-check-circle text-4xl text-green-400"></i>
                    </div>
                </div>
                
                <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-800 text-sm font-semibold uppercase">Rechazados</p>
                            <p class="text-3xl font-bold text-red-900 mt-1"><?= $estadisticas['rechazado'] ?></p>
                        </div>
                        <i class="fas fa-times-circle text-4xl text-red-400"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-500 text-sm">
                <p>
                    <i class="far fa-copyright mr-1"></i>
                    <?= date('Y') ?> <?= APP_NAME ?> - Sistema de Gestión de Reservaciones
                </p>
                <p class="mt-1">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Última actualización: <?= date('d/m/Y H:i:s') ?>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
