<?php
// app/Models/Transport.php

require_once __DIR__ . '/Database.php';

class Transport {
    private $conn;
    private $table_name = "transports";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Démarre une nouvelle transaction de base de données.
     * @return bool True en cas de succès, false en cas d'échec.
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Valide la transaction de base de données actuelle.
     * @return bool True en cas de succès, false en cas d'échec.
     */
    public function commitTransaction() {
        return $this->conn->commit();
    }

    /**
     * Annule la transaction de base de données actuelle.
     * @return bool True en cas de succès, false en cas d'échec.
     */
    public function rollBack() { // Note: Nommé rollBack pour correspondre à PDO
        return $this->conn->rollBack();
    }

    public function getAllTransports() {
        $query = "SELECT t.*, c.name as client_name FROM " . $this->table_name . " t LEFT JOIN clients c ON t.client_id = c.id ORDER BY t.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransportById($id) {
        $query = "SELECT *, billed_status, total_transport_cost FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUnbilledTransports() {
        $query = "SELECT t.* FROM " . $this->table_name . " t WHERE t.billed_status = 'Unbilled' ORDER BY t.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($attestation, $status, $date, $client_id, $total_transport_cost) {
        $query = "INSERT INTO " . $this->table_name . " (attestation, status, date, client_id, total_transport_cost, billed_status) VALUES (:attestation, :status, :date, :client_id, :total_transport_cost, 'Unbilled')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":attestation", $attestation);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":total_transport_cost", $total_transport_cost);
        
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $attestation, $status, $date, $client_id, $total_transport_cost) {
        $query = "UPDATE " . $this->table_name . " SET attestation = :attestation, status = :status, date = :date, client_id = :client_id, total_transport_cost = :total_transport_cost WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":attestation", $attestation);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":total_transport_cost", $total_transport_cost);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function updateBilledStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET billed_status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        try {
            // Note: Cette méthode utilise directement $this->conn pour la transaction,
            // car elle encapsule la suppression de plusieurs éléments liés au transport.
            $this->conn->beginTransaction();

            $query_lines = "DELETE FROM transport_lines WHERE transport_id = :id";
            $stmt_lines = $this->conn->prepare($query_lines);
            $stmt_lines->bindParam(":id", $id);
            $stmt_lines->execute();

            $query_declarations = "DELETE FROM transport_declarations WHERE transport_id = :id";
            $stmt_declarations = $this->conn->prepare($query_declarations);
            $stmt_declarations->bindParam(":id", $id);
            $stmt_declarations->execute();

            $query_transport = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt_transport = $this->conn->prepare($query_transport);
            $stmt_transport->bindParam(":id", $id);
            $stmt_transport->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting transport: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les transports qui n'ont pas encore été facturés.
     * @return array Tableau des transports non facturés.
     */
    public function getAllUnbilledTransports() {
        $query = "SELECT t.*, c.name AS client_name
                  FROM " . $this->table_name . " t
                  LEFT JOIN clients c ON t.client_id = c.id
                  WHERE t.is_billed = FALSE
                  ORDER BY t.date DESC"; // Utilisez t.date si c'est le nom de votre colonne de date
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}