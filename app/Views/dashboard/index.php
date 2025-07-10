<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Tableau de Bord</h1>

<div class="bg-white p-6 rounded-lg shadow-lg">
    <p class="text-app-gray-700 text-lg">Bienvenue sur votre application de gestion et de facturation, <span class="font-semibold text-app-yellow-700"><?php echo htmlspecialchars($username); ?></span> !</p>
    <p class="text-app-gray-700">Votre rôle actuel est : <span class="font-semibold text-app-yellow-700"><?php echo htmlspecialchars($role); ?></span>.</p>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Factures</h3>
            <p class="text-app-gray-800 mb-4">Créez, visualisez et imprimez toutes vos factures (prestations, matériel, transport, pont bascule).</p>
            <a href="<?php echo BASE_URL; ?>/factures" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Accéder aux factures</a>
        </div>

        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Prestations</h3>
            <p class="text-app-gray-800 mb-4">Enregistrez les déclarations et les lignes de prestations, et liez-les à des fournisseurs.</p>
            <a href="<?php echo BASE_URL; ?>/prestations" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer les prestations</a>
        </div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Utilisateurs</h3>
            <p class="text-app-gray-800 mb-4">Ajoutez ou modifiez les comptes utilisateurs et leurs rôles.</p>
            <a href="<?php echo BASE_URL; ?>/users" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer les utilisateurs</a>
        </div>
        <?php endif; ?>

        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion du Matériel</h3>
            <p class="text-app-gray-800 mb-4">Enregistrez, modifiez et suivez votre inventaire de matériel.</p>
            <a href="<?php echo BASE_URL; ?>/materiels" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer le matériel</a>
        </div>

        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Fournisseurs</h3>
            <p class="text-app-gray-800 mb-4">Ajoutez ou modifiez les informations de vos fournisseurs.</p>
            <a href="<?php echo BASE_URL; ?>/fournisseurs" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer les fournisseurs</a>
        </div>

        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Clients</h3>
            <p class="text-app-gray-800 mb-4">Ajoutez ou modifiez les informations de vos clients.</p>
            <a href="<?php echo BASE_URL; ?>/clients" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer les clients</a>
        </div>

        <div class="bg-app-yellow-100 p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold mb-3 text-app-yellow-800">Gestion des Transports</h3>
            <p class="text-app-gray-800 mb-4">Enregistrez les détails des transports et leurs éléments facturables.</p>
            <a href="<?php echo BASE_URL; ?>/transports" class="inline-block bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded">Gérer les transports</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>