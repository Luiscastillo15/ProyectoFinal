<?php
// models/UserModel.php

class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserById($id_usuario) {
        $query = "SELECT * FROM Usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($cedula) {
        $query = "UPDATE Usuario SET Fecha_Ultimo_Registro = NOW() WHERE Cedula = :cedula";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        return $stmt->execute();
    }

    public function registerLogout($cedula) {
        $query = "UPDATE Usuario SET Fecha_Ultimo_Registro = NOW() WHERE Cedula = :cedula";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        return $stmt->execute();
    }
}
?>