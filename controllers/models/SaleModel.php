<?php
class SaleModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollBack() {
        $this->conn->rollBack();
    }

    public function createSale($cedulaRif, $total) {
        $query = "INSERT INTO Venta (Cedula_Rif, Total, Estado, No_Control) 
                  VALUES (:cedula_rif, :total, 'Completada', '')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula_rif', $cedulaRif);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getAllSalesWithClient() {
        $query = "SELECT v.*, c.Nombre, c.Apellido 
                  FROM Venta v 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif
                  ORDER BY v.Fecha_Emision DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSaleWithClient($saleId) {
        $query = "SELECT v.*, c.Nombre, c.Apellido, c.Telefono, c.Direccion, c.Correo 
                  FROM Venta v 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif
                  WHERE v.id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSalesByDateRange($startDate, $endDate) {
        $query = "SELECT v.*, c.Nombre, c.Apellido 
                  FROM Venta v 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif
                  WHERE DATE(v.Fecha_Emision) BETWEEN :start_date AND :end_date
                  ORDER BY v.Fecha_Emision DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSale($saleId) {
        $query = "DELETE FROM Venta WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        return $stmt->execute();
    }
}
?>