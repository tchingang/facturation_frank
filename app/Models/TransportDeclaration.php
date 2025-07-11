<?php
require_once __DIR__ . '/Database.php';

class TransportDeclaration {
    private $conn;
    private $table_name = "transport_declarations";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getDeclarationsByTransportId($transport_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE transport_id = :transport_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":transport_id", $transport_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($transport_id, $designation) {
        $query = "INSERT INTO " . $this->table_name . " (transport_id, designation) VALUES (:transport_id, :designation)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":transport_id", $transport_id);
        $stmt->bindParam(":designation", $designation);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update($id, $designation) {
        $query = "UPDATE " . $this->table_name . " SET designation = :designation WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":designation", $designation);
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

    public function deleteByTransportId($transport_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE transport_id = :transport_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":transport_id", $transport_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>