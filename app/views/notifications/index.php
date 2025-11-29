<h2 class="text-2xl font-bold text-gray-800 mb-6">Notificaciones</h2>

<?php if (empty($notifications)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700">No hay notificaciones</h3>
    <p class="text-gray-500 mt-2">Las notificaciones aparecerán aquí</p>
</div>
<?php else: ?>
<div class="space-y-4">
    <?php foreach ($notifications as $notif): ?>
    <div class="bg-white rounded-xl shadow-sm p-4 <?= $notif['leida'] ? 'opacity-75' : '' ?>">
        <div class="flex items-start">
            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                <?php
                $iconClass = 'bg-blue-100 text-blue-600';
                $icon = 'fa-bell';
                switch ($notif['tipo']) {
                    case 'cita_nueva': $iconClass = 'bg-green-100 text-green-600'; $icon = 'fa-calendar-plus'; break;
                    case 'cita_confirmada': $iconClass = 'bg-green-100 text-green-600'; $icon = 'fa-check-circle'; break;
                    case 'cita_cancelada': $iconClass = 'bg-red-100 text-red-600'; $icon = 'fa-calendar-times'; break;
                    case 'cita_reprogramada': $iconClass = 'bg-yellow-100 text-yellow-600'; $icon = 'fa-calendar-alt'; break;
                    case 'recordatorio': $iconClass = 'bg-yellow-100 text-yellow-600'; $icon = 'fa-clock'; break;
                }
                echo $iconClass;
                ?>">
                <i class="fas <?= $icon ?>"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-semibold text-gray-800"><?= e($notif['titulo']) ?></h4>
                <p class="text-gray-600 text-sm mt-1"><?= e($notif['mensaje']) ?></p>
                <p class="text-xs text-gray-400 mt-2"><?= timeAgo($notif['created_at']) ?></p>
            </div>
            <?php if ($notif['enlace']): ?>
            <a href="<?= url($notif['enlace']) ?>" class="text-primary hover:text-secondary">
                <i class="fas fa-arrow-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
