<?php
class DebtModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createDebt($saleId, $totalAmount, $paidAmount) {
        $debtAmount = $totalAmount - $paidAmount;
        
        if ($debtAmount > 0) {
            $query = "INSERT INTO Deuda (id_venta, Monto_Total, Monto_Pagado, Monto_Deuda, Estado) 
                      VALUES (:sale_id, :total_amount, :paid_amount, :debt_amount, 'Pendiente')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sale_id', $saleId);
            $stmt->bindParam(':total_amount', $totalAmount);
            $stmt->bindParam(':paid_amount', $paidAmount);
            $stmt->bindParam(':debt_amount', $debtAmount);
            return $stmt->execute();
        }
        return false;
    }

    public function getAllDebts() {
        $query = "SELECT d.*, v.Fecha_Emision, c.Nombre, c.Apellido, c.Cedula_Rif, c.Telefono 
                  FROM Deuda d 
                  JOIN Venta v ON d.id_venta = v.id_venta 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif 
                  WHERE d.Estado = 'Pendiente'
                  ORDER BY v.Fecha_Emision DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDebtBySale($saleId) {
        $query = "SELECT * FROM Deuda WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addPayment($debtId, $paymentAmount, $paymentMethod) {
        $this->conn->beginTransaction();
        
        try {
            // Obtener la deuda actual
            $query = "SELECT * FROM Deuda WHERE id_deuda = :debt_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':debt_id', $debtId);
            $stmt->execute();
            $debt = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$debt) {
                throw new Exception("Deuda no encontrada");
            }
            
            // Registrar el pago
            $query = "INSERT INTO PagoDeuda (id_deuda, Monto, Metodo_Pago) 
                      VALUES (:debt_id, :amount, :method)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':debt_id', $debtId);
            $stmt->bindParam(':amount', $paymentAmount);
            $stmt->bindParam(':method', $paymentMethod);
            $stmt->execute();
            
            // Actualizar la deuda
            $newPaidAmount = $debt['Monto_Pagado'] + $paymentAmount;
            $newDebtAmount = $debt['Monto_Total'] - $newPaidAmount;
            $newStatus = ($newDebtAmount <= 0) ? 'Pagado' : 'Pendiente';
            
            $query = "UPDATE Deuda SET 
                      Monto_Pagado = :paid_amount, 
                      Monto_Deuda = :debt_amount, 
                      Estado = :status,
                      Fecha_Ultimo_Pago = NOW()
                      WHERE id_deuda = :debt_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':paid_amount', $newPaidAmount);
            $stmt->bindParam(':debt_amount', $newDebtAmount);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':debt_id', $debtId);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getDebtPayments($debtId) {
        $query = "SELECT * FROM PagoDeuda WHERE id_deuda = :debt_id ORDER BY Fecha_Pago DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':debt_id', $debtId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBySale($saleId) {
        // Primero eliminar los pagos de deuda
        $query = "DELETE pd FROM PagoDeuda pd 
                  JOIN Deuda d ON pd.id_deuda = d.id_deuda 
                  WHERE d.id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        
        // Luego eliminar la deuda
        $query = "DELETE FROM Deuda WHERE id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        return $stmt->execute();
    }
}
?>