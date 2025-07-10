<?php
// app/Models/Supplier.php

require_once __DIR__ . '/Database.php';

class Supplier {
    private $conn;
    private $table_name = "suppliers";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllSuppliers() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupplierById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $phone, $address, $email) {
        $query = "INSERT INTO " . $this->table_name . " (name, phone, address, email) VALUES (:name, :phone, :address, :email)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":email", $email);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $name, $phone, $address, $email) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, phone = :phone, address = :address, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":email", $email);
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