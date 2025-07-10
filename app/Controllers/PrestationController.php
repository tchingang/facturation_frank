<?php
// app/Controllers/PrestationController.php (Corrected)

require_once __DIR__ . '/../Models/Prestation.php';
require_once __DIR__ . '/../Models/PrestationLigne.php';
require_once __DIR__ . '/../Models/Client.php'; // Make sure Client model is included

class PrestationController {
    private $prestationModel;
    private $prestationLigneModel;
    private $clientModel; // Initialize clientModel

    public function __construct() {
        $this->prestationModel = new Prestation();
        $this->prestationLigneModel = new PrestationLigne();
        $this->clientModel = new Client(); // Initialize
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $prestations = $this->prestationModel->getAllPrestations();
        require_once __DIR__ . '/../Views/prestations/index.php';
    }

    public function create() {
        $this->requireAuth();
        $error = null;
        $clients = $this->clientModel->getAllClients(); // Fetch clients for the form

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attestation = $_POST['attestation_number'] ?? ''; // Assuming this field name from your form
            $status = $_POST['status'] ?? 'Brouillon';
            $date = $_POST['prestation_date'] ?? ''; // Assuming this field name from your form
            $client_id = $_POST['client_id'] ?? null; // Get client_id from the form

            // Basic validation
            if (empty($attestation) || empty($date) || empty($client_id)) {
                $error = "Veuillez remplir tous les champs obligatoires (numéro d'attestation, date, client).";
            } else {
                try {
                    // Pass all 4 arguments to the create method
                    $prestation_id = $this->prestationModel->create($attestation, $status, $date, $client_id); // <--- FIX IS HERE

                    if ($prestation_id) {
                        // Handle prestation lines if your form supports them
                        // For example:
                        // $lines_data = $_POST['lines'] ?? [];
                        // foreach ($lines_data as $line) {
                        //     $this->prestationLigneModel->create($prestation_id, $line['description'], $line['quantity'], $line['unit_price']);
                        // }

                        $_SESSION['success_message'] = "Prestation ajoutée avec succès !";
                        header('Location: ' . BASE_URL . '/prestations');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la création de la prestation principale.");
                    }
                } catch (Exception $e) {
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Prestation creation error: " . $e->getMessage());
                }
            }
        }
        require_once __DIR__ . '/../Views/prestations/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $prestation = $this->prestationModel->getPrestationById($id);
        $clients = $this->clientModel->getAllClients(); // Fetch clients for the form
        $error = null;

        if (!$prestation) {
            $_SESSION['error_message'] = "Prestation non trouvée.";
            header('Location: ' . BASE_URL . '/prestations');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attestation = $_POST['attestation_number'] ?? $prestation['attestation'];
            $status = $_POST['status'] ?? $prestation['status'];
            $date = $_POST['prestation_date'] ?? $prestation['date'];
            $client_id = $_POST['client_id'] ?? $prestation['client_id']; // Get client_id from the form

            if (empty($attestation) || empty($date) || empty($client_id)) {
                $error = "Veuillez remplir tous les champs obligatoires (numéro d'attestation, date, client).";
            } else {
                try {
                    // Pass all 5 arguments to the update method
                    if ($this->prestationModel->update($id, $attestation, $status, $date, $client_id)) { // <--- FIX IS HERE
                        // Update associated lines if needed
                        $_SESSION['success_message'] = "Prestation mise à jour avec succès !";
                        header('Location: ' . BASE_URL . '/prestations');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la mise à jour de la prestation principale.");
                    }
                } catch (Exception $e) {
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Prestation update error: " . $e->getMessage());
                }
            }
        }
        require_once __DIR__ . '/../Views/prestations/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        if ($this->prestationModel->delete($id)) {
            $_SESSION['success_message'] = "Prestation supprimée avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de la prestation. Veuillez réessayer.";
        }
        header('Location: ' . BASE_URL . '/prestations');
        exit();
    }
}