<?php
class StockModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createStock($productId, $cantidad) {
        $query = "INSERT INTO Stock (id_producto, Cantidad) VALUES (:productId, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->execute();
    }

    public function updateStock($productId, $cantidad) {
        $query = "UPDATE Stock SET Cantidad = :cantidad, Fecha_actualizacion = NOW() 
                  WHERE id_producto = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->execute();
    }

    public function deleteStockByProduct($productId) {
        $query = "DELETE FROM Stock WHERE id_producto = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
    }

    public function getStockByProduct($productId) {
        $query = "SELECT * FROM Stock WHERE id_producto = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>