<?php
class ProductModel {
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

    public function getAllProductsWithStock() {
        $query = "SELECT p.*, COALESCE(s.Cantidad, 0) as Cantidad, pr.Nombre as Proveedor_Nombre
                  FROM Producto p 
                  LEFT JOIN Stock s ON p.id_producto = s.id_producto
                  LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
                  ORDER BY p.Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductWithStock($id) {
        $query = "SELECT p.*, COALESCE(s.Cantidad, 0) as Cantidad, pr.Nombre as Proveedor_Nombre
                  FROM Producto p 
                  LEFT JOIN Stock s ON p.id_producto = s.id_producto
                  LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
                  WHERE p.id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductsWithLowStock($threshold = 10) {
        $query = "SELECT p.*, COALESCE(s.Cantidad, 0) as Cantidad, pr.Nombre as Proveedor_Nombre
                  FROM Producto p 
                  LEFT JOIN Stock s ON p.id_producto = s.id_producto
                  LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
                  WHERE COALESCE(s.Cantidad, 0) <= p.Umbral_Bajo
                  ORDER BY s.Cantidad ASC, p.Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProduct($nombre, $precio, $unidad, $id_proveedor = null, $umbral_bajo = 15, $umbral_critico = 5) {
        $query = "INSERT INTO Producto (Nombre, Precio, Unidad, id_proveedor, Umbral_Bajo, Umbral_Critico) VALUES (:nombre, :precio, :unidad, :id_proveedor, :umbral_bajo, :umbral_critico)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':id_proveedor', $id_proveedor);
        $stmt->bindParam(':umbral_bajo', $umbral_bajo);
        $stmt->bindParam(':umbral_critico', $umbral_critico);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function updateProduct($id, $nombre, $precio, $unidad, $id_proveedor = null, $umbral_bajo = 15, $umbral_critico = 5) {
        $query = "UPDATE Producto SET Nombre = :nombre, Precio = :precio, Unidad = :unidad, id_proveedor = :id_proveedor, Umbral_Bajo = :umbral_bajo, Umbral_Critico = :umbral_critico
                  WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':id_proveedor', $id_proveedor);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':umbral_bajo', $umbral_bajo);
        $stmt->bindParam(':umbral_critico', $umbral_critico);
        $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM Producto WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function searchProducts($searchTerm) {
        $query = "SELECT p.*, COALESCE(s.Cantidad, 0) as Cantidad, pr.Nombre as Proveedor_Nombre
                  FROM Producto p 
                  LEFT JOIN Stock s ON p.id_producto = s.id_producto
                  LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
                  WHERE p.Nombre LIKE :search OR p.id_producto LIKE :search
                  ORDER BY p.Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>