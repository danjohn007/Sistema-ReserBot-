<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Interesados - ReserBot</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    
    <!-- Header -->
    <div class="bg-white shadow-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="/chatbot/public/images/logo.png" alt="Logo" class="h-14 w-auto object-contain">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Registros de Interesados</h1>
                        <p class="text-sm text-gray-500 mt-1">Vista pública de prospectos registrados</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Última actualización</div>
                    <div class="text-lg font-semibold text-gray-700"><?php echo date('d/m/Y H:i'); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if (isset($error)): ?>
            <!-- Error Message -->
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <!-- Total -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-gray-500 card-hover fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total</p>
                        <p class="text-3xl font-bold text-gray-700 mt-1"><?php echo $estadisticas['total']; ?></p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-database text-gray-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pendiente -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-yellow-500 card-hover fade-in" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pendiente</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1"><?php echo $estadisticas['pendiente']; ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Contactado -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500 card-hover fade-in" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Contactado</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1"><?php echo $estadisticas['contactado']; ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-phone text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Completado -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500 card-hover fade-in" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completado</p>
                        <p class="text-3xl font-bold text-green-600 mt-1"><?php echo $estadisticas['completado']; ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Rechazado -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-red-500 card-hover fade-in" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Rechazado</p>
                        <p class="text-3xl font-bold text-red-600 mt-1"><?php echo $estadisticas['rechazado']; ?></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Registros -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in" style="animation-delay: 0.5s;">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    Lista de Registros
                </h2>
            </div>
            
            <?php if (empty($registros)): ?>
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay registros disponibles</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Teléfono
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Notas
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Fecha Registro
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Acción
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($registros as $registro): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#<?php echo $registro['id']; ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                                    <?php 
                                                    $nombre = $registro['nombre'];
                                                    $iniciales = '';
                                                    $palabras = explode(' ', $nombre);
                                                    foreach ($palabras as $palabra) {
                                                        if (!empty($palabra)) {
                                                            $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
                                                            if (strlen($iniciales) >= 2) break;
                                                        }
                                                    }
                                                    echo $iniciales;
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($nombre); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?php if (!empty($registro['email'])): ?>
                                                <a href="mailto:<?php echo htmlspecialchars($registro['email']); ?>" class="text-blue-600 hover:underline">
                                                    <i class="fas fa-envelope mr-1 text-gray-400"></i><?php echo htmlspecialchars($registro['email']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            <?php echo htmlspecialchars($registro['telefono'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $estado = strtolower($registro['estado']);
                                        $estadoConfig = [
                                            'pendiente' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Pendiente'],
                                            'contactado' => ['color' => 'blue', 'icon' => 'phone', 'text' => 'Contactado'],
                                            'completado' => ['color' => 'green', 'icon' => 'check-circle', 'text' => 'Completado'],
                                            'rechazado' => ['color' => 'red', 'icon' => 'times-circle', 'text' => 'Rechazado']
                                        ];
                                        
                                        $config = $estadoConfig[$estado] ?? ['color' => 'gray', 'icon' => 'question', 'text' => ucfirst($estado)];
                                        $color = $config['color'];
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-800 border border-<?php echo $color; ?>-300">
                                            <i class="fas fa-<?php echo $config['icon']; ?> mr-1.5"></i>
                                            <?php echo $config['text']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($registro['notas'] ?? ''); ?>">
                                            <?php 
                                            $notas = $registro['notas'] ?? 'Sin notas';
                                            echo htmlspecialchars(strlen($notas) > 50 ? substr($notas, 0, 50) . '...' : $notas);
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700">
                                            <i class="far fa-calendar text-gray-400 mr-1"></i>
                                            <?php 
                                            $fecha = strtotime($registro['created_at']);
                                            echo date('d/m/Y', $fecha);
                                            ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <i class="far fa-clock text-gray-400 mr-1"></i>
                                            <?php echo date('H:i', $fecha); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($estado === 'pendiente'): ?>
                                            <button
                                                onclick="marcarContactado(<?php echo $registro['id']; ?>, this)"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                                <i class="fas fa-phone-alt mr-1.5"></i>Marcar Contactado
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>
                <i class="fas fa-info-circle mr-1"></i>
                Esta es una vista pública de solo lectura. Para gestionar los registros, accede al panel de administración.
            </p>
        </div>
        
    </div>

    <script>
        function marcarContactado(id, btn) {
            if (!confirm('¿Marcar este registro como Contactado?')) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i>Guardando...';

            fetch('/chatbot/interesados/contactar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Reemplazar botón por badge de contactado
                    btn.parentElement.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-300"><i class="fas fa-check mr-1.5"></i>Actualizado</span>';
                    // Actualizar el badge de estado en la misma fila
                    const row = btn.closest('tr');
                    const estadoCell = row.querySelector('td:nth-child(5)');
                    if (estadoCell) {
                        estadoCell.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-300"><i class="fas fa-phone mr-1.5"></i>Contactado</span>';
                    }
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Intenta de nuevo'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-phone-alt mr-1.5"></i>Marcar Contactado';
                }
            })
            .catch(() => {
                alert('Error de conexión');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-phone-alt mr-1.5"></i>Marcar Contactado';
            });
        }
    </script>

</body>
</html>
