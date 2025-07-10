<?php
class DashboardController {
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        // Logique pour afficher le tableau de bord
        $username = $_SESSION['username'] ?? 'Invité';
        $role = $_SESSION['role'] ?? '';

        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}
?>