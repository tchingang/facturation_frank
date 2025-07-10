<?php
// app/Models/InvoiceLine.php

require_once __DIR__ . '/Database.php';

class InvoiceLine {
    private $conn;
    private $table_name = "invoice_lines"; // Make sure this matches the table name above

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getLinesByInvoiceId($invoice_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE invoice_id = :invoice_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":invoice_id", $invoice_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($invoice_id, $description, $quantity, $unit_price) {
        $query = "INSERT INTO " . $this->table_name . " (invoice_id, description, quantity, unit_price) VALUES (:invoice_id, :description, :quantity, :unit_price)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":invoice_id", $invoice_id);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":unit_price", $unit_price);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function deleteByInvoiceId($invoice_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE invoice_id = :invoice_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":invoice_id", $invoice_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete($id) { // Method to delete a single line if needed
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}