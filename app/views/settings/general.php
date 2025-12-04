<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('/configuraciones') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Configuración General</h2>
        
        <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('/configuraciones/general') ?>" enctype="multipart/form-data">
            <div class="space-y-6">
                <!-- Site Name and Logo -->
                <div class="border-b pb-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Identidad del Sitio</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Sitio</label>
                            <input type="text" name="nombre_sitio" value="<?= e($settings['nombre_sitio'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logotipo</label>
                            <?php if (!empty($settings['logotipo'])): ?>
                            <div class="mb-2">
                                <img src="<?= asset($settings['logotipo']) ?>" alt="Logo actual" class="h-16">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="logotipo" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Tamaño recomendado: 200x60px</p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact -->
                <div class="border-b pb-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Información de Contacto</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email del Sistema</label>
                            <input type="email" name="email_sistema" value="<?= e($settings['email_sistema'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de Contacto</label>
                            <input type="tel" name="telefono_contacto" value="<?= e($settings['telefono_contacto'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="4421234567" maxlength="10" pattern="[0-9]{10}"
                                   title="Ingrese un número de 10 dígitos">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horario de Atención</label>
                            <input type="text" name="horario_atencion" value="<?= e($settings['horario_atencion'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="Ej: Lunes a Viernes 8:00 - 20:00">
                        </div>
                    </div>
                </div>
                
                <!-- Reservations Settings -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-4">Configuración de Reservaciones</h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="confirmacion_automatica" value="1" 
                                   <?= ($settings['confirmacion_automatica'] ?? '0') == '1' ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Confirmar citas automáticamente</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="recordatorio_24h" value="1" 
                                   <?= ($settings['recordatorio_24h'] ?? '1') == '1' ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Enviar recordatorio 24 horas antes</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="recordatorio_1h" value="1" 
                                   <?= ($settings['recordatorio_1h'] ?? '1') == '1' ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Enviar recordatorio 1 hora antes</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="permitir_cancelacion_cliente" value="1" 
                                   <?= ($settings['permitir_cancelacion_cliente'] ?? '1') == '1' ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Permitir que los clientes cancelen citas</span>
                        </label>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horas mínimas de anticipación para cancelar</label>
                            <input type="number" name="horas_anticipacion_cancelacion" min="0" 
                                   value="<?= e($settings['horas_anticipacion_cancelacion'] ?? '24') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
