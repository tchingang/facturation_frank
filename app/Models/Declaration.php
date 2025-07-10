<?php
// app/Models/Declaration.php

require_once __DIR__ . '/Database.php';

class Declaration {
    private $conn;
    private $table_name = "declarations";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getDeclarationsByPrestationId($prestation_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prestation_id = :prestation_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":prestation_id", $prestation_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($prestation_id, $nature, $designation, $regime, $poids, $valeur) {
        $query = "INSERT INTO " . $this->table_name . " (prestation_id, nature, designation, regime, poids, valeur) VALUES (:prestation_id, :nature, :designation, :regime, :poids, :valeur)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":prestation_id", $prestation_id);
        $stmt->bindParam(":nature", $nature);
        $stmt->bindParam(":designation", $designation);
        $stmt->bindParam(":regime", $regime);
        $stmt->bindParam(":poids", $poids);
        $stmt->bindParam(":valeur", $valeur);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $nature, $designation, $regime, $poids, $valeur) {
        $query = "UPDATE " . $this->table_name . " SET nature = :nature, designation = :designation, regime = :regime, poids = :poids, valeur = :valeur WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nature", $nature);
        $stmt->bindParam(":designation", $designation);
        $stmt->bindParam(":regime", $regime);
        $stmt->bindParam(":poids", $poids);
        $stmt->bindParam(":valeur", $valeur);
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

    public function deleteByPrestationId($prestation_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE prestation_id = :prestation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":prestation_id", $prestation_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>