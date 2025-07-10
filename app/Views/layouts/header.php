<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon App de Facturation - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Palette adoucie
                        // Jaune principal, moins vif
                        'app-yellow': {
                            DEFAULT: '#FACC15', // Un jaune plus doux, équivalent à yellow-400
                            50: '#FEFCE8',
                            100: '#FEF9C3',
                            200: '#FDE68A',
                            300: '#FCD34D',
                            400: '#FACC15', // Utilisation comme DEFAULT
                            500: '#EAB308',
                            600: '#CA8A04',
                            700: '#A16207',
                            800: '#854D09',
                            900: '#713F12',
                            950: '#422006',
                        },
                        // Gris pour le texte et les arrière-plans doux
                        'app-gray': {
                            DEFAULT: '#4B5563', // gray-600
                            50: '#F9FAFB', // gray-50
                            100: '#F3F4F6', // gray-100
                            200: '#E5E7EB', // gray-200
                            300: '#D1D5DB', // gray-300
                            400: '#9CA3AF', // gray-400
                            500: '#6B7280', // gray-500
                            600: '#4B5563', // gray-600
                            700: '#374151', // gray-700
                            800: '#1F2937', // gray-800
                            900: '#111827', // gray-900
                            950: '#030712', // gray-950
                        },
                        'app-blue': {
                            600: '#2563eb', // Example blue shade, adjust as needed
                            700: '#1d4ed8', // Darker blue for hover
                        },
                        'app-gray': {
                            300: '#d1d5db',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                        },
                        'app-yellow': {
                            600: '#d97706', // Example yellow shade
                            700: '#b45309', // Darker yellow for hover
                        },
                        // Add other custom colors like app-green, app-red if you use them
                        'red': { // Ensure default Tailwind colors are available if you mix
                            50: '#fef2f2',
                            100: '#fee2e2',
                            400: '#f87171',
                            500: '#ef4444',
                            700: '#b91c1c',
                        },
                        'blue': { // Default blue
                            50: '#eff6ff',
                            500: '#3b82f6',
                            700: '#2563eb',
                        },
                        'green': { // Default green
                            50: '#f0fdf4',
                            500: '#22c55e',
                            700: '#16a34a',
                        },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>

<body class="bg-white font-sans leading-normal tracking-normal">

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="flex h-screen bg-app-gray-50">
            <aside class="w-64 bg-app-yellow-600 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="p-6 text-app-gray-900 text-3xl font-bold border-b border-app-yellow-700 text-center">
                        <i class="fas fa-file-invoice mr-2"></i> Facturation
                    </div>
                    <nav class="mt-6">
                        <a href="<?php echo BASE_URL; ?>/" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-tachometer-alt mr-3 text-lg"></i> Tableau de Bord
                        </a>
                        <a href="<?php echo BASE_URL; ?>/invoices" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-receipt mr-3 text-lg"></i> Factures
                        </a>
                        <a href="<?php echo BASE_URL; ?>/prestations" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-cogs mr-3 text-lg"></i> Prestations
                        </a>
                        <a href="<?php echo BASE_URL; ?>/transports" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-truck mr-3 text-lg"></i> Transports
                        </a>
                        <a href="<?php echo BASE_URL; ?>/materiels" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-boxes mr-3 text-lg"></i> Matériel
                        </a>
                        <a href="<?php echo BASE_URL; ?>/weighbridges" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-balance-scale-right mr-3 text-lg"></i>
                            <span class="flex-1 ml-3 whitespace-nowrap">Ponts Bascule</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/fournisseurs" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-industry mr-3 text-lg"></i> Fournisseurs
                        </a>
                        <a href="<?php echo BASE_URL; ?>/clients" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                            <i class="fas fa-users mr-3 text-lg"></i> Clients
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>/users" class="flex items-center px-6 py-3 text-app-gray-900 hover:bg-app-yellow-700 transition-colors duration-200">
                                <i class="fas fa-user-shield mr-3 text-lg"></i> Utilisateurs
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <div class="p-6 border-t border-app-yellow-700">
                    <div class="flex items-center justify-between text-app-gray-900 text-sm">
                        <span>Connecté en tant que: <br><strong class="text-base"><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                        <a href="<?php echo BASE_URL; ?>/logout" class="bg-app-yellow-700 text-white px-3 py-1 rounded-md hover:bg-app-yellow-800 text-xs">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </aside>

            <main class="flex-1 overflow-y-auto p-8">
            <?php else: ?>
            <?php endif; ?>