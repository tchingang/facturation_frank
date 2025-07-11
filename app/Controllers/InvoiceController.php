<?php
// app/Controllers/InvoiceController.php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/InvoiceLine.php';
require_once __DIR__ . '/../Models/Client.php';
require_once __DIR__ . '/../Models/Prestation.php';
require_once __DIR__ . '/../Models/PrestationLigne.php';
require_once __DIR__ . '/../Models/Transport.php';
require_once __DIR__ . '/../Models/TransportLigne.php';
require_once __DIR__ . '/../Models/Weighbridge.php';
require_once __DIR__ . '/../Models/WeighbridgeTax.php';


class InvoiceController {
    private $invoiceModel;
    private $invoiceLineModel;
    private $clientModel;
    private $prestationModel;
    private $prestationLigneModel;
    private $transportModel;
    private $transportLigneModel;
    private $weighbridgeModel;
    private $weighbridgeTaxModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->invoiceLineModel = new InvoiceLine();
        $this->clientModel = new Client();
        $this->prestationModel = new Prestation();
        $this->prestationLigneModel = new PrestationLigne();
        $this->transportModel = new Transport();
        $this->transportLigneModel = new TransportLigne();
        $this->weighbridgeModel = new Weighbridge();
        $this->weighbridgeTaxModel = new WeighbridgeTax();
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function index() {
        $this->requireAuth();
        $invoices = $this->invoiceModel->getAllInvoices();
        require_once __DIR__ . '/../Views/invoices/index.php';
    }
    public function create() {
        $this->requireAuth();

        $error = '';
        $clients = $this->clientModel->getAllClients();
        $prestations = $this->prestationModel->getAllUnbilledPrestations();
        $transports = $this->transportModel->getAllUnbilledTransports();
        $weighbridges = $this->weighbridgeModel->getAllUnbilledWeighbridges();

        // Générer le prochain numéro de facture
        $nextInvoiceNumber = $this->invoiceModel->getNextInvoiceNumber();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $invoice_number = $_POST['invoice_number'];
            $invoice_date = $_POST['invoice_date'];
            $due_date = $_POST['due_date'];
            $client_id = $_POST['client_id'];
            $status = $_POST['status'];
            $invoice_lines_data = json_decode($_POST['invoice_lines_json'] ?? '[]', true);

            $total_amount = 0;
            foreach ($invoice_lines_data as $line) {
                $total_amount += ($line['quantity'] * $line['unit_price']);
            }

            try {
                $this->invoiceModel->beginTransaction();

                $invoice_id = $this->invoiceModel->create(
                    $invoice_number,
                    $invoice_date,
                    $due_date,
                    $client_id,
                    $total_amount,
                    $status
                );

                if ($invoice_id) {
                    foreach ($invoice_lines_data as $line) {
                        if (!$this->invoiceLineModel->create(
                            $invoice_id,
                            $line['description'],
                            $line['quantity'],
                            $line['unit_price']
                        )) {
                            throw new Exception("Erreur lors de l'ajout d'une ligne de facture.");
                        }

                        if (isset($line['type']) && isset($line['source_id'])) {
                            switch ($line['type']) {
                                case 'prestation':
                                    $this->prestationModel->updateBilledStatus($line['source_id'], 'Billed');
                                    break;
                                case 'transport':
                                    $this->transportModel->updateBilledStatus($line['source_id'], 'Billed');
                                    break;
                                case 'weighbridge':
                                    $this->weighbridgeModel->updateBilledStatus($line['source_id'], 'Billed');
                                    break;
                            }
                        }
                    }

                    $this->invoiceModel->commitTransaction();
                    $_SESSION['success_message'] = "Facture créée avec succès!";
                    header('Location: ' . BASE_URL . '/invoices');
                    exit();
                } else {
                    throw new Exception("Erreur lors de la création de la facture principale.");
                }
            } catch (Exception $e) {
                $this->invoiceModel->rollbackTransaction();
                $error = "Erreur: " . $e->getMessage();
                error_log("Invoice creation error: " . $e->getMessage()); 
            }
        }

