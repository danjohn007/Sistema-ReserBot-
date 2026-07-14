<div class="mx-auto max-w-6xl">
    <div class="mb-6">
        <a href="<?= url('/solicitudes-registro') ?>" class="mb-2 inline-flex items-center text-sm text-gray-500 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a solicitudes
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Nueva solicitud</h2>
        <p class="text-sm text-gray-500">Todas las sucursales y profesionistas se revisaran como una sola unidad.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-700">
        <i class="fas fa-circle-exclamation mr-2"></i><?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('/solicitudes-registro/crear') ?>" id="registrationRequestForm" class="space-y-8">
        <section>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Sucursales</h3>
                    <p class="text-sm text-gray-500">Agrega todas las ubicaciones que formaran parte de esta solicitud.</p>
                </div>
                <button type="button" onclick="addBranch()" id="addBranchButton"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-primary px-4 text-sm font-semibold text-primary hover:bg-blue-50 transition">
                    <i class="fas fa-building-circle-check mr-2"></i>Agregar sucursal
                </button>
            </div>

            <div id="branchesContainer" class="space-y-4">
                <?php foreach ($branches as $index => $branch): ?>
                    <?php include __DIR__ . '/branch-fields.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Profesionistas</h3>
                    <p class="text-sm text-gray-500">Selecciona en cuales sucursales atendera cada profesionista.</p>
                </div>
                <button type="button" onclick="addProfessional()" id="addProfessionalButton"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-primary px-4 text-sm font-semibold text-primary hover:bg-blue-50 transition">
                    <i class="fas fa-user-plus mr-2"></i>Agregar profesionista
                </button>
            </div>

            <div id="professionalsContainer" class="space-y-4">
                <?php foreach ($professionals as $index => $professional): ?>
                    <?php include __DIR__ . '/professional-fields.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end">
            <a href="<?= url('/solicitudes-registro') ?>"
               class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 px-5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" id="submitRequestButton"
                    class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-6 text-sm font-semibold text-white hover:bg-secondary transition">
                <i class="fas fa-paper-plane mr-2"></i>Enviar a revision
            </button>
        </div>
    </form>
</div>

<template id="branchTemplate">
    <?php
    $index = '__BRANCH_INDEX__';
    $branch = [
        'nombre' => '', 'direccion' => '', 'ciudad' => '', 'estado' => 'Queretaro',
        'codigo_postal' => '', 'telefono' => '', 'email' => '',
        'horario_apertura' => '08:00', 'horario_cierre' => '20:00'
    ];
    include __DIR__ . '/branch-fields.php';
    ?>
</template>

<template id="professionalTemplate">
    <?php
    $index = '__PROFESSIONAL_INDEX__';
    $professional = [
        'nombre' => '', 'apellidos' => '', 'email' => '', 'telefono' => '',
        'profesion' => '', 'especialidad' => '', 'descripcion' => '',
        'experiencia_anos' => 0, 'tarifa_base' => 0, 'sucursales' => []
    ];
    include __DIR__ . '/professional-fields.php';
    ?>
</template>

<script>
let branchIndex = <?= empty($branches) ? 0 : (max(array_map('intval', array_keys($branches))) + 1) ?>;
let professionalIndex = <?= count($professionals) ?>;

function addBranch() {
    if (document.querySelectorAll('[data-branch-card]').length >= 10) return;
    const template = document.getElementById('branchTemplate').innerHTML;
    document.getElementById('branchesContainer').insertAdjacentHTML(
        'beforeend',
        template.replaceAll('__BRANCH_INDEX__', branchIndex)
    );
    branchIndex += 1;
    updateBranchCards();
    syncProfessionalBranchOptions();
}

function removeBranch(button) {
    const cards = document.querySelectorAll('[data-branch-card]');
    if (cards.length <= 1) return;
    button.closest('[data-branch-card]').remove();
    updateBranchCards();
    syncProfessionalBranchOptions();
}

