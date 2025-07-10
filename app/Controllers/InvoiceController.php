<?php
// app/Controllers/InvoiceController.php (Corrected for array keys and total_transport_cost)

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
        $clients = $this->clientModel->getAllClients(); // Assurez-vous que cette méthode existe
        $prestations = $this->prestationModel->getAllUnbilledPrestations(); // Récupérer uniquement les prestations non facturées
        $transports = $this->transportModel->getAllUnbilledTransports(); // Récupérer uniquement les transports non facturés
        $weighbridges = $this->weighbridgeModel->getAllUnbilledWeighbridges(); // Récupérer uniquement les ponts bascule non facturés

        // Générer le prochain numéro de facture
        $nextInvoiceNumber = $this->invoiceModel->getNextInvoiceNumber();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ... (your existing POST logic for creating invoice) ...
            $invoice_number = $_POST['invoice_number']; // This will now come from the generated value
            $invoice_date = $_POST['invoice_date'];
            $due_date = $_POST['due_date'];
            $client_id = $_POST['client_id'];
            $status = $_POST['status'];
            $invoice_lines_data = json_decode($_POST['invoice_lines_json'] ?? '[]', true); // Assurez-vous que c'est la bonne façon de récupérer les lignes

            // Calculate total_amount before creating the invoice
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
                    $total_amount, // Passez le total calculé
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

                        // Mettre à jour le statut "billed" pour les éléments source
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
                $this->invoiceModel->rollBack();
                $error = "Erreur: " . $e->getMessage();
                error_log("Invoice creation error: " . $e->getMessage()); // Log error for debugging
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

                    // Start Transaction
                    $this->invoiceModel->beginTransaction();

                    // --- Determine items to unbill (those removed from the invoice) ---
                    // This is a simplified approach. A more robust solution would involve:
                    // 1. Storing source_id and type directly in invoice_lines table.
                    // 2. Fetching current invoice lines' source_ids and types.
                    // 3. Comparing with $invoice_lines_data_from_post to find removed items.
                    // For now, we'll assume any item not in the POST data should be unbilled if it was previously billed by THIS invoice.
                    // This means we need to fetch the original items linked to this invoice.
                    // This requires having `source_id` and `type` columns in your `invoice_lines` table.
                    // If you don't have them, this part will be more complex (parsing descriptions).

                    // For now, let's just collect current items for potential unbilling if they are not re-selected.
                    // This is a placeholder for a more complex logic.
                    // $current_linked_prestations = []; // Fetch from DB based on invoice_lines.description
                    // $current_linked_transports = [];
                    // $current_linked_weighbridges = [];

                    // Delete existing lines first (simplifies update logic, but loses original source_id links if not stored)
                    $this->invoiceLineModel->deleteByInvoiceId($id);

                    foreach ($invoice_lines_data_from_post as $line_data) {
                        $type = $line_data['type'] ?? 'manual';
                        $description = trim($line_data['description'] ?? '');
                        $quantity = (float)($line_data['quantity'] ?? 0);
                        $unit_price = (float)($line_data['unit_price'] ?? 0);
                        $source_id = $line_data['source_id'] ?? null; // For linked items

                        if (empty($description) || $quantity <= 0 || $unit_price < 0) {
                            throw new Exception("Détails de ligne de facture invalides pour une ligne de type '{$type}'.");
                        }

                        // Collect data for saving to invoice_lines table
                        $all_invoice_lines_to_save[] = [
                            'description' => $description,
                            'quantity' => $quantity,
                            'unit_price' => $unit_price
                        ];
                        $total_amount += ($quantity * $unit_price);

                        // Collect IDs for status update if it's a linked item
                        if ($type === 'prestation' && $source_id) {
                            $newly_billed_prestation_ids[] = $source_id;
                        } elseif ($type === 'transport' && $source_id) {
                            $newly_billed_transport_ids[] = $source_id;
                        } elseif ($type === 'weighbridge' && $source_id) {
                            $newly_billed_weighbridge_ids[] = $source_id;
                        }
                    }

                    if ($this->invoiceModel->update($id, $invoice_number, $invoice_date, $due_date, $client_id, $total_amount, $status)) {
                        // Recreate invoice lines
                        foreach ($all_invoice_lines_to_save as $line_data) {
                            $this->invoiceLineModel->create($id, $line_data['description'], $line_data['quantity'], $line_data['unit_price']);
                        }

                        // Update status of newly billed items to 'Billed'
                        // For items that were previously billed by THIS invoice but are now *removed*,
                        // their status should ideally revert to 'Unbilled'. This requires the more complex logic
                        // mentioned above (tracking original source_ids in invoice_lines).
                        // For this current setup, we're only marking newly selected items as 'Billed'.
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
                    // error_log($e->getTraceAsString());
                }
            }
        }
        require_once __DIR__ . '/../Views/invoices/edit.php';
    }

    public function delete($id) {
        $this->requireAuth();
        // IMPORTANT: When deleting an invoice, you should revert the billed_status
        // of all associated prestations, transports, and weighbridges back to 'Unbilled'.
        // This requires:
        // 1. Fetching all invoice lines for this invoice ID.
        // 2. Identifying which original service (prestation, transport, weighbridge) each line refers to.
        //    This is easiest if your `invoice_lines` table has columns like `source_type` and `source_id`.
        // 3. Calling `updateBilledStatus($id, 'Unbilled')` on the respective models.

        // Placeholder for the logic to revert billed_status on associated items
        // $invoice_lines_to_revert = $this->invoiceLineModel->getLinesByInvoiceId($id);
        // foreach ($invoice_lines_to_revert as $line) {
        //     // Logic to parse description or use source_type/source_id from invoice_lines
        //     // and call appropriate model's updateBilledStatus
        // }

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
        $client = $this->clientModel->getClientById($invoice['client_id']); // Récupérer les détails complets du client

        // 1. Instancier Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true); // Active l'analyseur HTML5
        $options->set('isRemoteEnabled', true);     // Permet de charger des images ou CSS externes (si besoin)
        $options->set('defaultFont', 'Helvetica'); // Définir une police par défaut compatible

        $dompdf = new Dompdf($options);

        // 2. Charger le contenu HTML de la vue PDF
        // Utiliser ob_start() pour capturer la sortie du fichier de vue
        ob_start();
        require __DIR__ . '/../Views/invoices/invoice_pdf_template.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);

        // (Optionnel) Définir la taille du papier et l'orientation
        $dompdf->setPaper('A4', 'portrait');

        // 3. Rendre le HTML en PDF
        $dompdf->render();

        // 4. Sortir le PDF vers le navigateur
        $invoice_number = htmlspecialchars($invoice['invoice_number']);
        $dompdf->stream("Facture_N_{$invoice_number}.pdf", array("Attachment" => false)); // "Attachment" => false pour l'afficher dans le navigateur
        exit();
    }
}