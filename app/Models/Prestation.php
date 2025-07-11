<?php
// app/Models/Prestation.php (Updated)

require_once __DIR__ . '/Database.php';

class Prestation {
    private $conn;
    private $table_name = "prestations";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Récupère toutes les prestations avec des infos agrégées si nécessaire
    public function getAllPrestations() {
        $query = "SELECT p.*, p.billed_status FROM " . $this->table_name . " p ORDER BY p.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère une prestation par ID
    public function getPrestationById($id) {
        $query = "SELECT *, billed_status FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les prestations qui n'ont pas encore été facturées.
     * @return array Tableau associatif de prestations.
     */
    public function getUnbilledPrestations() {
        $query = "SELECT p.* FROM " . $this->table_name . " p WHERE p.billed_status = 'Unbilled' ORDER BY p.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle prestation principale.
     * Le statut facturé est initialisé à 'Unbilled' par défaut.
     */
    public function create($attestation, $status, $date, $client_id) {
        $query = "INSERT INTO " . $this->table_name . " (attestation, status, date, client_id, billed_status) VALUES (:attestation, :status, :date, :client_id, 'Unbilled')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":attestation", $attestation);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":client_id", $client_id);

        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour une prestation principale.
     * Le statut facturé n'est pas modifié ici, mais via updateBilledStatus.
     */
    public function update($id, $attestation, $status, $date, $client_id) {
        $query = "UPDATE " . $this->table_name . " SET attestation = :attestation, status = :status, date = :date, client_id = :client_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":attestation", $attestation);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Met à jour le statut de facturation d'une prestation.
     * @param int $id L'ID de la prestation.
     * @param string $status Le nouveau statut ('Unbilled', 'Billed', 'Paid', 'Overdue').
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function updateBilledStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET billed_status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Supprime une prestation et ses lignes/déclarations associées
    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // Supprimer les lignes de prestation associées
            $query_lines = "DELETE FROM prestation_lines WHERE prestation_id = :id";
            $stmt_lines = $this->conn->prepare($query_lines);
            $stmt_lines->bindParam(":id", $id);
            $stmt_lines->execute();

            // Supprimer les déclarations associées
            $query_declarations = "DELETE FROM declarations WHERE prestation_id = :id";
            $stmt_declarations = $this->conn->prepare($query_declarations);
            $stmt_declarations->bindParam(":id", $id);
            $stmt_declarations->execute();

            // Enfin, supprimer la prestation elle-même
            $query_prestation = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt_prestation = $this->conn->prepare($query_prestation);
            $stmt_prestation->bindParam(":id", $id);
            $stmt_prestation->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting prestation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère toutes les prestations qui n'ont pas encore été facturées.
     * @return array Tableau des prestations non facturées.
     */
    public function getAllUnbilledPrestations() {
        $query = "SELECT p.*, c.name AS client_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clients c ON p.client_id = c.id
                  WHERE p.is_billed = FALSE
                  ORDER BY p.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}