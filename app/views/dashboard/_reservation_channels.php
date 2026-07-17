<?php
$urlInstagram = getWhatsAppUrl("Hola me gustaria reservar con " . $user["nombre"] . " " . $user["apellidos"]);
$urlFacebook = getWhatsAppUrl("Hola deseo reservar con " . $user["nombre"] . " " . $user["apellidos"]);
$urlLandingWhatsApp = $landingStatus['whatsapp_url'] ?? getWhatsAppUrl("Hola quiero reservar con " . $user["nombre"] . " " . $user["apellidos"]);
$landingUrl = $landingStatus['public_url'] ?? '';
$landingAvailable = $landingStatus['available'] ?? false;
?>

<div class="mt-auto border-t border-gray-200 pt-6">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h4 class="font-semibold text-gray-900">Canales de reserva</h4>
            <p class="text-xs text-gray-500">Ligas separadas para identificar el origen de cada cita</p>
        </div>
        <a href="<?= url('/metricas/origen-reservas') ?>" class="text-sm font-semibold text-primary hover:underline">Ver m&eacute;tricas</a>
    </div>

    <?php if ($landingUrl): ?>
    <button type="button" onclick="abrirCanalReserva(this)"
            data-channel-url="<?= e($landingUrl) ?>" data-channel-name="Landing" aria-haspopup="dialog"
            class="mb-3 flex min-h-[92px] w-full items-center gap-4 rounded-lg border border-cyan-200 bg-cyan-50 p-4 text-left transition hover:border-cyan-300 hover:bg-cyan-100">
        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-cyan-700 text-white"><i class="fas fa-window-maximize text-xl"></i></span>
        <span class="min-w-0 flex-1">
            <span class="flex flex-wrap items-center gap-2"><span class="font-semibold text-gray-900">Landing</span><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Publicada</span></span>
            <span class="mt-1 block text-xs text-gray-600">P&aacute;gina principal para compartir con pacientes</span>
        </span>
        <i class="fas fa-share-nodes text-cyan-700"></i>
    </button>
    <?php else: ?>
    <a href="<?= url('/mi-landing') ?>" class="mb-3 flex min-h-[92px] w-full items-center gap-4 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-left transition hover:border-cyan-400 hover:bg-cyan-50">
        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gray-200 text-gray-600"><i class="fas fa-window-maximize text-xl"></i></span>
        <span class="min-w-0 flex-1">
            <span class="flex flex-wrap items-center gap-2"><span class="font-semibold text-gray-900">Landing</span><span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600"><?= $landingAvailable ? 'Sin crear' : 'Conexi&oacute;n pendiente' ?></span></span>
            <span class="mt-1 block text-xs text-gray-600">Crea una p&aacute;gina sencilla con servicios y ubicaciones</span>
        </span>
        <i class="fas fa-chevron-right text-gray-400"></i>
    </a>
    <?php endif; ?>

    <div class="mb-3 grid grid-cols-1 gap-3">
        <button type="button" onclick="abrirCanalReserva(this)" data-channel-url="<?= e($urlLandingWhatsApp) ?>" data-channel-name="WhatsApp de la landing" aria-haspopup="dialog"
                class="flex min-h-[88px] w-full items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-left transition hover:border-emerald-300 hover:bg-emerald-100">
            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white"><i class="fab fa-whatsapp text-xl"></i></span>
            <span class="min-w-0 flex-1"><span class="block font-semibold text-gray-900">WhatsApp de la landing</span><span class="mt-1 block text-xs text-gray-600">Liga del bot&oacute;n &ldquo;Reservar cita&rdquo; de la p&aacute;gina</span></span>
            <i class="fas fa-share-nodes text-emerald-600"></i>
        </button>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <button type="button" onclick="abrirCanalReserva(this)" data-channel-url="<?= e($urlInstagram) ?>" data-channel-name="Instagram" aria-haspopup="dialog"
                class="flex min-h-[88px] w-full items-center gap-3 rounded-lg border border-pink-200 bg-pink-50 p-4 text-left transition hover:border-pink-300 hover:bg-pink-100">
            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-pink-600 text-white"><i class="fab fa-instagram text-xl"></i></span>
            <span class="min-w-0 flex-1"><span class="block font-semibold text-gray-900">Instagram</span><span class="mt-1 block text-xs text-gray-600">Perfil, historias y mensajes</span></span>
            <i class="fas fa-share-nodes text-pink-600"></i>
        </button>
        <button type="button" onclick="abrirCanalReserva(this)" data-channel-url="<?= e($urlFacebook) ?>" data-channel-name="Facebook" aria-haspopup="dialog"
                class="flex min-h-[88px] w-full items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-left transition hover:border-blue-300 hover:bg-blue-100">
            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white"><i class="fab fa-facebook-f text-xl"></i></span>
            <span class="min-w-0 flex-1"><span class="block font-semibold text-gray-900">Facebook</span><span class="mt-1 block text-xs text-gray-600">P&aacute;gina, publicaciones y anuncios</span></span>
            <i class="fas fa-share-nodes text-blue-600"></i>
        </button>
    </div>
    <div class="mt-3 flex justify-end">
        <a href="<?= url('/mi-landing') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-primary"><i class="fas fa-pen"></i><span>Administrar landing</span></a>
    </div>
