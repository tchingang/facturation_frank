<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-6 text-app-gray-800">Gestion des Prestations</h1>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<div class="mb-4 flex justify-end">
    <a href="<?php echo BASE_URL; ?>/prestations/create" class="bg-app-yellow-600 hover:bg-app-yellow-700 text-white font-bold py-2 px-4 rounded-md flex items-center">
        <i class="fas fa-plus-circle mr-2"></i> Ajouter une prestation
    </a>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-app-gray-200 bg-app-gray-100 text-left text-xs font-semibold text-app-gray-600 uppercase tracking-wider">
                    ID
                </th>
                <th class="px-5 py-3 border-b-2 border-app-gray-200 bg-app-gray-100 text-left text-xs font-semibold text-app-gray-600 uppercase tracking-wider">
                    Attestation
                </th>
                <th class="px-5 py-3 border-b-2 border-app-gray-200 bg-app-gray-100 text-left text-xs font-semibold text-app-gray-600 uppercase tracking-wider">
                    Statut
                </th>
                <th class="px-5 py-3 border-b-2 border-app-gray-200 bg-app-gray-100 text-left text-xs font-semibold text-app-gray-600 uppercase tracking-wider">
                    Date Prestation
                </th>
                <th class="px-5 py-3 border-b-2 border-app-gray-200 bg-app-gray-100 text-left text-xs font-semibold text-app-gray-600 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prestations)): ?>
                <?php foreach ($prestations as $prestation): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($prestation['id']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($prestation['attestation']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($prestation['status']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($prestation['date']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm">
                            <a href="<?php echo BASE_URL; ?>/prestations/edit/<?php echo $prestation['id']; ?>" class="text-app-yellow-600 hover:text-app-yellow-800 mr-3">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="<?php echo BASE_URL; ?>/prestations/delete/<?php echo $prestation['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette prestation et toutes ses données associées ?');">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-5 py-5 border-b border-app-gray-200 bg-white text-sm text-center">
                        Aucune prestation trouvée.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>