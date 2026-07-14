<?php 
$user = currentUser();
$rolId = $user['rol_id'];
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php if ($rolId == ROLE_SUPERADMIN): ?>
    <!-- Superadmin Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Sucursales</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalBranches ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Especialistas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalSpecialists ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Solicitudes Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $pendingRegistrationRequests ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clipboard-check text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Ingresos del Mes</p>
                <p class="text-3xl font-bold text-gray-800"><?= formatMoney($monthlyIncome ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_BRANCH_ADMIN || $rolId == ROLE_RECEPTIONIST): ?>
    <!-- Branch Admin Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Especialistas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalSpecialists ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Citas Hoy</p>
                <p class="text-3xl font-bold text-gray-800"><?= $todayAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $pendingAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Ingresos del Mes</p>
                <p class="text-3xl font-bold text-gray-800"><?= formatMoney($monthlyIncome ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_SPECIALIST): ?>
    <!-- Specialist Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Citas Hoy</p>
                <p class="text-3xl font-bold text-gray-800"><?= $todayAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?= $pendingAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Calificación</p>
                <p class="text-3xl font-bold text-gray-800">
                    <?= number_format($specialist['calificacion_promedio'] ?? 0, 1) ?>
                    <i class="fas fa-star text-yellow-400 text-lg"></i>
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($rolId == ROLE_CLIENT): ?>
    <!-- Client Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Citas</p>
                <p class="text-3xl font-bold text-gray-800"><?= $totalAppointments ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Próximas Citas</p>
                <p class="text-3xl font-bold text-gray-800"><?= count($upcomingAppointments ?? []) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="col-span-1 md:col-span-2">
        <a href="<?= url('/reservaciones/nueva') ?>" 
           class="block bg-primary hover:bg-secondary text-white rounded-xl shadow-sm p-6 text-center transition duration-200">
            <i class="fas fa-plus-circle text-4xl mb-2"></i>
            <p class="text-lg font-semibold">Agendar Nueva Cita</p>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if ($rolId == ROLE_SUPERADMIN || $rolId == ROLE_BRANCH_ADMIN || $rolId == ROLE_SPECIALIST): ?>
<!-- Botón de Métricas de Origen -->
<div class="mb-6">
    <a href="<?= url('/metricas/origen-reservas') ?>"
       class="inline-flex items-center gap-3 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-md hover:from-blue-700 hover:to-indigo-700 transition font-semibold">
        <i class="fas fa-chart-pie text-xl"></i>
        <span>Métricas de Origen de Reservas</span>
        <i class="fas fa-arrow-right text-sm opacity-75"></i>
    </a>
