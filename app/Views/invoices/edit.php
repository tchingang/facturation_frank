<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Modifier la facture: <?php echo htmlspecialchars($invoice['invoice_number']); ?></h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/invoices/edit/<?php echo $invoice['id']; ?>" method="POST">
        <h2 class="text-xl font-semibold mb-4 text-app-gray-700">Informations Principales de la Facture</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="invoice_number" class="block text-app-gray-700 text-sm font-bold mb-2">Numéro de facture:</label>
                <input type="text" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="invoice_date" class="block text-app-gray-700 text-sm font-bold mb-2">Date de la facture:</label>
                <input type="date" id="invoice_date" name="invoice_date" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="due_date" class="block text-app-gray-700 text-sm font-bold mb-2">Date d'échéance:</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($invoice['due_date']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="client_id" class="block text-app-gray-700 text-sm font-bold mb-2">Client:</label>
                <select id="client_id" name="client_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Sélectionnez un client</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo htmlspecialchars($client['id']); ?>" <?php echo ($invoice['client_id'] == $client['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($client['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-app-gray-700 text-sm font-bold mb-2">Statut:</label>
                <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="Brouillon" <?php echo ($invoice['status'] == 'Brouillon') ? 'selected' : ''; ?>>Brouillon</option>
                    <option value="Envoyée" <?php echo ($invoice['status'] == 'Envoyée') ? 'selected' : ''; ?>>Envoyée</option>
                    <option value="Payée" <?php echo ($invoice['status'] == 'Payée') ? 'selected' : ''; ?>>Payée</option>
                    <option value="Annulée" <?php echo ($invoice['status'] == 'Annulée') ? 'selected' : ''; ?>>Annulée</option>
                </select>
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-8 mb-4 text-app-gray-700">Ajouter des Lignes de Facture</h2>
        <div class="mb-4">
            <label for="line_type_select" class="block text-app-gray-700 text-sm font-bold mb-2">Type de ligne à ajouter:</label>
            <select id="line_type_select" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="manual">Ligne Manuelle</option>
                <option value="prestation">Prestation Existante</option>
                <option value="transport">Transport Existant</option>
                <option value="weighbridge">Pont Bascule Existant</option>
            </select>
            <button type="button" onclick="showAddSection()" class="bg-app-blue-600 hover:bg-app-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-2">
                Afficher les options d'ajout
            </button>
        </div>

        <div id="manual_line_section" class="line-add-section border p-4 mb-4 rounded-md hidden">
            <h3 class="font-bold text-app-gray-800 mb-2">Ajouter une Ligne Manuelle</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                    <input type="text" id="manual_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                    <input type="number" step="0.01" id="manual_quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0">
                </div>
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                    <input type="number" step="0.01" id="manual_unit_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0">
                </div>
            </div>
            <button type="button" onclick="addManualLine()" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
                Ajouter cette ligne manuelle
            </button>
        </div>

        <div id="prestation_section" class="line-add-section border p-4 mb-4 rounded-md hidden">
            <h3 class="font-bold text-app-gray-800 mb-2">Ajouter des Prestations</h3>
            <label for="select_prestations" class="block text-app-gray-700 text-sm font-bold mb-2">Sélectionnez des prestations:</label>
            <select id="select_prestations" multiple class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline h-40">
                </select>
            <button type="button" onclick="addSelectedPrestations()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-2">
                Ajouter les prestations sélectionnées
            </button>
        </div>

        <div id="transport_section" class="line-add-section border p-4 mb-4 rounded-md hidden">
            <h3 class="font-bold text-app-gray-800 mb-2">Ajouter des Transports</h3>
            <label for="select_transports" class="block text-app-gray-700 text-sm font-bold mb-2">Sélectionnez des transports:</label>
            <select id="select_transports" multiple class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline h-40">
                </select>
            <button type="button" onclick="addSelectedTransports()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-2">
                Ajouter les transports sélectionnés
            </button>
        </div>

        <div id="weighbridge_section" class="line-add-section border p-4 mb-4 rounded-md hidden">
            <h3 class="font-bold text-app-gray-800 mb-2">Ajouter des Passages Pont Bascule</h3>
            <label for="select_weighbridges" class="block text-app-gray-700 text-sm font-bold mb-2">Sélectionnez des passages pont bascule:</label>
            <select id="select_weighbridges" multiple class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline h-40">
                </select>
            <button type="button" onclick="addSelectedWeighbridges()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-2">
                Ajouter les passages pont bascule sélectionnés
            </button>
        </div>


        <h2 class="text-xl font-semibold mt-8 mb-4 text-app-gray-700">Lignes de Facture Actuelles</h2>
        <div id="invoice-lines-container">
            <?php if (!empty($invoice_lines)): ?>
                <?php foreach ($invoice_lines as $idx => $line): ?>
                    <div class="invoice-line-item border border-app-gray-300 p-4 mb-4 rounded-md">
                        <h3 class="font-bold text-app-gray-800 mb-2">Ligne #<?php echo $idx + 1; ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                                <input type="text" name="lines[<?php echo $idx; ?>][description]" value="<?php echo htmlspecialchars($line['description']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            </div>
                            <div>
                                <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                                <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][quantity]" value="<?php echo htmlspecialchars($line['quantity']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0.01">
                            </div>
                            <div>
                                <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                                <input type="number" step="0.01" name="lines[<?php echo $idx; ?>][unit_price]" value="<?php echo htmlspecialchars($line['unit_price']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                            </div>
                        </div>
                        <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="flex items-center justify-between mt-8">
            <button type="submit" class="bg-app-blue-600 hover:bg-app-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour la facture
            </button>
            <a href="<?php echo BASE_URL; ?>/invoices" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    let lineCounter = <?php echo !empty($invoice_lines) ? count($invoice_lines) : 0; ?>; // Keep track of total lines

    // Data from PHP for dynamic selects
    const allPrestations = <?php echo json_encode($prestations); ?>;
    const allTransports = <?php echo json_encode($transports); ?>;
    const allWeighbridges = <?php echo json_encode($weighbridges); ?>;
    const currentInvoiceLines = <?php echo json_encode($invoice_lines); ?>; // Current lines on this invoice

    const selectPrestationsElement = document.getElementById('select_prestations');
    const selectTransportsElement = document.getElementById('select_transports');
    const selectWeighbridgesElement = document.getElementById('select_weighbridges');
    const clientIdSelect = document.getElementById('client_id');
    const invoiceLinesContainer = document.getElementById('invoice-lines-container');
    const lineTypeSelect = document.getElementById('line_type_select');


    // Hide all add sections initially
    document.querySelectorAll('.line-add-section').forEach(section => section.classList.add('hidden'));

    // Show selected add section
    function showAddSection() {
        document.querySelectorAll('.line-add-section').forEach(section => section.classList.add('hidden'));
        const selectedType = lineTypeSelect.value;
        document.getElementById(`${selectedType}_section`).classList.remove('hidden');
    }

    // Filter and populate select boxes based on client and existing lines
    function filterAndPopulateItems() {
        const selectedClientId = clientIdSelect.value;

        // Clear existing options
        selectPrestationsElement.innerHTML = '';
        selectTransportsElement.innerHTML = '';
        selectWeighbridgesElement.innerHTML = '';

        if (!selectedClientId) {
            selectPrestationsElement.innerHTML = '<option disabled selected>Sélectionnez un client d\'abord</option>';
            selectTransportsElement.innerHTML = '<option disabled selected>Sélectionnez un client d\'abord</option>';
            selectWeighbridgesElement.innerHTML = '<option disabled selected>Sélectionnez un client d\'abord</option>';
            return;
        }

        // Get IDs of items already present in the current invoice lines (from DB)
        const existingPrestationDescriptions = currentInvoiceLines.filter(line => line.description.startsWith('Prestation:')).map(line => line.description);
        const existingTransportDescriptions = currentInvoiceLines.filter(line => line.description.startsWith('Transport:')).map(line => line.description);
        const existingWeighbridgeDescriptions = currentInvoiceLines.filter(line => line.description.startsWith('Pont Bascule:')).map(line => line.description);

        // Filter for prestations
        const clientPrestations = allPrestations.filter(p => p.client_id == selectedClientId &&
            !existingPrestationDescriptions.includes(`Prestation: ${p.attestation_number} (Date: ${p.prestation_date})`)); // Match based on generated description
        if (clientPrestations.length > 0) {
            clientPrestations.forEach(p => {
                const option = document.createElement('option');
                option.value = p.id;
                option.textContent = `Prestation ${p.attestation_number} (Date: ${p.prestation_date})`;
                selectPrestationsElement.appendChild(option);
            });
        } else {
            selectPrestationsElement.innerHTML = '<option disabled selected>Aucune prestation disponible.</option>';
        }

        // Filter for transports
        const clientTransports = allTransports.filter(t => t.client_id == selectedClientId &&
            !existingTransportDescriptions.includes(`Transport: ${t.attestation} (Date: ${t.date})`)); // Match based on generated description
        if (clientTransports.length > 0) {
            clientTransports.forEach(t => {
                const option = document.createElement('option');
                option.value = t.id;
                option.textContent = `Transport ${t.attestation} (Date: ${t.date})`;
                selectTransportsElement.appendChild(option);
            });
        } else {
            selectTransportsElement.innerHTML = '<option disabled selected>Aucun transport disponible.</option>';
        }

        // Filter for weighbridges
        const clientWeighbridges = allWeighbridges.filter(w => w.client_id == selectedClientId &&
            !existingWeighbridgeDescriptions.includes(`Pont Bascule: ${w.weigh_number} (Date: ${w.weigh_date}, Véhicule: ${w.vehicle_number})`)); // Match based on generated description
        if (clientWeighbridges.length > 0) {
            clientWeighbridges.forEach(w => {
                const option = document.createElement('option');
                option.value = w.id;
                option.textContent = `Pesée ${w.weigh_number} (Date: ${w.weigh_date}, Véhicule: ${w.vehicle_number})`;
                selectWeighbridgesElement.appendChild(option);
            });
        } else {
            selectWeighbridgesElement.innerHTML = '<option disabled selected>Aucun pont bascule disponible.</option>';
        }
    }

    clientIdSelect.addEventListener('change', filterAndPopulateItems);
    document.addEventListener('DOMContentLoaded', filterAndPopulateItems);


    // Add Manual Line
    function addManualLine() {
        const description = document.getElementById('manual_description').value;
        const quantity = parseFloat(document.getElementById('manual_quantity').value);
        const unit_price = parseFloat(document.getElementById('manual_unit_price').value);

        if (!description || isNaN(quantity) || quantity <= 0 || isNaN(unit_price) || unit_price < 0) {
            alert("Veuillez remplir correctement les champs de la ligne manuelle.");
            return;
        }

        const newItem = document.createElement('div');
        newItem.classList.add('invoice-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');
        newItem.dataset.lineType = 'manual'; // Custom attribute
        newItem.dataset.lineId = lineCounter; // Unique ID for this specific line

        newItem.innerHTML = `
            <h3 class="font-bold text-app-gray-800 mb-2">Ligne Manuelle #${lineCounter + 1}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                    <input type="text" name="lines[${lineCounter}][description]" value="${description}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                </div>
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                    <input type="number" step="0.01" name="lines[${lineCounter}][quantity]" value="${quantity}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                </div>
                <div>
                    <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                    <input type="number" step="0.01" name="lines[${lineCounter}][unit_price]" value="${unit_price}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                </div>
            </div>
            <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
        `;
        invoiceLinesContainer.appendChild(newItem);
        lineCounter++;

        // Clear manual fields
        document.getElementById('manual_description').value = '';
        document.getElementById('manual_quantity').value = '';
        document.getElementById('manual_unit_price').value = '';
    }

    // Add Selected Prestations
    function addSelectedPrestations() {
        const selectedPrestationIds = Array.from(selectPrestationsElement.selectedOptions).map(option => option.value);

        selectedPrestationIds.forEach(prestationId => {
            const prestation = allPrestations.find(p => p.id == prestationId);
            if (prestation) {
                const newItem = document.createElement('div');
                newItem.classList.add('invoice-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md', 'bg-blue-50');
                newItem.dataset.lineType = 'prestation';
                newItem.dataset.sourceId = prestation.id; // Store source ID for re-adding to select

                newItem.innerHTML = `
                    <h3 class="font-bold text-app-gray-800 mb-2">Prestation: ${prestation.attestation_number}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                            <input type="hidden" name="selected_prestations[]" value="${prestation.id}">
                            <input type="text" value="Prestation: ${prestation.attestation_number} (Date: ${prestation.prestation_date})" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                            <input type="number" step="0.01" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire (Total):</label>
                            <input type="number" step="0.01" value="${prestation.total_amount || 0}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                    </div>
                    <button type="button" onclick="removeDynamicLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette prestation</button>
                `;
                invoiceLinesContainer.appendChild(newItem);
            }
        });
        filterAndPopulateItems(); // Re-filter to remove added items from selects
    }

    // Add Selected Transports
    function addSelectedTransports() {
        const selectedTransportIds = Array.from(selectTransportsElement.selectedOptions).map(option => option.value);

        selectedTransportIds.forEach(transportId => {
            const transport = allTransports.find(t => t.id == transportId);
            if (transport) {
                const newItem = document.createElement('div');
                newItem.classList.add('invoice-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md', 'bg-green-50');
                newItem.dataset.lineType = 'transport';
                newItem.dataset.sourceId = transport.id;

                newItem.innerHTML = `
                    <h3 class="font-bold text-app-gray-800 mb-2">Transport: ${transport.attestation}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                            <input type="hidden" name="selected_transports[]" value="${transport.id}">
                            <input type="text" value="Transport: ${transport.attestation} (Date: ${transport.date})" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                            <input type="number" step="0.01" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire (Total):</label>
                            <input type="number" step="0.01" value="${transport.total_amount || 0}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                    </div>
                    <button type="button" onclick="removeDynamicLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer ce transport</button>
                `;
                invoiceLinesContainer.appendChild(newItem);
            }
        });
        filterAndPopulateItems();
    }

    // Add Selected Weighbridges
    function addSelectedWeighbridges() {
        const selectedWeighbridgeIds = Array.from(selectWeighbridgesElement.selectedOptions).map(option => option.value);

        selectedWeighbridgeIds.forEach(weighbridgeId => {
            const weighbridge = allWeighbridges.find(w => w.id == weighbridgeId);
            if (weighbridge) {
                const newItem = document.createElement('div');
                newItem.classList.add('invoice-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md', 'bg-yellow-50');
                newItem.dataset.lineType = 'weighbridge';
                newItem.dataset.sourceId = weighbridge.id;

                newItem.innerHTML = `
                    <h3 class="font-bold text-app-gray-800 mb-2">Pont Bascule: ${weighbridge.weigh_number}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                            <input type="hidden" name="selected_weighbridges[]" value="${weighbridge.id}">
                            <input type="text" value="Pont Bascule: ${weighbridge.weigh_number} (Date: ${weighbridge.weigh_date}, Véhicule: ${weighbridge.vehicle_number})" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                            <input type="number" step="0.01" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                        <div>
                            <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire (Total Taxes):</label>
                            <input type="number" step="0.01" value="${weighbridge.total_amount || 0}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                        </div>
                    </div>
                    <button type="button" onclick="removeDynamicLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer ce pont bascule</button>
                `;
                invoiceLinesContainer.appendChild(newItem);
            }
        });
        filterAndPopulateItems();
    }


    // Unified remove function for all types of lines
    function removeLine(button) {
        button.closest('.invoice-line-item').remove();
    }

    function removeDynamicLine(button) {
        const itemToRemove = button.closest('.invoice-line-item');
        const sourceId = itemToRemove.dataset.sourceId;
        const sourceType = itemToRemove.dataset.lineType;

        // Re-add to the correct select list
        if (sourceType === 'prestation') {
            const prestation = allPrestations.find(p => p.id == sourceId);
            if (prestation) {
                const option = document.createElement('option');
                option.value = prestation.id;
                option.textContent = `Prestation ${prestation.attestation_number} (Date: ${prestation.prestation_date})`;
                selectPrestationsElement.appendChild(option);
            }
        } else if (sourceType === 'transport') {
            const transport = allTransports.find(t => t.id == sourceId);
            if (transport) {
                const option = document.createElement('option');
                option.value = transport.id;
                option.textContent = `Transport ${transport.attestation} (Date: ${transport.date})`;
                selectTransportsElement.appendChild(option);
            }
        } else if (sourceType === 'weighbridge') {
            const weighbridge = allWeighbridges.find(w => w.id == sourceId);
            if (weighbridge) {
                const option = document.createElement('option');
                option.value = weighbridge.id;
                option.textContent = `Pesée ${weighbridge.weigh_number} (Date: ${weighbridge.weigh_date}, Véhicule: ${weighbridge.vehicle_number})`;
                selectWeighbridgesElement.appendChild(option);
            }
        }
        itemToRemove.remove();
        filterAndPopulateItems(); // Re-filter to ensure correct order
    }

    // Populate existing invoice lines on load
    document.addEventListener('DOMContentLoaded', () => {
        currentInvoiceLines.forEach(line => {
            const newItem = document.createElement('div');
            newItem.classList.add('invoice-line-item', 'border', 'border-app-gray-300', 'p-4', 'mb-4', 'rounded-md');

            let lineDescription = line.description;
            let hiddenInput = '';
            let lineType = 'manual';
            let bgColorClass = '';

            // Attempt to identify if it's a prestation, transport or weighbridge based on description format
            if (lineDescription.startsWith('Prestation:')) {
                lineType = 'prestation';
                bgColorClass = 'bg-blue-50';
                // Extract ID if needed for re-adding to select (more complex without storing ID directly in invoice_lines)
                // For editing, we directly output the lines. Re-adding to source select is done if they are removed.
            } else if (lineDescription.startsWith('Transport:')) {
                lineType = 'transport';
                bgColorClass = 'bg-green-50';
            } else if (lineDescription.startsWith('Pont Bascule:')) {
                lineType = 'weighbridge';
                bgColorClass = 'bg-yellow-50';
            }

            newItem.classList.add(bgColorClass);

            newItem.innerHTML = `
                <h3 class="font-bold text-app-gray-800 mb-2">Ligne #${lineCounter + 1} (${lineType.charAt(0).toUpperCase() + lineType.slice(1)})</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-app-gray-700 text-sm font-bold mb-1">Désignation:</label>
                        <input type="text" name="lines[${lineCounter}][description]" value="${htmlspecialchars(line.description)}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label class="block text-app-gray-700 text-sm font-bold mb-1">Quantité:</label>
                        <input type="number" step="0.01" name="lines[${lineCounter}][quantity]" value="${htmlspecialchars(line.quantity)}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0.01">
                    </div>
                    <div>
                        <label class="block text-app-gray-700 text-sm font-bold mb-1">Prix Unitaire:</label>
                        <input type="number" step="0.01" name="lines[${lineCounter}][unit_price]" value="${htmlspecialchars(line.unit_price)}" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                    </div>
                </div>
                <button type="button" onclick="removeLine(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm mt-2">Supprimer cette ligne</button>
            `;
            invoiceLinesContainer.appendChild(newItem);
            lineCounter++;
        });
        filterAndPopulateItems(); // After populating existing, filter remaining available items
    });


    // Helper for HTML escaping (from PHP's htmlspecialchars)
    function htmlspecialchars(str) {
        if (typeof str !== 'string') {
            return str;
        }
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return str.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>