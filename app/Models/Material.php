<?php
// app/Models/Material.php

require_once __DIR__ . '/Database.php';

class Material {
    private $conn;
    private $table_name = "materials";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllMaterials() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMaterialById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $description, $quantity, $unit_price_ht) {
        $query = "INSERT INTO " . $this->table_name . " (name, description, quantity, unit_price_ht) VALUES (:name, :description, :quantity, :unit_price_ht)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":unit_price_ht", $unit_price_ht);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $name, $description, $quantity, $unit_price_ht) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, quantity = :quantity, unit_price_ht = :unit_price_ht WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":unit_price_ht", $unit_price_ht);
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
}
?>