</div>
<?php endif; ?>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upcoming Appointments -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <?= $rolId == ROLE_CLIENT ? 'Mis Próximas Citas' : 'Próximas Citas' ?>
            </h3>
            <a href="<?= url($rolId == ROLE_CLIENT ? '/mis-citas' : '/reservaciones') ?>" class="text-primary text-sm hover:underline">
                Ver todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($upcomingAppointments)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>No hay citas próximas</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach (array_slice($upcomingAppointments, 0, 5) as $appt): ?>
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white mr-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800"><?= e($appt['servicio_nombre']) ?></p>
                    <p class="text-sm text-gray-500">
                        <?= formatDate($appt['fecha_cita']) ?> a las <?= formatTime($appt['hora_inicio']) ?>
                    </p>
                    <?php if ($rolId != ROLE_CLIENT): ?>
                    <p class="text-sm text-gray-500">
                        Cliente: <?= e($appt['cliente_nombre_completo']) ?>
                    </p>
                    <?php else: ?>
                    <p class="text-sm text-gray-500">
                        <?= e($appt['especialista_nombre'] . ' ' . $appt['especialista_apellidos']) ?>
                    </p>
                    <?php endif; ?>
                </div>
                <span class="px-3 py-1 text-xs rounded-full <?= getStatusBadgeClass($appt['estado']) ?>">
                    <?= getStatusText($appt['estado']) ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions or Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <?php if ($rolId == ROLE_SUPERADMIN || $rolId == ROLE_BRANCH_ADMIN): ?>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actividad del Mes</h3>
        <canvas id="monthlyChart" height="200"></canvas>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_map(function($s) { 
                            return date('M Y', strtotime($s['mes'] . '-01')); 
                        }, $monthlyStats ?? [])) ?>,
                        datasets: [{
                            label: 'Citas',
                            data: <?= json_encode(array_column($monthlyStats ?? [], 'total_citas')) ?>,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
        </script>
        <?php else: ?>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-2 gap-4">
            <?php if ($rolId == ROLE_CLIENT): ?>
            <a href="<?= url('/reservaciones/nueva') ?>" class="p-4 bg-blue-100 rounded-lg text-center hover:bg-blue-200 transition">
                <i class="fas fa-plus-circle text-3xl text-blue-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Nueva Cita</p>
            </a>
            <a href="<?= url('/mis-citas') ?>" class="p-4 bg-green-100 rounded-lg text-center hover:bg-green-200 transition">
                <i class="fas fa-calendar-alt text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Mis Citas</p>
            </a>
            <a href="<?= url('/perfil') ?>" class="p-4 bg-purple-100 rounded-lg text-center hover:bg-purple-200 transition">
                <i class="fas fa-user text-3xl text-purple-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Mi Perfil</p>
            </a>
            <a href="<?= url('/notificaciones') ?>" class="p-4 bg-yellow-100 rounded-lg text-center hover:bg-yellow-200 transition">
                <i class="fas fa-bell text-3xl text-yellow-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Notificaciones</p>
            </a>
            <?php elseif ($rolId == ROLE_SPECIALIST): ?>
            <a href="<?= url('/calendario') ?>" class="p-4 bg-blue-100 rounded-lg text-center hover:bg-blue-200 transition">
                <i class="fas fa-calendar-week text-3xl text-blue-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Calendario</p>
            </a>
            <a href="<?= url('/especialistas/horarios') ?>" class="p-4 bg-green-100 rounded-lg text-center hover:bg-green-200 transition">
                <i class="fas fa-clock text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Mis Horarios</p>
            </a>
            <a href="<?= url('/ia') ?>" class="p-4 bg-indigo-100 rounded-lg text-center hover:bg-indigo-200 transition">
                <i class="fas fa-robot text-3xl text-indigo-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Config. IA</p>
            </a>
            <a href="<?= url('/reservaciones') ?>" class="p-4 bg-purple-100 rounded-lg text-center hover:bg-purple-200 transition">
                <i class="fas fa-list text-3xl text-purple-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Mis Citas</p>
            </a>
            <a href="<?= url('/perfil') ?>" class="p-4 bg-yellow-100 rounded-lg text-center hover:bg-yellow-200 transition">
                <i class="fas fa-user text-3xl text-yellow-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Mi Perfil</p>
            </a>
            <a href="<?= url('/pagos') ?>" class="p-4 bg-emerald-100 rounded-lg text-center hover:bg-emerald-200 transition col-span-2">
                <i class="fas fa-money-bill-wave text-3xl text-emerald-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Administraci&oacute;n de pagos</p>
            </a>
            <?php
            $liga1 = e($specialist['nombre_liga1'] ?? 'Liga 1');
            $liga2 = e($specialist['nombre_liga2'] ?? 'Liga 2');
            $liga3 = e($specialist['nombre_liga3'] ?? 'Liga 3');
            $nombreEsp = e($user['nombre'] . ' ' . $user['apellidos']);
            $urlLiga1 = getWhatsAppUrl("Hola quiero reservar con " . $user["nombre"] . " " . $user["apellidos"]);
            $urlLiga2 = getWhatsAppUrl("Hola me gustaria reservar con " . $user["nombre"] . " " . $user["apellidos"]);
            $urlLiga3 = getWhatsAppUrl("Hola deseo reservar con " . $user["nombre"] . " " . $user["apellidos"]);
            ?>
            <button type="button" onclick="abrirLigaWhatsApp(this)"
                    data-liga-url="<?= e($urlLiga1) ?>" data-liga-nombre="<?= $liga1 ?>" aria-haspopup="dialog"
                    class="p-4 bg-green-100 rounded-lg text-center hover:bg-green-200 transition cursor-pointer w-full">
                <i class="fab fa-whatsapp text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800"><?= $liga1 ?></p>
            </button>
            <button type="button" onclick="abrirLigaWhatsApp(this)"
                    data-liga-url="<?= e($urlLiga2) ?>" data-liga-nombre="<?= $liga2 ?>" aria-haspopup="dialog"
                    class="p-4 bg-green-100 rounded-lg text-center hover:bg-green-200 transition cursor-pointer w-full">
                <i class="fab fa-whatsapp text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800"><?= $liga2 ?></p>
            </button>
            <button type="button" onclick="abrirLigaWhatsApp(this)"
                    data-liga-url="<?= e($urlLiga3) ?>" data-liga-nombre="<?= $liga3 ?>" aria-haspopup="dialog"
                    class="p-4 bg-pink-100 rounded-lg text-center hover:bg-pink-200 transition cursor-pointer w-full col-span-2">
                <i class="fab fa-whatsapp text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-800"><?= $liga3 ?></p>
            </button>

            <div id="modalCompartirLiga" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4"
                 role="dialog" aria-modal="true" aria-labelledby="tituloCompartirLiga">
                <div class="w-full max-w-md rounded-lg bg-white shadow-2xl" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                        <h3 id="tituloCompartirLiga" class="min-w-0 truncate text-base font-semibold text-gray-800">Compartir liga</h3>
                        <button type="button" onclick="cerrarLigaWhatsApp()"
                                class="ml-3 flex h-9 w-9 flex-shrink-0 items-center justify-center text-gray-500 hover:text-gray-800 transition"
                                aria-label="Cerrar">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-3 p-5">
                        <label for="urlCompartirLiga" class="sr-only">Liga de WhatsApp</label>
                        <input id="urlCompartirLiga" type="text" readonly onclick="this.select()"
                               class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                        <div class="grid grid-cols-2 gap-3">
                            <button id="btnCopiarLiga" type="button" onclick="copiarLigaDesdeModal()"
                                    class="flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-copy mr-2"></i><span>Copiar</span>
                            </button>
                            <button type="button" onclick="compartirLigaWhatsApp()"
                                    class="flex h-11 items-center justify-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700 transition">
                                <i class="fas fa-share-alt mr-2"></i><span>Compartir</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón para editar nombres -->
            <div class="col-span-2 flex justify-end">
                <button onclick="document.getElementById('formNombresLigas').classList.toggle('hidden')"
                        class="text-xs text-gray-400 hover:text-gray-600 transition flex items-center gap-1">
                    <i class="fas fa-pen text-xs"></i> Personalizar nombres de ligas
                </button>
            </div>

            <!-- Formulario inline de edición -->
            <div id="formNombresLigas" class="col-span-2 hidden bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nombres de las ligas</p>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Liga 1</label>
                        <input id="inp_liga1" type="text" maxlength="60" value="<?= $liga1 ?>"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Liga 2</label>
                        <input id="inp_liga2" type="text" maxlength="60" value="<?= $liga2 ?>"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Liga 3</label>
                        <input id="inp_liga3" type="text" maxlength="60" value="<?= $liga3 ?>"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button onclick="document.getElementById('formNombresLigas').classList.add('hidden')"
                            class="text-sm px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                        Cancelar
                    </button>
                    <button onclick="guardarNombresLigas()"
                            id="btnGuardarLigas"
                            class="text-sm px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>

            <script>
            let ligaWhatsAppActual = '';
            let nombreLigaActual = '';
            let botonLigaActual = null;

            function abrirLigaWhatsApp(btn) {
                ligaWhatsAppActual = btn.dataset.ligaUrl;
                nombreLigaActual = btn.dataset.ligaNombre;
                botonLigaActual = btn;

                document.getElementById('tituloCompartirLiga').textContent = nombreLigaActual;
                document.getElementById('urlCompartirLiga').value = ligaWhatsAppActual;

                const modal = document.getElementById('modalCompartirLiga');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                setTimeout(function() {
                    document.getElementById('urlCompartirLiga').focus();
                }, 0);
            }

            function cerrarLigaWhatsApp() {
                const modal = document.getElementById('modalCompartirLiga');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');

                if (botonLigaActual) {
                    botonLigaActual.focus();
                }
            }

            function copiarTextoLiga(url) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(url);
                }

                const ta = document.createElement('textarea');
                ta.value = url;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                return Promise.resolve();
            }

            function copiarLigaDesdeModal() {
                copiarTextoLiga(ligaWhatsAppActual).then(function() {
                    const btn = document.getElementById('btnCopiarLiga');
                    btn.innerHTML = '<i class="fas fa-check mr-2 text-green-600"></i><span>Copiado</span>';
                    btn.classList.add('border-green-400', 'text-green-700', 'bg-green-50');

                    setTimeout(function() {
                        btn.innerHTML = '<i class="fas fa-copy mr-2"></i><span>Copiar</span>';
                        btn.classList.remove('border-green-400', 'text-green-700', 'bg-green-50');
                    }, 1800);
                }).catch(function() {
                    alert('No se pudo copiar la liga. Selecciona el texto y cópialo manualmente.');
                });
            }

            async function compartirLigaWhatsApp() {
                const datosCompartir = {
                    title: nombreLigaActual,
                    text: 'Reserva tu cita desde esta liga:',
                    url: ligaWhatsAppActual
                };

                if (navigator.share) {
                    try {
                        await navigator.share(datosCompartir);
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            alert('No se pudo abrir el menú para compartir.');
                        }
                    }
                    return;
                }

                const texto = datosCompartir.text + ' ' + ligaWhatsAppActual;
                window.open('https://wa.me/?text=' + encodeURIComponent(texto), '_blank', 'noopener,noreferrer');
            }

            document.getElementById('modalCompartirLiga').addEventListener('click', cerrarLigaWhatsApp);
            document.addEventListener('keydown', function(event) {
                const modal = document.getElementById('modalCompartirLiga');
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    cerrarLigaWhatsApp();
                }
            });

            function guardarNombresLigas() {
                const btn = document.getElementById('btnGuardarLigas');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

                const body = new URLSearchParams({
                    nombre_liga1: document.getElementById('inp_liga1').value,
                    nombre_liga2: document.getElementById('inp_liga2').value,
                    nombre_liga3: document.getElementById('inp_liga3').value,
                });

                fetch('<?= BASE_URL ?>/dashboard/guardar-nombres-ligas', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString()
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al guardar: ' + (data.message || 'Intenta de nuevo'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar';
                    }
                })
                .catch(() => {
                    alert('Error de conexión');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar';
                });
            }
            </script>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($rolId == ROLE_CLIENT && !empty($pastAppointments)): ?>
<!-- Past Appointments for Client -->
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Citas</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialista</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($pastAppointments as $appt): ?>
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <?= formatDate($appt['fecha_cita']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700"><?= e($appt['servicio_nombre']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <?= e($appt['especialista_nombre'] . ' ' . $appt['especialista_apellidos']) ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full <?= getStatusBadgeClass($appt['estado']) ?>">
                            <?= getStatusText($appt['estado']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
