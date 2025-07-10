<?php
// app/Models/WeighbridgeTax.php

require_once __DIR__ . '/Database.php';

class WeighbridgeTax {
    private $conn;
    private $table_name = "weighbridge_taxes";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getTaxesByWeighbridgeId($weighbridge_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE weighbridge_id = :weighbridge_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weighbridge_id", $weighbridge_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($weighbridge_id, $tax_description, $tax_amount) {
        $query = "INSERT INTO " . $this->table_name . " (weighbridge_id, tax_description, tax_amount) VALUES (:weighbridge_id, :tax_description, :tax_amount)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":weighbridge_id", $weighbridge_id);
        $stmt->bindParam(":tax_description", $tax_description);
        $stmt->bindParam(":tax_amount", $tax_amount);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $tax_description, $tax_amount) {
        $query = "UPDATE " . $this->table_name . " SET tax_description = :tax_description, tax_amount = :tax_amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tax_description", $tax_description);
        $stmt->bindParam(":tax_amount", $tax_amount);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function deleteByWeighbridgeId($weighbridge_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE weighbridge_id = :weighbridge_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weighbridge_id", $weighbridge_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>