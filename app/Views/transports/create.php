<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Ajouter un nouveau transport</h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/transports/create" method="POST">
        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Informations Principales du Transport</h2>
        <div class="mb-4">
            <label for="attestation" class="block text-app-gray-700 text-sm font-bold mb-2">Numéro d'attestation:</label>
            <input type="text" id="attestation" name="attestation" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block text-app-gray-700 text-sm font-bold mb-2">Statut:</label>
            <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="Brouillon">Brouillon</option>
                <option value="En attente">En attente</option>
                <option value="Validé">Validé</option>
                <option value="Annulé">Annulé</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="date" class="block text-app-gray-700 text-sm font-bold mb-2">Date du transport:</label>
            <input type="date" id="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-6">
            <label for="client_id" class="block text-app-gray-700 text-sm font-bold mb-2">Client:</label>
            <select id="client_id" name="client_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Sélectionnez un client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo htmlspecialchars($client['id']); ?>">
                        <?php echo htmlspecialchars($client['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Lignes de Transport</h2>
        <div id="transport-lines-container">
            <div class="transport-line-item border border-app-gray-300 p-4 mb-4 rounded-md">
                <h3 class="font-bold text-app-gray-800 mb-2">Ligne #1</h3>
                <div class="mb-2">
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                    <input type="text" name="lines[0][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
            </div>
        </div>
        <button type="button" onclick="addLine()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
            Ajouter une ligne de transport
        </button>

        <h2 class="text-xl font-semibold mt-8 mb-4 text-app-gray-700">Déclarations</h2>
        <div id="declarations-container">
            <div class="declaration-item border border-app-gray-300 p-4 mb-4 rounded-md">
                <h3 class="font-bold text-app-gray-800 mb-2">Déclaration #1</h3>
                <div class="mb-2">
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                    <input type="text" name="declarations[0][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <button type="button" onclick="removeDeclaration(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette déclaration</button>
            </div>
        </div>
        <button type="button" onclick="addDeclaration()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
            Ajouter une déclaration
        </button>

        <div class="flex items-center justify-between mt-8">
            <button type="submit" class="bg-app-blue-600 hover:bg-app-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Enregistrer le transport
            </button>
            <a href="<?php echo BASE_URL; ?>/transports" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    let lineIndex = 1; // Start from 1 as 0 is hardcoded
    let declarationIndex = 1; // Start from 1 as 0 is hardcoded

    function addLine() {
        const container = document.getElementById('transport-lines-container');
        const newItem = document.createElement('div');
        newItem.classList.add('transport-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');
        newItem.innerHTML = `
            <h3 class="font-bold text-app-gray-800 mb-2">Ligne #${lineIndex + 1}</h3>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                <input type="text" name="lines[${lineIndex}][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
        `;
        container.appendChild(newItem);
        lineIndex++;
    }

    function removeLine(button) {
        button.closest('.transport-line-item').remove();
        updateLineNumbers();
    }

    function updateLineNumbers() {
        const items = document.querySelectorAll('.transport-line-item');
        items.forEach((item, index) => {
            item.querySelector('h3').textContent = `Ligne #${index + 1}`;
            item.querySelectorAll('input, textarea, select').forEach(input => {
                const name = input.name;
                if (name) {
                    input.name = name.replace(/lines\[\d+\]/, `lines[${index}]`);
                }
            });
        });
        lineIndex = items.length;
    }

    function addDeclaration() {
        const container = document.getElementById('declarations-container');
        const newItem = document.createElement('div');
        newItem.classList.add('declaration-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');
        newItem.innerHTML = `
            <h3 class="font-bold text-app-gray-800 mb-2">Déclaration #${declarationIndex + 1}</h3>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                <input type="text" name="declarations[${declarationIndex}][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <button type="button" onclick="removeDeclaration(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette déclaration</button>
        `;
        container.appendChild(newItem);
        declarationIndex++;
    }

    function removeDeclaration(button) {
        button.closest('.declaration-item').remove();
        updateDeclarationNumbers();
    }

    function updateDeclarationNumbers() {
        const items = document.querySelectorAll('.declaration-item');
        items.forEach((item, index) => {
            item.querySelector('h3').textContent = `Déclaration #${index + 1}`;
            item.querySelectorAll('input, textarea, select').forEach(input => {
                const name = input.name;
                if (name) {
                    input.name = name.replace(/declarations\[\d+\]/, `declarations[${index}]`);
                }
            });
        });
        declarationIndex = items.length;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>