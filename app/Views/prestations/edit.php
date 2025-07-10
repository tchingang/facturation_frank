<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Modifier la prestation: <?php echo htmlspecialchars($prestation['attestation']); ?></h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/prestations/edit/<?php echo $prestation['id']; ?>" method="POST">
        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Informations Principales</h2>
        <div class="mb-4">
            <label for="attestation" class="block text-app-gray-700 text-sm font-bold mb-2">Numéro d'attestation:</label>
            <input type="text" id="attestation" name="attestation" value="<?php echo htmlspecialchars($prestation['attestation']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block text-app-gray-700 text-sm font-bold mb-2">Statut:</label>
            <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="Brouillon" <?php echo ($prestation['status'] == 'Brouillon') ? 'selected' : ''; ?>>Brouillon</option>
                <option value="En attente" <?php echo ($prestation['status'] == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                <option value="Validée" <?php echo ($prestation['status'] == 'Validée') ? 'selected' : ''; ?>>Validée</option>
                <option value="Annulée" <?php echo ($prestation['status'] == 'Annulée') ? 'selected' : ''; ?>>Annulée</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="date" class="block text-app-gray-700 text-sm font-bold mb-2">Date de la prestation:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($prestation['date']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-6">
            <label for="client_id" class="block text-app-gray-700 text-sm font-bold mb-2">Client:</label>
            <select id="client_id" name="client_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Sélectionner un client</option>
                <?php
                // Assuming $clients is passed from the controller
                if (isset($clients) && is_array($clients)) {
                    foreach ($clients as $client) {
                        $selected = ($client['id'] == $prestation['client_id']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($client['id']) . '" ' . $selected . '>' . htmlspecialchars($client['name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Lignes de Prestation</h2>
        <div id="prestation-lines-container">
            <?php if (!empty($prestation_lines)): ?>
                <?php foreach ($prestation_lines as $idx => $line): ?>
                    <div class="prestation-line-item border border-app-gray-300 p-4 mb-4 rounded-md">
                        <h3 class="font-bold text-app-gray-800 mb-2">Ligne #<?php echo $idx + 1; ?></h3>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                            <input type="text" name="lines[<?php echo $idx; ?>][designation]" value="<?php echo htmlspecialchars($line['designation']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Base de calcul:</label>
                            <input type="text" name="lines[<?php echo $idx; ?>][base_calcul]" value="<?php echo htmlspecialchars($line['base_calcul']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                            <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][prix_unitaire]" value="<?php echo htmlspecialchars($line['prix_unitaire']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant HT:</label>
                            <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][montant_ht]" value="<?php echo htmlspecialchars($line['montant_ht']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">TVA (%):</label>
                            <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][tva]" value="<?php echo htmlspecialchars($line['tva']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant TTC:</label>
                            <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][montant_ttc]" value="<?php echo htmlspecialchars($line['montant_ttc']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addLine()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
            Ajouter une ligne de prestation
        </button>

        <h2 class="text-xl font-semibold mt-8 mb-4 text-app-gray-700">Déclarations</h2>
        <div id="declarations-container">
            <?php if (!empty($declarations)): ?>
                <?php foreach ($declarations as $idx => $declaration): ?>
                    <div class="declaration-item border border-app-gray-300 p-4 mb-4 rounded-md">
                        <h3 class="font-bold text-app-gray-800 mb-2">Déclaration #<?php echo $idx + 1; ?></h3>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Nature:</label>
                            <input type="text" name="declarations[<?php echo $idx; ?>][nature]" value="<?php echo htmlspecialchars($declaration['nature']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                            <input type="text" name="declarations[<?php echo $idx; ?>][designation]" value="<?php echo htmlspecialchars($declaration['designation']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Régime:</label>
                            <input type="text" name="declarations[<?php echo $idx; ?>][regime]" value="<?php echo htmlspecialchars($declaration['regime']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Poids:</label>
                            <input type="number" step="0.01" name="declarations[<?php echo $idx; ?>][poids]" value="<?php echo htmlspecialchars($declaration['poids']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Valeur:</label>
                            <input type="number" step="0.01" name="declarations[<?php echo $idx; ?>][valeur]" value="<?php echo htmlspecialchars($declaration['valeur']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <button type="button" onclick="removeDeclaration(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette déclaration</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addDeclaration()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
            Ajouter une déclaration
        </button>

        <div class="flex items-center justify-between mt-8">
            <button type="submit" class="bg-app-blue-600 hover:bg-app-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour la prestation
            </button>
            <a href="<?php echo BASE_URL; ?>/prestations" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    let lineIndex = <?php echo !empty($prestation_lines) ? count($prestation_lines) : 0; ?>;
    let declarationIndex = <?php echo !empty($declarations) ? count($declarations) : 0; ?>;

    function addLine() {
        const container = document.getElementById('prestation-lines-container');
        const newItem = document.createElement('div');
        newItem.classList.add('prestation-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');
        newItem.innerHTML = `
            <h3 class="font-bold text-app-gray-800 mb-2">Ligne #${lineIndex + 1}</h3>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                <input type="text" name="lines[${lineIndex}][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Base de calcul:</label>
                <input type="text" name="lines[${lineIndex}][base_calcul]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                <input type="number" step="0.01" name="lines[${lineIndex}][prix_unitaire]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant HT:</label>
                <input type="number" step="0.01" name="lines[${lineIndex}][montant_ht]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">TVA (%):</label>
                <input type="number" step="0.01" name="lines[${lineIndex}][tva]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant TTC:</label>
                <input type="number" step="0.01" name="lines[${lineIndex}][montant_ttc]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
        `;
        container.appendChild(newItem);
        lineIndex++;
    }

    function removeLine(button) {
        button.closest('.prestation-line-item').remove();
        updateLineNumbers();
    }

    function updateLineNumbers() {
        const items = document.querySelectorAll('.prestation-line-item');
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
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Nature:</label>
                <input type="text" name="declarations[${declarationIndex}][nature]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                <input type="text" name="declarations[${declarationIndex}][designation]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Régime:</label>
                <input type="text" name="declarations[${declarationIndex}][regime]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Poids:</label>
                <input type="number" step="0.01" name="declarations[${declarationIndex}][poids]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-2">
                <label class="block text-app-gray-700 text-sm font-bold mb-1">Valeur:</label>
                <input type="number" step="0.01" name="declarations[${declarationIndex}][valeur]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
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