        // Passage des données à la vue
        require_once __DIR__ . '/../Views/invoices/create.php';
    }

    public function edit($id) {
        $this->requireAuth();
        $invoice = $this->invoiceModel->getInvoiceById($id);
        $invoice_lines = $this->invoiceLineModel->getLinesByInvoiceId($id);
        $clients = $this->clientModel->getAllClients();
        // Fetch all items for client, regardless of billed status, for editing flexibility
        $prestations = $this->prestationModel->getAllPrestations();
        $transports = $this->transportModel->getAllTransports();
        $weighbridges = $this->weighbridgeModel->getAllWeighbridges();
        $error = null;

        if (!$invoice) {
            $_SESSION['error_message'] = "Facture non trouvée.";
            header('Location: ' . BASE_URL . '/invoices');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice_number = $_POST['invoice_number'] ?? $invoice['invoice_number'];
            $invoice_date = $_POST['invoice_date'] ?? $invoice['invoice_date'];
            $due_date = $_POST['due_date'] ?? $invoice['due_date'];
            $client_id = $_POST['client_id'] ?? $invoice['client_id'];
            $status = $_POST['status'] ?? $invoice['status'];

            $invoice_lines_data_from_post = $_POST['invoice_lines'] ?? [];

            if (empty($invoice_number) || empty($invoice_date) || empty($due_date) || empty($client_id)) {
                $error = "Veuillez remplir toutes les informations principales de la facture.";
            } elseif (empty($invoice_lines_data_from_post)) {
                $error = "Veuillez ajouter au moins une ligne à la facture.";
            } else {
                try {
                    $total_amount = 0;
                    $all_invoice_lines_to_save = [];
                    $newly_billed_prestation_ids = [];
                    $newly_billed_transport_ids = [];
                    $newly_billed_weighbridge_ids = [];

                    $this->invoiceModel->beginTransaction();

                    $this->invoiceLineModel->deleteByInvoiceId($id);

                    foreach ($invoice_lines_data_from_post as $line_data) {
                        $type = $line_data['type'] ?? 'manual';
                        $description = trim($line_data['description'] ?? '');
                        $quantity = (float)($line_data['quantity'] ?? 0);
                        $unit_price = (float)($line_data['unit_price'] ?? 0);
                        $source_id = $line_data['source_id'] ?? null;

                        if (empty($description) || $quantity <= 0 || $unit_price < 0) {
                            throw new Exception("Détails de ligne de facture invalides pour une ligne de type '{$type}'.");
                        }

                        $all_invoice_lines_to_save[] = [
                            'description' => $description,
                            'quantity' => $quantity,
                            'unit_price' => $unit_price
                        ];
                        $total_amount += ($quantity * $unit_price);

                        if ($type === 'prestation' && $source_id) {
                            $newly_billed_prestation_ids[] = $source_id;
                        } elseif ($type === 'transport' && $source_id) {
                            $newly_billed_transport_ids[] = $source_id;
                        } elseif ($type === 'weighbridge' && $source_id) {
                            $newly_billed_weighbridge_ids[] = $source_id;
                        }
                    }

                    if ($this->invoiceModel->update($id, $invoice_number, $invoice_date, $due_date, $client_id, $total_amount, $status)) {

                        foreach ($all_invoice_lines_to_save as $line_data) {
                            $this->invoiceLineModel->create($id, $line_data['description'], $line_data['quantity'], $line_data['unit_price']);
                        }

                        foreach ($newly_billed_prestation_ids as $p_id) {
                            $this->prestationModel->updateBilledStatus($p_id, 'Billed');
                        }
                        foreach ($newly_billed_transport_ids as $t_id) {
                            $this->transportModel->updateBilledStatus($t_id, 'Billed');
                        }
                        foreach ($newly_billed_weighbridge_ids as $w_id) {
                            $this->weighbridgeModel->updateBilledStatus($w_id, 'Billed');
                        }

                        $this->invoiceModel->commitTransaction();
                        $_SESSION['success_message'] = "Facture mise à jour avec succès !";
                        header('Location: ' . BASE_URL . '/invoices');
                        exit();
                    } else {
                        throw new Exception("Erreur lors de la mise à jour de la facture principale.");
                    }
                } catch (Exception $e) {
                    $this->invoiceModel->rollbackTransaction();
                    $error = "Erreur: " . $e->getMessage();
                    error_log("Invoice update error: " . $e->getMessage());
                    error_log($e->getTraceAsString());
                }
            }
        }
        require_once __DIR__ . '/../Views/invoices/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();


        try {
            $this->invoiceModel->beginTransaction();
            if ($this->invoiceModel->delete($id)) {
                $this->invoiceModel->commitTransaction();
                $_SESSION['success_message'] = "Facture supprimée avec succès !";
            } else {
                throw new Exception("Erreur lors de la suppression de la facture. Veuillez réessayer.");
            }
        } catch (Exception $e) {
            $this->invoiceModel->rollbackTransaction();
            $_SESSION['error_message'] = "Erreur lors de la suppression de la facture: " . $e->getMessage();
            error_log("Invoice deletion error: " . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '/invoices');
        exit();
    }

    /**
     * Génère une facture au format PDF.
     * @param int $id L'ID de la facture à générer.
     */
    public function generatePdf($id) {
        $this->requireAuth();

        $invoice = $this->invoiceModel->getInvoiceById($id);

        if (!$invoice) {
            $_SESSION['error_message'] = "Facture non trouvée pour la génération PDF.";
            header('Location: ' . BASE_URL . '/invoices');
            exit();
        }

        $invoice_lines = $this->invoiceLineModel->getLinesByInvoiceId($id);
        $client = $this->clientModel->getClientById($invoice['client_id']);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);

        ob_start();
        require __DIR__ . '/../Views/invoices/invoice_pdf_template.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $invoice_number = htmlspecialchars($invoice['invoice_number']);
        $dompdf->stream("Facture_N_{$invoice_number}.pdf", array("Attachment" => false));
        exit();
    }
}