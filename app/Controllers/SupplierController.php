<?php
// app/Controllers/SupplierController.php

require_once __DIR__ . '/../Models/Supplier.php';

class SupplierController {
    private $supplierModel;

    public function __construct() {
        $this->supplierModel = new Supplier();
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $suppliers = $this->supplierModel->getAllSuppliers();
        require_once __DIR__ . '/../Views/suppliers/index.php';
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $email = $_POST['email'] ?? '';

            // Simple validation
            if (empty($name) || empty($phone) || empty($address) || empty($email)) {
                $error = "Veuillez remplir tous les champs obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse email n'est pas valide.";
            } elseif ($this->supplierModel->create($name, $phone, $address, $email)) {
                $_SESSION['success_message'] = "Fournisseur ajouté avec succès !";
                header('Location: ' . BASE_URL . '/fournisseurs');
                exit();
            } else {
                $error = "Erreur lors de l'ajout du fournisseur.";
            }
        }
        require_once __DIR__ . '/../Views/suppliers/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $supplier = $this->supplierModel->getSupplierById($id);

        if (!$supplier) {
            $_SESSION['error_message'] = "Fournisseur non trouvé.";
            header('Location: ' . BASE_URL . '/fournisseurs');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? $supplier['name'];
            $phone = $_POST['phone'] ?? $supplier['phone'];
            $address = $_POST['address'] ?? $supplier['address'];
            $email = $_POST['email'] ?? $supplier['email'];

            if (empty($name) || empty($phone) || empty($address) || empty($email)) {
                $error = "Veuillez remplir tous les champs obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse email n'est pas valide.";
            } elseif ($this->supplierModel->update($id, $name, $phone, $address, $email)) {
                $_SESSION['success_message'] = "Fournisseur mis à jour avec succès !";
                header('Location: ' . BASE_URL . '/fournisseurs');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du fournisseur.";
            }
        }
        require_once __DIR__ . '/../Views/suppliers/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        if ($this->supplierModel->delete($id)) {
            $_SESSION['success_message'] = "Fournisseur supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du fournisseur.";
        }
        header('Location: ' . BASE_URL . '/fournisseurs');
        exit();
    }
}
?>