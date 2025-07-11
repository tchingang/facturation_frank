<?php
// app/Controllers/WeighbridgeController.php

require_once __DIR__ . '/../Models/Weighbridge.php';
require_once __DIR__ . '/../Models/WeighbridgeTax.php';
require_once __DIR__ . '/../Models/Client.php';

class WeighbridgeController {
    private $weighbridgeModel;
    private $weighbridgeTaxModel;
    private $clientModel;

    public function __construct() {
        $this->weighbridgeModel = new Weighbridge();
        $this->weighbridgeTaxModel = new WeighbridgeTax();
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
        $weighbridges = $this->weighbridgeModel->getAllWeighbridges();
        require_once __DIR__ . '/../Views/weighbridges/index.php';
    }

    public function create() {
        $this->requireAuth();
        $error = null;
        $clients = $this->clientModel->getAllClients();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $weigh_number = $_POST['weigh_number'] ?? '';
            $weigh_date = $_POST['weigh_date'] ?? '';
            $client_id = $_POST['client_id'] ?? null;
            $vehicle_number = $_POST['vehicle_number'] ?? '';
            $driver_name = $_POST['driver_name'] ?? '';
            $first_weight = (float)($_POST['first_weight'] ?? 0);
            $second_weight = (float)($_POST['second_weight'] ?? 0);
            $net_weight = $first_weight - $second_weight;
            $notes = $_POST['notes'] ?? '';

            $taxes = $_POST['taxes'] ?? [];

            if (empty($weigh_number) || empty($weigh_date) || empty($client_id) || empty($vehicle_number) || empty($driver_name) || $first_weight <= 0 || $second_weight <= 0 || empty($taxes)) {
                $error = "Veuillez remplir toutes les informations principales du pont bascule et ajouter au moins une taxe.";
            } else {
                try {
                    $total_amount = 0;
                    foreach ($taxes as $tax_data) {
                        $tax_amount = (float)($tax_data['tax_amount'] ?? 0);
                        $total_amount += $tax_amount;
                    }

                    $weighbridge_id = $this->weighbridgeModel->create($weigh_number, $weigh_date, $client_id, $vehicle_number, $driver_name, $first_weight, $second_weight, $net_weight, $total_amount, $notes);

                    if ($weighbridge_id) {
                        foreach ($taxes as $tax_data) {
                            $tax_description = $tax_data['tax_description'] ?? '';
                            $tax_amount = (float)($tax_data['tax_amount'] ?? 0);

                            if (empty($tax_description) || $tax_amount < 0) {
                                throw new Exception("Détails de taxe invalides.");
                            }
                            $this->weighbridgeTaxModel->create($weighbridge_id, $tax_description, $tax_amount);
                        }

                        $_SESSION['success_message'] = "Enregistrement de pont bascule ajouté avec succès !";
                        header('Location: ' . BASE_URL . '/weighbridges');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la création de l'enregistrement de pont bascule.");
                    }
                } catch (Exception $e) {
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Weighbridge creation error: " . $e->getMessage());
                }
            }
        }
        require_once __DIR__ . '/../Views/weighbridges/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $weighbridge = $this->weighbridgeModel->getWeighbridgeById($id);
        $weighbridge_taxes = $this->weighbridgeTaxModel->getTaxesByWeighbridgeId($id);
        $clients = $this->clientModel->getAllClients();
        $error = null;

        if (!$weighbridge) {
            $_SESSION['error_message'] = "Enregistrement de pont bascule non trouvé.";
            header('Location: ' . BASE_URL . '/weighbridges');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $weigh_number = $_POST['weigh_number'] ?? $weighbridge['weigh_number'];
            $weigh_date = $_POST['weigh_date'] ?? $weighbridge['weigh_date'];
            $client_id = $_POST['client_id'] ?? $weighbridge['client_id'];
            $vehicle_number = $_POST['vehicle_number'] ?? $weighbridge['vehicle_number'];
            $driver_name = $_POST['driver_name'] ?? $weighbridge['driver_name'];
            $first_weight = (float)($_POST['first_weight'] ?? 0);
            $second_weight = (float)($_POST['second_weight'] ?? 0);
            $net_weight = $first_weight - $second_weight; // Recalcul du poids net
            $notes = $_POST['notes'] ?? $weighbridge['notes'];

            $taxes_data = $_POST['taxes'] ?? [];

            if (empty($weigh_number) || empty($weigh_date) || empty($client_id) || empty($vehicle_number) || empty($driver_name) || $first_weight <= 0 || $second_weight <= 0 || empty($taxes_data)) {
                $error = "Veuillez remplir toutes les informations principales du pont bascule et ajouter au moins une taxe.";
            } else {
                try {
                    $total_amount = 0;
                    foreach ($taxes_data as $tax_data) {
                        $tax_amount = (float)($tax_data['tax_amount'] ?? 0);
                        $total_amount += $tax_amount;
                    }

                    if ($this->weighbridgeModel->update($id, $weigh_number, $weigh_date, $client_id, $vehicle_number, $driver_name, $first_weight, $second_weight, $net_weight, $total_amount, $notes)) {
                        // Supprimer les anciennes taxes
                        $this->weighbridgeTaxModel->deleteByWeighbridgeId($id);

                        // Ajouter les nouvelles taxes
                        foreach ($taxes_data as $tax_data) {
                            $tax_description = $tax_data['tax_description'] ?? '';
                            $tax_amount = (float)($tax_data['tax_amount'] ?? 0);

                            if (empty($tax_description) || $tax_amount < 0) {
                                throw new Exception("Détails de taxe invalides.");
                            }
                            $this->weighbridgeTaxModel->create($id, $tax_description, $tax_amount);
                        }

                        $_SESSION['success_message'] = "Enregistrement de pont bascule mis à jour avec succès !";
                        header('Location: ' . BASE_URL . '/weighbridges');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la mise à jour de l'enregistrement de pont bascule principal.");
                    }
                } catch (Exception $e) {
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Weighbridge update error: " . $e->getMessage());
                }
            }
        }
        require_once __DIR__ . '/../Views/weighbridges/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        if ($this->weighbridgeModel->delete($id)) {
            $_SESSION['success_message'] = "Enregistrement de pont bascule supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de l'enregistrement de pont bascule. Veuillez réessayer.";
        }
        header('Location: ' . BASE_URL . '/weighbridges');
        exit();
    }
}
?>