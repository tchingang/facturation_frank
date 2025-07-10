<?php
// app/Models/PrestationLigne.php

require_once __DIR__ . '/Database.php';

class PrestationLigne {
    private $conn;
    private $table_name = "prestation_lines";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getLinesByPrestationId($prestation_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prestation_id = :prestation_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":prestation_id", $prestation_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($prestation_id, $designation, $base_calcul, $prix_unitaire, $montant_ht, $tva, $montant_ttc) {
        $query = "INSERT INTO " . $this->table_name . " (prestation_id, designation, base_calcul, prix_unitaire, montant_ht, tva, montant_ttc) VALUES (:prestation_id, :designation, :base_calcul, :prix_unitaire, :montant_ht, :tva, :montant_ttc)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":prestation_id", $prestation_id);
        $stmt->bindParam(":designation", $designation);
        $stmt->bindParam(":base_calcul", $base_calcul);
        $stmt->bindParam(":prix_unitaire", $prix_unitaire);
        $stmt->bindParam(":montant_ht", $montant_ht);
        $stmt->bindParam(":tva", $tva);
        $stmt->bindParam(":montant_ttc", $montant_ttc);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $designation, $base_calcul, $prix_unitaire, $montant_ht, $tva, $montant_ttc) {
        $query = "UPDATE " . $this->table_name . " SET designation = :designation, base_calcul = :base_calcul, prix_unitaire = :prix_unitaire, montant_ht = :montant_ht, tva = :tva, montant_ttc = :montant_ttc WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":designation", $designation);
        $stmt->bindParam(":base_calcul", $base_calcul);
        $stmt->bindParam(":prix_unitaire", $prix_unitaire);
        $stmt->bindParam(":montant_ht", $montant_ht);
        $stmt->bindParam(":tva", $tva);
        $stmt->bindParam(":montant_ttc", $montant_ttc);
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