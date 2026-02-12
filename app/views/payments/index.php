<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    Mis Pagos
                </h2>
                <p class="text-green-100 mt-2">Gestiona los pagos de tus reservaciones completadas</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold">
                    <?= formatMoney($totalGeneral) ?>
                </div>
                <p class="text-sm text-green-100">Total de ingresos</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" action="<?= url('/pagos') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Sucursal -->
                <?php if (!empty($branches) && count($branches) > 1): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-gray-400 mr-1"></i>Sucursal
                    </label>
                    <select name="sucursal_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Todas las sucursales</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= $filters['sucursal_id'] == $branch['id'] ? 'selected' : '' ?>>
                            <?= e($branch['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-gray-400 mr-1"></i>Desde
                    </label>
                    <input type="date" name="fecha_desde" value="<?= e($filters['fecha_desde'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-gray-400 mr-1"></i>Hasta
                    </label>
                    <input type="date" name="fecha_hasta" value="<?= e($filters['fecha_hasta'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <!-- Método de pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card text-gray-400 mr-1"></i>Método de Pago
                    </label>
                    <select name="metodo_pago" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Todos los métodos</option>
                        <option value="efectivo" <?= $filters['metodo_pago'] == 'efectivo' ? 'selected' : '' ?>>💵 Efectivo</option>
                        <option value="tarjeta" <?= $filters['metodo_pago'] == 'tarjeta' ? 'selected' : '' ?>>💳 Tarjeta</option>
                        <option value="transferencia" <?= $filters['metodo_pago'] == 'transferencia' ? 'selected' : '' ?>>🏦 Transferencia</option>
                        <option value="paypal" <?= $filters['metodo_pago'] == 'paypal' ? 'selected' : '' ?>>🅿️ PayPal</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
                <a href="<?= url('/pagos') ?>" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>Limpiar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- Resumen por método de pago -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">💵 Efectivo</p>
                    <p class="text-lg font-bold text-gray-900"><?= formatMoney($totalPorMetodo['efectivo']) ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">💳 Tarjeta</p>
                    <p class="text-lg font-bold text-gray-900"><?= formatMoney($totalPorMetodo['tarjeta']) ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">🏦 Transferencia</p>
                    <p class="text-lg font-bold text-gray-900"><?= formatMoney($totalPorMetodo['transferencia']) ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">🅿️ PayPal</p>
                    <p class="text-lg font-bold text-gray-900"><?= formatMoney($totalPorMetodo['paypal']) ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">❓ Sin Definir</p>
                    <p class="text-lg font-bold text-gray-900"><?= formatMoney($totalPorMetodo['sin_definir']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de pagos -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reservación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente / Servicio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sucursal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Método de Pago
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Referencia
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            <p class="text-lg">No hay pagos registrados</p>
                            <p class="text-sm mt-1">Los pagos se crean automáticamente cuando completas una reservación</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-receipt text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900"><?= e($payment['codigo']) ?></div>
                                        <div class="text-xs text-gray-500">ID: #<?= $payment['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= e($payment['cliente_nombre']) ?></div>
                                <div class="text-xs text-gray-500"><?= e($payment['servicio_nombre']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($payment['fecha_cita'])) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= date('g:i A', strtotime($payment['hora_inicio'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= e($payment['sucursal_nombre']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600"><?= formatMoney($payment['monto']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($payment['metodo_pago']): ?>
                                    <?php
                                    $metodoIcons = [
                                        'efectivo' => '💵',
                                        'tarjeta' => '💳',
                                        'transferencia' => '🏦',
                                        'paypal' => '🅿️'
                                    ];
                                    $metodoLabels = [
                                        'efectivo' => 'Efectivo',
                                        'tarjeta' => 'Tarjeta',
                                        'transferencia' => 'Transferencia',
                                        'paypal' => 'PayPal'
                                    ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?= $metodoIcons[$payment['metodo_pago']] ?? '💰' ?> 
                                        <?= $metodoLabels[$payment['metodo_pago']] ?? ucfirst($payment['metodo_pago']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        ❓ Sin definir
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?= $payment['referencia_pago'] ? e($payment['referencia_pago']) : '<span class="text-gray-400">-</span>' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="editPayment(<?= $payment['id'] ?>, '<?= $payment['metodo_pago'] ?? '' ?>', '<?= e($payment['referencia_pago'] ?? '') ?>', '<?= e($payment['notas'] ?? '') ?>')" 
                                        class="text-blue-600 hover:text-blue-900 transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para editar pago -->
<div id="editPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
        <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    <span>Editar Información del Pago</span>
                </h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        
        <form id="editPaymentForm" class="p-6 space-y-4">
            <!-- Método de pago -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-credit-card mr-1"></i>Método de Pago *
                </label>
                <select id="edit_metodo_pago" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Seleccione un método --</option>
                    <option value="efectivo">💵 Efectivo</option>
                    <option value="tarjeta">💳 Tarjeta</option>
                    <option value="transferencia">🏦 Transferencia</option>
                    <option value="paypal">🅿️ PayPal</option>
                </select>
            </div>
            
            <!-- Referencia -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-hashtag mr-1"></i>Referencia / Número de Transacción
                </label>
                <input type="text" id="edit_referencia_pago"
                       placeholder="Ej: TXN123456, #001234"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <p class="text-xs text-gray-500 mt-1">Opcional: número de transacción, folio, etc.</p>
            </div>
            
            <!-- Notas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-1"></i>Notas Adicionales
                </label>
                <textarea id="edit_notas" rows="2"
                          placeholder="Observaciones, detalles adicionales..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
            
            <input type="hidden" id="edit_pago_id" value="">
        </form>
        
        <div class="p-6 bg-gray-50 border-t rounded-b-2xl">
            <div class="flex justify-end gap-3">
                <button onclick="closeEditModal()" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button onclick="savePayment()" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function editPayment(pagoId, metodoPago, referencia, notas) {
    document.getElementById('edit_pago_id').value = pagoId;
    document.getElementById('edit_metodo_pago').value = metodoPago || '';
    document.getElementById('edit_referencia_pago').value = referencia || '';
    document.getElementById('edit_notas').value = notas || '';
    
    document.getElementById('editPaymentModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editPaymentModal').classList.add('hidden');
}

async function savePayment() {
    const pagoId = document.getElementById('edit_pago_id').value;
    const metodoPago = document.getElementById('edit_metodo_pago').value;
    const referencia = document.getElementById('edit_referencia_pago').value;
    const notas = document.getElementById('edit_notas').value;
    
    if (!metodoPago) {
        alert('Por favor selecciona un método de pago');
        return;
    }
    
    const formData = new URLSearchParams();
    formData.append('pago_id', pagoId);
    formData.append('metodo_pago', metodoPago);
    formData.append('referencia_pago', referencia);
    formData.append('notas', notas);
    
    try {
        const response = await fetch('<?= BASE_URL ?>/pagos/actualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Pago actualizado exitosamente');
            closeEditModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo actualizar el pago'));
        }
    } catch (error) {
        console.error('Error al actualizar pago:', error);
        alert('Error al actualizar el pago. Por favor intente nuevamente.');
    }
}

// Close modal on overlay click
document.getElementById('editPaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditModal();
});
</script>