function addProfessional() {
    if (document.querySelectorAll('[data-professional-card]').length >= 10) return;
    const template = document.getElementById('professionalTemplate').innerHTML;
    document.getElementById('professionalsContainer').insertAdjacentHTML(
        'beforeend',
        template.replaceAll('__PROFESSIONAL_INDEX__', professionalIndex)
    );
    professionalIndex += 1;
    updateProfessionalCards();
    syncProfessionalBranchOptions();
}

function removeProfessional(button) {
    const cards = document.querySelectorAll('[data-professional-card]');
    if (cards.length <= 1) return;
    button.closest('[data-professional-card]').remove();
    updateProfessionalCards();
}

function updateBranchCards() {
    const cards = document.querySelectorAll('[data-branch-card]');
    cards.forEach(function(card, index) {
        card.querySelector('[data-branch-number]').textContent = index + 1;
        const removeButton = card.querySelector('[data-remove-branch]');
        removeButton.disabled = cards.length === 1;
        removeButton.classList.toggle('invisible', cards.length === 1);
    });
    document.getElementById('addBranchButton').disabled = cards.length >= 10;
}

function updateProfessionalCards() {
    const cards = document.querySelectorAll('[data-professional-card]');
    cards.forEach(function(card, index) {
        card.querySelector('[data-professional-number]').textContent = index + 1;
        const removeButton = card.querySelector('[data-remove-professional]');
        removeButton.disabled = cards.length === 1;
        removeButton.classList.toggle('invisible', cards.length === 1);
    });
    document.getElementById('addProfessionalButton').disabled = cards.length >= 10;
}

function getBranchOptions() {
    return Array.from(document.querySelectorAll('[data-branch-card]')).map(function(card, position) {
        const name = card.querySelector('[data-branch-name]').value.trim();
        return {
            value: card.dataset.branchIndex,
            label: name || 'Sucursal ' + (position + 1)
        };
    });
}

function syncProfessionalBranchOptions() {
    const branches = getBranchOptions();
    document.querySelectorAll('[data-professional-card]').forEach(function(card) {
        const container = card.querySelector('[data-professional-branches]');
        const checked = new Set(
            Array.from(container.querySelectorAll('input:checked')).map(function(input) { return input.value; })
        );

        if (checked.size === 0 && container.dataset.selectedBranches) {
            try {
                JSON.parse(container.dataset.selectedBranches).forEach(function(value) {
                    checked.add(String(value));
                });
            } catch (error) {
                checked.clear();
            }
        }
        if (checked.size === 0 && branches.length === 1) checked.add(branches[0].value);

        container.innerHTML = '';
        branches.forEach(function(branch) {
            const label = document.createElement('label');
            label.className = 'flex min-h-11 items-center gap-3 border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700';

            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'profesionistas[' + card.dataset.professionalIndex + '][sucursales][]';
            input.value = branch.value;
            input.checked = checked.has(branch.value);
            input.className = 'h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary';

            const text = document.createElement('span');
            text.textContent = branch.label;
            label.append(input, text);
            container.appendChild(label);
        });
        container.dataset.selectedBranches = '';
    });
}

document.getElementById('branchesContainer').addEventListener('input', function(event) {
    if (event.target.matches('[data-branch-name]')) syncProfessionalBranchOptions();
});

document.getElementById('registrationRequestForm').addEventListener('submit', function(event) {
    const missingAssignment = Array.from(document.querySelectorAll('[data-professional-branches]')).some(function(container) {
        return !container.querySelector('input:checked');
    });
    if (missingAssignment) {
        event.preventDefault();
        alert('Selecciona al menos una sucursal para cada profesionista.');
        return;
    }

    const button = document.getElementById('submitRequestButton');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando solicitud...';
});

updateBranchCards();
updateProfessionalCards();
syncProfessionalBranchOptions();
</script>
