<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Modifier le fournisseur: <?php echo htmlspecialchars($supplier['name']); ?></h1>

<div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
    <?php if (isset($error)): ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/fournisseurs/edit/<?php echo $supplier['id']; ?>" method="POST">
        <div class="mb-4">
            <label for="name" class="block text-app-gray-700 text-sm font-bold mb-2">Nom du fournisseur:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($supplier['name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-app-gray-700 text-sm font-bold mb-2">Téléphone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($supplier['phone']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="address" class="block text-app-gray-700 text-sm font-bold mb-2">Adresse:</label>
            <textarea id="address" name="address" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?php echo htmlspecialchars($supplier['address']); ?></textarea>
        </div>
        <div class="mb-6">
            <label for="email" class="block text-app-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-app-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour
            </button>
            <a href="<?php echo BASE_URL; ?>/fournisseurs" class="inline-block align-baseline font-bold text-sm text-app-gray-600 hover:text-app-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>