<?php
// app/Models/Invoice.php

require_once __DIR__ . '/Database.php';

class Invoice {
    private $conn;
    private $table_name = "invoices";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Starts a new database transaction.
     * @return bool True on success, false on failure.
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commits the current database transaction.
     * @return bool True on success, false on failure.
     */
    public function commitTransaction() {
        return $this->conn->commit();
    }

    /**
     * Rolls back the current database transaction.
     * @return bool True on success, false on failure.
     */
    public function rollbackTransaction() {
        return $this->conn->rollBack();
    }

    public function getAllInvoices() {
        $query = "SELECT i.*, c.name as client_name FROM " . $this->table_name . " i LEFT JOIN clients c ON i.client_id = c.id ORDER BY i.invoice_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceById($id) {
        $query = "SELECT i.*, c.name as client_name FROM " . $this->table_name . " i LEFT JOIN clients c ON i.client_id = c.id WHERE i.id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($invoice_number, $invoice_date, $due_date, $client_id, $total_amount, $status) {
        $query = "INSERT INTO " . $this->table_name . " (invoice_number, invoice_date, due_date, client_id, total_amount, status) VALUES (:invoice_number, :invoice_date, :due_date, :client_id, :total_amount, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":invoice_number", $invoice_number);
        $stmt->bindParam(":invoice_date", $invoice_date);
        $stmt->bindParam(":due_date", $due_date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":total_amount", $total_amount);
        $stmt->bindParam(":status", $status);

        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $invoice_number, $invoice_date, $due_date, $client_id, $total_amount, $status) {
        $query = "UPDATE " . $this->table_name . " SET invoice_number = :invoice_number, invoice_date = :invoice_date, due_date = :due_date, client_id = :client_id, total_amount = :total_amount, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":invoice_number", $invoice_number);
        $stmt->bindParam(":invoice_date", $invoice_date);
        $stmt->bindParam(":due_date", $due_date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":total_amount", $total_amount);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // Supprimer les lignes de facture associées
            $query_lines = "DELETE FROM invoice_lines WHERE invoice_id = :id";
            $stmt_lines = $this->conn->prepare($query_lines);
            $stmt_lines->bindParam(":id", $id);
            $stmt_lines->execute();

            // Enfin, supprimer la facture elle-même
            $query_invoice = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt_invoice = $this->conn->prepare($query_invoice);
            $stmt_invoice->bindParam(":id", $id);
            $stmt_invoice->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting invoice: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère le prochain numéro de facture basé sur le dernier enregistré.
     * Format: INV-YYYY-NNNN (ex: INV-2025-0001)
     * @return string Le prochain numéro de facture.
     */
    public function getNextInvoiceNumber() {
        $currentYear = date('Y');

        // Récupérer le dernier numéro de facture pour l'année en cours
        $query = "SELECT invoice_number FROM " . $this->table_name . " WHERE invoice_number LIKE :yearPrefix ORDER BY invoice_number DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $yearPrefix = "INV-" . $currentYear . "-%";
        $stmt->bindParam(":yearPrefix", $yearPrefix);
        $stmt->execute();
        $lastInvoice = $stmt->fetch(PDO::FETCH_ASSOC);

        $nextSequence = 1;
        if ($lastInvoice) {
            // Extraire la partie numérique du dernier numéro
            $lastNumber = (int) substr($lastInvoice['invoice_number'], -4);
            $nextSequence = $lastNumber + 1;
        }

        // Formater le numéro séquentiel avec des zéros de tête
        $formattedSequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        return "INV-" . $currentYear . "-" . $formattedSequence;
    }
}