</div>

<div id="modalCompartirLiga" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4"
     role="dialog" aria-modal="true" aria-labelledby="tituloCompartirLiga">
    <div class="w-full max-w-md rounded-lg bg-white shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h3 id="tituloCompartirLiga" class="min-w-0 truncate text-base font-semibold text-gray-800">Compartir liga</h3>
            <button type="button" onclick="cerrarCanalReserva()"
                    class="ml-3 flex h-9 w-9 flex-shrink-0 items-center justify-center text-gray-500 transition hover:text-gray-800"
                    aria-label="Cerrar">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="space-y-3 p-5">
            <label for="urlCompartirLiga" class="sr-only">Liga para compartir</label>
            <input id="urlCompartirLiga" type="text" readonly onclick="this.select()"
                   class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
            <div class="grid grid-cols-2 gap-3">
                <button id="btnCopiarLiga" type="button" onclick="copiarLigaDesdeModal()"
                        class="flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <i class="fas fa-copy mr-2"></i><span>Copiar</span>
                </button>
                <button type="button" onclick="compartirCanalReserva()"
                        class="flex h-11 items-center justify-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-share-alt mr-2"></i><span>Compartir</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let ligaCanalActual = '';
let nombreLigaActual = '';
let botonLigaActual = null;

function abrirCanalReserva(btn) {
    ligaCanalActual = btn.dataset.channelUrl;
    nombreLigaActual = btn.dataset.channelName;
    botonLigaActual = btn;
    document.getElementById('tituloCompartirLiga').textContent = nombreLigaActual;
    document.getElementById('urlCompartirLiga').value = ligaCanalActual;

    const modal = document.getElementById('modalCompartirLiga');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    setTimeout(function() { document.getElementById('urlCompartirLiga').focus(); }, 0);
}

function cerrarCanalReserva() {
    const modal = document.getElementById('modalCompartirLiga');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
    if (botonLigaActual) botonLigaActual.focus();
}

function copiarTextoLiga(url) {
    if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(url);
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
    copiarTextoLiga(ligaCanalActual).then(function() {
        const btn = document.getElementById('btnCopiarLiga');
        btn.innerHTML = '<i class="fas fa-check mr-2 text-green-600"></i><span>Copiado</span>';
        btn.classList.add('border-green-400', 'text-green-700', 'bg-green-50');
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-copy mr-2"></i><span>Copiar</span>';
            btn.classList.remove('border-green-400', 'text-green-700', 'bg-green-50');
        }, 1800);
    }).catch(function() {
        alert('No se pudo copiar la liga. Selecciona el texto y copialo manualmente.');
    });
}

async function compartirCanalReserva() {
    const datosCompartir = { title: nombreLigaActual, text: 'Reserva tu cita desde esta liga:', url: ligaCanalActual };
    if (navigator.share) {
        try {
            await navigator.share(datosCompartir);
        } catch (error) {
            if (error.name !== 'AbortError') alert('No se pudo abrir el menu para compartir.');
        }
        return;
    }
    window.open('https://wa.me/?text=' + encodeURIComponent(datosCompartir.text + ' ' + ligaCanalActual), '_blank', 'noopener,noreferrer');
}

document.getElementById('modalCompartirLiga').addEventListener('click', cerrarCanalReserva);
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('modalCompartirLiga');
    if (event.key === 'Escape' && !modal.classList.contains('hidden')) cerrarCanalReserva();
});
</script>
