<?php
class SaleDetailModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createSaleDetail($saleId, $productId, $quantity, $unitPrice) {
        $query = "INSERT INTO DetalleVenta (id_venta, id_producto, Cantidad, Precio_Unitario) 
                  VALUES (:sale_id, :product_id, :quantity, :unit_price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unit_price', $unitPrice);
        $stmt->execute();
    }

    public function getDetailsBySale($saleId) {
        $query = "SELECT dv.*, p.Nombre as producto_nombre 
                  FROM DetalleVenta dv 
                  JOIN Producto p ON dv.id_producto = p.id_producto
                  WHERE dv.id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($limit = 10) {
        $query = "SELECT p.Nombre as producto_nombre, 
                         SUM(dv.Cantidad) as total_cantidad,
                         SUM(dv.Cantidad * dv.Precio_Unitario) as total_venta
                  FROM DetalleVenta dv 
                  JOIN Producto p ON dv.id_producto = p.id_producto
                  GROUP BY dv.id_producto, p.Nombre
                  ORDER BY total_cantidad DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBySale($saleId) {
        $query = "DELETE FROM DetalleVenta WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        return $stmt->execute();
    }
}
?>