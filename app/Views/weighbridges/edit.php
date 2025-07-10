<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Modifier l'enregistrement de Pont Bascule: <?php echo htmlspecialchars($weighbridge['weigh_number']); ?></h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/weighbridges/edit/<?php echo $weighbridge['id']; ?>" method="POST">
        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Informations du Passage</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="weigh_number" class="block text-app-gray-700 text-sm font-bold mb-2">Numéro de pesée:</label>
                <input type="text" id="weigh_number" name="weigh_number" value="<?php echo htmlspecialchars($weighbridge['weigh_number']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="weigh_date" class="block text-app-gray-700 text-sm font-bold mb-2">Date de la pesée:</label>
                <input type="date" id="weigh_date" name="weigh_date" value="<?php echo htmlspecialchars($weighbridge['weigh_date']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="client_id" class="block text-app-gray-700 text-sm font-bold mb-2">Client:</label>
                <select id="client_id" name="client_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Sélectionnez un client</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo htmlspecialchars($client['id']); ?>" <?php echo ($weighbridge['client_id'] == $client['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($client['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="vehicle_number" class="block text-app-gray-700 text-sm font-bold mb-2">Numéro du véhicule:</label>
                <input type="text" id="vehicle_number" name="vehicle_number" value="<?php echo htmlspecialchars($weighbridge['vehicle_number']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="driver_name" class="block text-app-gray-700 text-sm font-bold mb-2">Nom du chauffeur:</label>
                <input type="text" id="driver_name" name="driver_name" value="<?php echo htmlspecialchars($weighbridge['driver_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="first_weight" class="block text-app-gray-700 text-sm font-bold mb-2">Premier Poids (kg):</label>
                <input type="number" step="0.01" id="first_weight" name="first_weight" value="<?php echo htmlspecialchars($weighbridge['first_weight']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0.01">
            </div>
            <div>
                <label for="second_weight" class="block text-app-gray-700 text-sm font-bold mb-2">Deuxième Poids (kg):</label>
                <input type="number" step="0.01" id="second_weight" name="second_weight" value="<?php echo htmlspecialchars($weighbridge['second_weight']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0.01">
            </div>
            <div>
                <label for="net_weight_display" class="block text-app-gray-700 text-sm font-bold mb-2">Poids Net (kg):</label>
                <input type="text" id="net_weight_display" value="<?php echo number_format($weighbridge['net_weight'], 2, ',', ' '); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 bg-app-gray-100 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>
            <div class="md:col-span-2">
                <label for="notes" class="block text-app-gray-700 text-sm font-bold mb-2">Notes:</label>
                <textarea id="notes" name="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($weighbridge['notes']); ?></textarea>
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-8 mb-4 text-app-gray-700">Taxes Payées</h2>
        <div id="taxes-container">
            <?php if (!empty($weighbridge_taxes)): ?>
                <?php foreach ($weighbridge_taxes as $idx => $tax): ?>
                    <div class="tax-item border border-app-gray-300 p-4 mb-4 rounded-md">
                        <h3 class="font-bold text-app-gray-800 mb-2">Taxe #<?php echo $idx + 1; ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-app-gray-700 text-sm font-bold mb-1">Description de la taxe:</label>
                                <input type="text" name="taxes[<?php echo $idx; ?>][tax_description]" value="<?php echo htmlspecialchars($tax['tax_description']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            </div>
                            <div>
                                <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant de la taxe (XAF):</label>
                                <input type="number" step="0.01" name="taxes[<?php echo $idx; ?>][tax_amount]" value="<?php echo htmlspecialchars($tax['tax_amount']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                            </div>
                        </div>
                        <button type="button" onclick="removeTax(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette taxe</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addTax()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
            Ajouter une ligne de taxe
        </button>

        <div class="flex items-center justify-between mt-8">
            <button type="submit" class="bg-app-blue-600 hover:bg-app-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour le passage
            </button>
            <a href="<?php echo BASE_URL; ?>/weighbridges" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    let taxIndex = <?php echo !empty($weighbridge_taxes) ? count($weighbridge_taxes) : 0; ?>;

    function calculateNetWeight() {
        const firstWeight = parseFloat(document.getElementById('first_weight').value) || 0;
        const secondWeight = parseFloat(document.getElementById('second_weight').value) || 0;
        const netWeight = firstWeight - secondWeight;
        document.getElementById('net_weight_display').value = netWeight.toFixed(2);
    }

    document.getElementById('first_weight').addEventListener('input', calculateNetWeight);
    document.getElementById('second_weight').addEventListener('input', calculateNetWeight);
    document.addEventListener('DOMContentLoaded', calculateNetWeight);

    function addTax() {
        const container = document.getElementById('taxes-container');
        const newItem = document.createElement('div');
        newItem.classList.add('tax-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');
        newItem.innerHTML = `
            <h3 class="font-bold text-app-gray-800 mb-2">Taxe #${taxIndex + 1}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Description de la taxe:</label>
                    <input type="text" name="taxes[${taxIndex}][tax_description]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Montant de la taxe (XAF):</label>
                    <input type="number" step="0.01" name="taxes[${taxIndex}][tax_amount]" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>
            </div>
            <button type="button" onclick="removeTax(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette taxe</button>
        `;
        container.appendChild(newItem);
        taxIndex++;
    }

    function removeTax(button) {
        button.closest('.tax-item').remove();
        updateTaxNumbers();
    }

    function updateTaxNumbers() {
        const items = document.querySelectorAll('.tax-item');
        items.forEach((item, index) => {
            item.querySelector('h3').textContent = `Taxe #${index + 1}`;
            item.querySelectorAll('input, textarea, select').forEach(input => {
                const name = input.name;
                if (name) {
                    input.name = name.replace(/taxes\[\d+\]/, `taxes[${index}]`);
                }
            });
        });
        taxIndex = items.length;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>