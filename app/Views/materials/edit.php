<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Modifier le matériel: <?php echo htmlspecialchars($material['name']); ?></h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/materiels/edit/<?php echo $material['id']; ?>" method="POST">
        <div class="mb-4">
            <label for="name" class="block text-app-gray-700 text-sm font-bold mb-2">Nom du matériel:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($material['name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-app-gray-700 text-sm font-bold mb-2">Description:</label>
            <textarea id="description" name="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($material['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="quantity" class="block text-app-gray-700 text-sm font-bold mb-2">Quantité:</label>
            <input type="number" id="quantity" name="quantity" min="0" value="<?php echo htmlspecialchars($material['quantity']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-6">
            <label for="unit_price_ht" class="block text-app-gray-700 text-sm font-bold mb-2">Prix Unitaire HT:</label>
            <input type="number" id="unit_price_ht" name="unit_price_ht" step="0.01" min="0" value="<?php echo htmlspecialchars($material['unit_price_ht']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour
            </button>
            <a href="<?php echo BASE_URL; ?>/materiels" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>