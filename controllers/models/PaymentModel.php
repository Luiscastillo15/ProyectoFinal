<?php
class PaymentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPayment($saleId, $amount) {
        $query = "INSERT INTO Pago (id_venta, Monto) VALUES (:sale_id, :amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getPaymentsBySale($saleId) {
        $query = "SELECT * FROM Pago WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBySale($saleId) {
        $query = "DELETE FROM Pago WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        return $stmt->execute();
    }
}
?>