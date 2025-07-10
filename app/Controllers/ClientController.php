<?php
// app/Controllers/ClientController.php

require_once __DIR__ . '/../Models/Client.php';

class ClientController {
    private $clientModel;

    public function __construct() {
        $this->clientModel = new Client();
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $clients = $this->clientModel->getAllClients();
        require_once __DIR__ . '/../Views/clients/index.php';
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $email = $_POST['email'] ?? '';
            $tax_id = $_POST['tax_id'] ?? '';

            // Simple validation
            if (empty($name) || empty($phone) || empty($address) || empty($email) || empty($tax_id)) {
                $error = "Veuillez remplir tous les champs obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse email n'est pas valide.";
            } elseif ($this->clientModel->create($name, $phone, $address, $email, $tax_id)) {
                $_SESSION['success_message'] = "Client ajouté avec succès !";
                header('Location: ' . BASE_URL . '/clients');
                exit();
            } else {
                $error = "Erreur lors de l'ajout du client.";
            }
        }
        require_once __DIR__ . '/../Views/clients/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $client = $this->clientModel->getClientById($id);

        if (!$client) {
            $_SESSION['error_message'] = "Client non trouvé.";
            header('Location: ' . BASE_URL . '/clients');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? $client['name'];
            $phone = $_POST['phone'] ?? $client['phone'];
            $address = $_POST['address'] ?? $client['address'];
            $email = $_POST['email'] ?? $client['email'];
            $tax_id = $_POST['tax_id'] ?? $client['tax_id'];

            if (empty($name) || empty($phone) || empty($address) || empty($email) || empty($tax_id)) {
                $error = "Veuillez remplir tous les champs obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse email n'est pas valide.";
            } elseif ($this->clientModel->update($id, $name, $phone, $address, $email, $tax_id)) {
                $_SESSION['success_message'] = "Client mis à jour avec succès !";
                header('Location: ' . BASE_URL . '/clients');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du client.";
            }
        }
        require_once __DIR__ . '/../Views/clients/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        if ($this->clientModel->delete($id)) {
            $_SESSION['success_message'] = "Client supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du client.";
        }
        header('Location: ' . BASE_URL . '/clients');
        exit();
    }
}
?>