<?php
class RolModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todos los roles
     */
    public function getAllRoles() {
        $query = "SELECT * FROM Rol ORDER BY Nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un rol por ID
     */
    public function getRoleById($id_rol) {
        $query = "SELECT * FROM Rol WHERE id_rol = :id_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo rol
     */
    public function createRole($nombre) {
        $query = "INSERT INTO Rol (Nombre) VALUES (:nombre)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        return $stmt->execute();
    }

    /**
     * Actualizar un rol existente
     */
    public function updateRole($id_rol, $nombre) {
        $query = "UPDATE Rol SET Nombre = :nombre WHERE id_rol = :id_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id_rol', $id_rol);
        return $stmt->execute();
    }

    /**
     * Eliminar un rol
     */
    public function deleteRole($id_rol) {
        $query = "DELETE FROM Rol WHERE id_rol = :id_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol);
        return $stmt->execute();
    }

    /**
     * Verificar si un rol tiene usuarios asociados
     */
    public function hasUsers($id_rol) {
        $query = "SELECT COUNT(*) as total FROM Usuario WHERE id_rol = :id_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
?>