<?php
// app/Controllers/MaterialController.php

require_once __DIR__ . '/../Models/Material.php';

class MaterialController {
    private $materialModel;

    public function __construct() {
        $this->materialModel = new Material();
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $materials = $this->materialModel->getAllMaterials();
        require_once __DIR__ . '/../Views/materials/index.php';
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $quantity = $_POST['quantity'] ?? 0;
            $unit_price_ht = $_POST['unit_price_ht'] ?? 0.0;

            // Simple validation
            if (empty($name) || !is_numeric($quantity) || $quantity < 0 || !is_numeric($unit_price_ht) || $unit_price_ht < 0) {
                $error = "Veuillez remplir tous les champs obligatoires avec des valeurs valides.";
            } elseif ($this->materialModel->create($name, $description, $quantity, $unit_price_ht)) {
                $_SESSION['success_message'] = "Matériel ajouté avec succès !";
                header('Location: ' . BASE_URL . '/materiels');
                exit();
            } else {
                $error = "Erreur lors de l'ajout du matériel.";
            }
        }
        require_once __DIR__ . '/../Views/materials/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $material = $this->materialModel->getMaterialById($id);

        if (!$material) {
            $_SESSION['error_message'] = "Matériel non trouvé.";
            header('Location: ' . BASE_URL . '/materiels');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? $material['name'];
            $description = $_POST['description'] ?? $material['description'];
            $quantity = $_POST['quantity'] ?? $material['quantity'];
            $unit_price_ht = $_POST['unit_price_ht'] ?? $material['unit_price_ht'];

            if (empty($name) || !is_numeric($quantity) || $quantity < 0 || !is_numeric($unit_price_ht) || $unit_price_ht < 0) {
                $error = "Veuillez remplir tous les champs obligatoires avec des valeurs valides.";
            } elseif ($this->materialModel->update($id, $name, $description, $quantity, $unit_price_ht)) {
                $_SESSION['success_message'] = "Matériel mis à jour avec succès !";
                header('Location: ' . BASE_URL . '/materiels');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du matériel.";
            }
        }
        require_once __DIR__ . '/../Views/materials/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        if ($this->materialModel->delete($id)) {
            $_SESSION['success_message'] = "Matériel supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du matériel.";
        }
        header('Location: ' . BASE_URL . '/materiels');
        exit();
    }
}
?>