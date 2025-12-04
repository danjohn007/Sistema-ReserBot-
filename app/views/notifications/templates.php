<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Plantillas de Notificaciones</h2>
                <p class="text-sm text-gray-500 mt-1">Personaliza los mensajes automáticos del sistema</p>
            </div>
            <button onclick="openTemplateModal()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <i class="fas fa-plus mr-2"></i>Nueva Plantilla
            </button>
        </div>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($templates)): ?>
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-bell-slash text-4xl mb-4"></i>
            <p>No hay plantillas configuradas</p>
            <p class="text-sm mt-2">Crea plantillas personalizadas para cada tipo de notificación</p>
        </div>
        <?php else: ?>
        
        <!-- Agrupar por tipo -->
        <?php 
        $groupedTemplates = [];
        foreach ($templates as $template) {
            $groupedTemplates[$template['tipo']][] = $template;
        }
        
        $tipoLabels = [
            'cita_nueva' => 'Cita Nueva',
            'confirmacion' => 'Confirmación',
            'recordatorio_24h' => 'Recordatorio 24h',
            'recordatorio_1h' => 'Recordatorio 1h',
            'cancelacion' => 'Cancelación',
            'reprogramacion' => 'Reprogramación'
        ];
        
        $canalLabels = [
            'email' => 'Email',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp'
        ];
        
        $canalIcons = [
            'email' => 'fa-envelope',
            'sms' => 'fa-comment-sms',
            'whatsapp' => 'fa-brands fa-whatsapp'
        ];
        ?>
        
        <?php foreach ($groupedTemplates as $tipo => $tipoTemplates): ?>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">
                <?= e($tipoLabels[$tipo] ?? ucfirst($tipo)) ?>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($tipoTemplates as $template): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-primary mr-3">
                                <i class="fas <?= $canalIcons[$template['canal']] ?? 'fa-bell' ?>"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800"><?= e($canalLabels[$template['canal']] ?? ucfirst($template['canal'])) ?></h4>
                                <?php if ($template['sucursal_nombre']): ?>
                                <p class="text-xs text-gray-500"><?= e($template['sucursal_nombre']) ?></p>
                                <?php else: ?>
                                <p class="text-xs text-gray-500">Global</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full <?= $template['activo'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                            <?= $template['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </div>
                    
                    <?php if ($template['canal'] == 'email' && $template['asunto']): ?>
                    <div class="mb-2">
                        <p class="text-xs text-gray-500">Asunto:</p>
                        <p class="text-sm font-medium text-gray-700"><?= e($template['asunto']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Contenido:</p>
                        <p class="text-sm text-gray-600 line-clamp-3"><?= e(substr($template['contenido'], 0, 100)) ?><?= strlen($template['contenido']) > 100 ? '...' : '' ?></p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="editTemplate(<?= htmlspecialchars(json_encode($template), ENT_QUOTES) ?>)" 
                                class="flex-1 px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                        <button onclick="toggleTemplate(<?= $template['id'] ?>, <?= $template['activo'] ? 0 : 1 ?>)" 
                                class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                            <i class="fas fa-toggle-<?= $template['activo'] ? 'on' : 'off' ?>"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- Modal para crear/editar plantilla -->
<div id="templateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Nueva Plantilla</h3>
                <button onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form method="POST" action="<?= url('/notificaciones/plantillas') ?>" id="templateForm">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="template_id">
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Notificación *</label>
                            <select name="tipo" id="tipo" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">-- Seleccione --</option>
                                <option value="cita_nueva">Cita Nueva</option>
                                <option value="confirmacion">Confirmación</option>
                                <option value="recordatorio_24h">Recordatorio 24h</option>
                                <option value="recordatorio_1h">Recordatorio 1h</option>
                                <option value="cancelacion">Cancelación</option>
                                <option value="reprogramacion">Reprogramación</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Canal *</label>
                            <select name="canal" id="canal" required onchange="toggleAsunto()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">-- Seleccione --</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Global (todas las sucursales)</option>
                            <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= e($branch['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="asuntoField" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asunto del Email</label>
                        <input type="text" name="asunto" id="asunto"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Asunto del correo electrónico">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contenido del Mensaje *</label>
                        <textarea name="contenido" id="contenido" rows="8" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                  placeholder="Contenido de la notificación..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Variables disponibles: {cliente_nombre}, {fecha_cita}, {hora_cita}, {especialista_nombre}, {servicio_nombre}, {sucursal_nombre}
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeTemplateModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-save mr-2"></i>Guardar Plantilla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTemplateModal() {
    document.getElementById('templateModal').classList.remove('hidden');
    document.getElementById('templateForm').reset();
    document.getElementById('template_id').value = '';
    document.getElementById('modalTitle').textContent = 'Nueva Plantilla';
}

function closeTemplateModal() {
    document.getElementById('templateModal').classList.add('hidden');
}

function editTemplate(template) {
    document.getElementById('templateModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Editar Plantilla';
    document.getElementById('template_id').value = template.id;
    document.getElementById('tipo').value = template.tipo;
    document.getElementById('canal').value = template.canal;
    document.getElementById('asunto').value = template.asunto || '';
    document.getElementById('contenido').value = template.contenido;
    document.getElementById('sucursal_id').value = template.sucursal_id || '';
    toggleAsunto();
}

function toggleAsunto() {
    const canal = document.getElementById('canal').value;
    const asuntoField = document.getElementById('asuntoField');
    if (canal === 'email') {
        asuntoField.style.display = 'block';
    } else {
        asuntoField.style.display = 'none';
    }
}

function toggleTemplate(id, activo) {
    if (confirm('¿Desea ' + (activo ? 'activar' : 'desactivar') + ' esta plantilla?')) {
        fetch('<?= url('/notificaciones/plantillas') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle&id=${id}&activo=${activo}`
        }).then(() => location.reload());
    }
}

// Close modal on outside click
document.getElementById('templateModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTemplateModal();
    }
});
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
