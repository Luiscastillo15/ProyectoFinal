<?php
class ClientModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllClients() {
        $query = "SELECT * FROM Cliente";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClient($cedula_rif) {
        $query = "SELECT * FROM Cliente WHERE Cedula_Rif = :cedula_rif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula_rif', $cedula_rif);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createClient($cedula_rif, $nombre, $apellido, $telefono, $direccion, $correo) {
        $query = "INSERT INTO Cliente (Cedula_Rif, Nombre, Apellido, Telefono, Direccion, Correo) 
                  VALUES (:cedula_rif, :nombre, :apellido, :telefono, :direccion, :correo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula_rif', $cedula_rif);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':correo', $correo);
        return $stmt->execute();
    }

    public function updateClient($cedula_rif, $nombre, $apellido, $telefono, $direccion, $correo) {
        $query = "UPDATE Cliente SET 
                  Nombre = :nombre, 
                  Apellido = :apellido, 
                  Telefono = :telefono, 
                  Direccion = :direccion, 
                  Correo = :correo 
                  WHERE Cedula_Rif = :cedula_rif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':cedula_rif', $cedula_rif);
        return $stmt->execute();
    }

    public function deleteClient($cedula_rif) {
        $query = "DELETE FROM Cliente WHERE Cedula_Rif = :cedula_rif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula_rif', $cedula_rif);
        return $stmt->execute();
    }
}
?>