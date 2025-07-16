<?php
class ProveedorModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProveedores() {
        $query = "SELECT * FROM Proveedor ORDER BY Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProveedor($id) {
        $query = "SELECT * FROM Proveedor WHERE id_proveedor = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProveedorByRif($rif) {
        $query = "SELECT * FROM Proveedor WHERE RIF = :rif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rif', $rif);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProveedor($rif, $nombre, $telefono, $direccion, $correo, $contacto, $tipo_producto) {
        $query = "INSERT INTO Proveedor (RIF, Nombre, Telefono, Direccion, Correo, Contacto, Tipo_Producto) 
                  VALUES (:rif, :nombre, :telefono, :direccion, :correo, :contacto, :tipo_producto)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rif', $rif);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contacto', $contacto);
        $stmt->bindParam(':tipo_producto', $tipo_producto);
        return $stmt->execute();
    }

    public function updateProveedor($id, $rif, $nombre, $telefono, $direccion, $correo, $contacto, $tipo_producto) {
        $query = "UPDATE Proveedor SET 
                  RIF = :rif,
                  Nombre = :nombre, 
                  Telefono = :telefono, 
                  Direccion = :direccion, 
                  Correo = :correo,
                  Contacto = :contacto,
                  Tipo_Producto = :tipo_producto
                  WHERE id_proveedor = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rif', $rif);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contacto', $contacto);
        $stmt->bindParam(':tipo_producto', $tipo_producto);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteProveedor($id) {
        $query = "DELETE FROM Proveedor WHERE id_proveedor = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function searchProveedores($searchTerm) {
        $query = "SELECT * FROM Proveedor 
                  WHERE Nombre LIKE :search 
                  OR RIF LIKE :search 
                  OR Contacto LIKE :search
                  OR Tipo_Producto LIKE :search
                  ORDER BY Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProveedoresByTipo($tipo) {
        $query = "SELECT * FROM Proveedor WHERE Tipo_Producto LIKE :tipo ORDER BY Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $tipo = "%{$tipo}%";
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkRifExists($rif, $excludeId = null) {
        if ($excludeId) {
            $query = "SELECT COUNT(*) as count FROM Proveedor WHERE RIF = :rif AND id_proveedor != :exclude_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':rif', $rif);
            $stmt->bindParam(':exclude_id', $excludeId);
        } else {
            $query = "SELECT COUNT(*) as count FROM Proveedor WHERE RIF = :rif";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':rif', $rif);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getProveedorConMasInventario() {
        $query = "SELECT 
                    pr.id_proveedor,
                    pr.Nombre AS nombre_proveedor,
                    SUM(COALESCE(s.Cantidad, 0)) AS total_inventario
                  FROM 
                    Producto p
                  LEFT JOIN 
                    Stock s ON p.id_producto = s.id_producto
                  LEFT JOIN 
                    Proveedor pr ON p.id_proveedor = pr.id_proveedor
                  GROUP BY 
                    pr.id_proveedor, pr.Nombre
                  ORDER BY 
                    total_inventario DESC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllProveedoresConInventario() {
        $query = "SELECT 
                    pr.id_proveedor,
                    pr.Nombre AS nombre_proveedor,
                    SUM(COALESCE(s.Cantidad, 0)) AS total_inventario
                  FROM 
                    Proveedor pr
                  LEFT JOIN 
                    Producto p ON pr.id_proveedor = p.id_proveedor
                  LEFT JOIN 
                    Stock s ON p.id_producto = s.id_producto
                  GROUP BY 
                    pr.id_proveedor, pr.Nombre
                  ORDER BY 
                    total_inventario DESC, pr.Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>