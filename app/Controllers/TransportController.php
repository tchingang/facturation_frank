<?php
// app/Controllers/TransportController.php (Updated with total_transport_cost and transactions)

require_once __DIR__ . '/../Models/Transport.php';
require_once __DIR__ . '/../Models/TransportLigne.php';
require_once __DIR__ . '/../Models/TransportDeclaration.php';
require_once __DIR__ . '/../Models/Client.php'; // Pour récupérer la liste des clients

class TransportController {
    private $transportModel;
    private $transportLigneModel;
    private $transportDeclarationModel;
    private $clientModel;

    public function __construct() {
        $this->transportModel = new Transport();
        $this->transportLigneModel = new TransportLigne();
        $this->transportDeclarationModel = new TransportDeclaration();
        $this->clientModel = new Client(); // Initialiser le modèle Client
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $transports = $this->transportModel->getAllTransports();
        require_once __DIR__ . '/../Views/transports/index.php';
    }

    public function create() {
        $this->requireAuth();
        $error = null;
        $clients = $this->clientModel->getAllClients(); // Récupérer tous les clients

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attestation = $_POST['attestation'] ?? '';
            $status = $_POST['status'] ?? '';
            $date = $_POST['date'] ?? '';
            $client_id = $_POST['client_id'] ?? null;
            // Retrieve the new total_transport_cost field
            $total_transport_cost = (float)($_POST['total_transport_cost'] ?? 0);

            $lines = $_POST['lines'] ?? [];
            $declarations = $_POST['declarations'] ?? [];

            if (empty($attestation) || empty($status) || empty($date) || empty($client_id)) {
                $error = "Veuillez remplir toutes les informations principales du transport.";
            } elseif (empty($lines)) {
                $error = "Veuillez ajouter au moins une ligne de transport.";
            } else {
                try {
                    // Start transaction for atomicity
                    $this->transportModel->beginTransaction();

                    // Pass the new total_transport_cost to the create method
                    $transport_id = $this->transportModel->create($attestation, $status, $date, $client_id, $total_transport_cost);

                    if ($transport_id) {
                        foreach ($lines as $line_data) {
                            $designation = $line_data['designation'] ?? '';
                            if (empty($designation)) {
                                throw new Exception("Désignation de ligne de transport invalide.");
                            }
                            $this->transportLigneModel->create($transport_id, $designation);
                        }

                        foreach ($declarations as $declaration_data) {
                            $designation = $declaration_data['designation'] ?? '';
                            if (empty($designation)) {
                                throw new Exception("Désignation de déclaration invalide.");
                            }
                            $this->transportDeclarationModel->create($transport_id, $designation);
                        }

                        $this->transportModel->commitTransaction(); // Commit if all successful
                        $_SESSION['success_message'] = "Transport ajouté avec succès !";
                        header('Location: ' . BASE_URL . '/transports');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la création du transport principal.");
                    }
                } catch (Exception $e) {
                    $this->transportModel->rollBack(); // Rollback on error
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Transport creation error: " . $e->getMessage());
                    // error_log($e->getTraceAsString()); // Uncomment for detailed stack trace
                }
            }
        }
        require_once __DIR__ . '/../Views/transports/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $transport = $this->transportModel->getTransportById($id);
        $transport_lines = $this->transportLigneModel->getLinesByTransportId($id);
        $transport_declarations = $this->transportDeclarationModel->getDeclarationsByTransportId($id);
        $clients = $this->clientModel->getAllClients(); // Récupérer tous les clients
        $error = null;

        if (!$transport) {
            $_SESSION['error_message'] = "Transport non trouvé.";
            header('Location: ' . BASE_URL . '/transports');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attestation = $_POST['attestation'] ?? $transport['attestation'];
            $status = $_POST['status'] ?? $transport['status'];
            $date = $_POST['date'] ?? $transport['date'];
            $client_id = $_POST['client_id'] ?? $transport['client_id'];
            // Retrieve the new total_transport_cost field
            $total_transport_cost = (float)($_POST['total_transport_cost'] ?? 0); // Use existing value as fallback

            $lines_data = $_POST['lines'] ?? [];
            $declarations_data = $_POST['declarations'] ?? [];

            if (empty($attestation) || empty($status) || empty($date) || empty($client_id)) {
                $error = "Veuillez remplir toutes les informations principales du transport.";
            } elseif (empty($lines_data)) {
                $error = "Veuillez ajouter au moins une ligne de transport.";
            } else {
                try {
                    // Start transaction
                    $this->transportModel->beginTransaction();

                    // Pass the new total_transport_cost to the update method
                    if ($this->transportModel->update($id, $attestation, $status, $date, $client_id, $total_transport_cost)) {
                        // Supprimer les anciennes lignes et déclarations
                        $this->transportLigneModel->deleteByTransportId($id);
                        $this->transportDeclarationModel->deleteByTransportId($id);

                        // Ajouter les nouvelles lignes
                        foreach ($lines_data as $line_data) {
                            $designation = $line_data['designation'] ?? '';
                            if (empty($designation)) {
                                throw new Exception("Désignation de ligne de transport invalide.");
                            }
                            $this->transportLigneModel->create($id, $designation);
                        }

                        // Ajouter les nouvelles déclarations
                        foreach ($declarations_data as $declaration_data) {
                            $designation = $declaration_data['designation'] ?? '';
                            if (empty($designation)) {
                                throw new Exception("Désignation de déclaration invalide.");
                            }
                            $this->transportDeclarationModel->create($id, $designation);
                        }

                        $this->transportModel->commitTransaction(); // Commit if all successful
                        $_SESSION['success_message'] = "Transport mis à jour avec succès !";
                        header('Location: ' . BASE_URL . '/transports');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la mise à jour du transport principal.");
                    }
                } catch (Exception $e) {
                    $this->transportModel->rollBack(); // Rollback on error
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Transport update error: " . $e->getMessage());
                    // error_log($e->getTraceAsString()); // Uncomment for detailed stack trace
                }
            }
        }
        require_once __DIR__ . '/../Views/transports/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        // The delete method in the Transport model already handles transactions internally
        if ($this->transportModel->delete($id)) {
            $_SESSION['success_message'] = "Transport supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du transport. Veuillez réessayer.";
        }
        header('Location: ' . BASE_URL . '/transports');
        exit();
    }
}