<?php
require_once __DIR__ . '/Database.php';

class Weighbridge {
    private $conn;
    private $table_name = "weighbridges";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllWeighbridges() {
        // Ensure billed_status is selected
        $query = "SELECT w.*, c.name as client_name, w.billed_status FROM " . $this->table_name . " w LEFT JOIN clients c ON w.client_id = c.id ORDER BY w.weigh_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWeighbridgeById($id) {
        // Ensure billed_status is selected
        $query = "SELECT *, billed_status FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les enregistrements de pont bascule qui n'ont pas encore été facturés.
     * @return array Tableau associatif des enregistrements de pont bascule.
     */
    public function getUnbilledWeighbridges() {
        // Assuming 'Unbilled' is the status for items not yet on an invoice
        $query = "SELECT w.* FROM " . $this->table_name . " w WHERE w.billed_status = 'Unbilled' ORDER BY w.weigh_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel enregistrement de pont bascule.
     * Le statut facturé est initialisé à 'Unbilled' par défaut.
     */
    public function create($weigh_number, $weigh_date, $client_id, $vehicle_number, $driver_name, $first_weight, $second_weight, $net_weight, $total_amount, $notes) {
        $query = "INSERT INTO " . $this->table_name . " (weigh_number, weigh_date, client_id, vehicle_number, driver_name, first_weight, second_weight, net_weight, total_amount, notes, billed_status) VALUES (:weigh_number, :weigh_date, :client_id, :vehicle_number, :driver_name, :first_weight, :second_weight, :net_weight, :total_amount, :notes, 'Unbilled')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":weigh_number", $weigh_number);
        $stmt->bindParam(":weigh_date", $weigh_date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":vehicle_number", $vehicle_number);
        $stmt->bindParam(":driver_name", $driver_name);
        $stmt->bindParam(":first_weight", $first_weight);
        $stmt->bindParam(":second_weight", $second_weight);
        $stmt->bindParam(":net_weight", $net_weight);
        $stmt->bindParam(":total_amount", $total_amount);
        $stmt->bindParam(":notes", $notes);

        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour un enregistrement de pont bascule.
     * Le statut facturé n'est pas modifié ici, mais via updateBilledStatus.
     */
    public function update($id, $weigh_number, $weigh_date, $client_id, $vehicle_number, $driver_name, $first_weight, $second_weight, $net_weight, $total_amount, $notes) {
        $query = "UPDATE " . $this->table_name . " SET weigh_number = :weigh_number, weigh_date = :weigh_date, client_id = :client_id, vehicle_number = :vehicle_number, driver_name = :driver_name, first_weight = :first_weight, second_weight = :second_weight, net_weight = :net_weight, total_amount = :total_amount, notes = :notes WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":weigh_number", $weigh_number);
        $stmt->bindParam(":weigh_date", $weigh_date);
        $stmt->bindParam(":client_id", $client_id);
        $stmt->bindParam(":vehicle_number", $vehicle_number);
        $stmt->bindParam(":driver_name", $driver_name);
        $stmt->bindParam(":first_weight", $first_weight);
        $stmt->bindParam(":second_weight", $second_weight);
        $stmt->bindParam(":net_weight", $net_weight);
        $stmt->bindParam(":total_amount", $total_amount);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /**
     * Met à jour le statut de facturation d'un enregistrement de pont bascule.
     * @param int $id L'ID de l'enregistrement de pont bascule.
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

    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // Supprimer les lignes de taxe associées
            $query_lines = "DELETE FROM weighbridge_taxes WHERE weighbridge_id = :id";
            $stmt_lines = $this->conn->prepare($query_lines);
            $stmt_lines->bindParam(":id", $id);
            $stmt_lines->execute();

            // Enfin, supprimer le pont bascule lui-même
            $query_weighbridge = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt_weighbridge = $this->conn->prepare($query_weighbridge);
            $stmt_weighbridge->bindParam(":id", $id);
            $stmt_weighbridge->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting weighbridge: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les passages pont bascule qui n'ont pas encore été facturés.
     * @return array Tableau des passages pont bascule non facturés.
     */
    public function getAllUnbilledWeighbridges() {
        $query = "SELECT w.*, c.name AS client_name
                  FROM " . $this->table_name . " w
                  LEFT JOIN clients c ON w.client_id = c.id
                  WHERE w.is_billed = FALSE
                  ORDER BY w.weigh_date DESC"; // Utilisez weigh_date pour le tri
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}