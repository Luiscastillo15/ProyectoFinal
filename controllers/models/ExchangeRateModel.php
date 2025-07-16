<?php
class ExchangeRateModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createExchangeRate($paymentDetailId, $rate, $time, $divisasAmount = null) {
        // Verificar si la columna Monto_Divisas existe antes de usarla
        try {
            $query = "INSERT INTO TasaCambio (id_detalle_pago, Precio, Hora, Monto_Divisas) 
                      VALUES (:payment_detail_id, :rate, :time, :divisas_amount)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':payment_detail_id', $paymentDetailId);
            $stmt->bindParam(':rate', $rate);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':divisas_amount', $divisasAmount);
            $stmt->execute();
        } catch (PDOException $e) {
            // Si la columna no existe, usar la consulta sin Monto_Divisas
            if (strpos($e->getMessage(), 'Monto_Divisas') !== false) {
                $query = "INSERT INTO TasaCambio (id_detalle_pago, Precio, Hora) 
                          VALUES (:payment_detail_id, :rate, :time)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':payment_detail_id', $paymentDetailId);
                $stmt->bindParam(':rate', $rate);
                $stmt->bindParam(':time', $time);
                $stmt->execute();
            } else {
                throw $e;
            }
        }
    }

    public function deleteByPaymentDetails($saleId) {
        $query = "DELETE tc FROM TasaCambio tc 
                  JOIN DetallePago dp ON tc.id_detalle_pago = dp.id_detalle_pago 
                  JOIN Pago p ON dp.id_pago_venta = p.id_pago_venta 
                  WHERE p.id_venta = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        return $stmt->execute();
    }

    public function getExchangeRateByPaymentDetail($paymentDetailId) {
        $query = "SELECT * FROM TasaCambio WHERE id_detalle_pago = :payment_detail_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_detail_id', $paymentDetailId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function checkDivisasColumnExists() {
        try {
            $query = "SHOW COLUMNS FROM TasaCambio LIKE 'Monto_Divisas'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }


    public function createDivisasColumnIfNotExists() {
        if (!$this->checkDivisasColumnExists()) {
            try {
                $query = "ALTER TABLE TasaCambio 
                          ADD COLUMN Monto_Divisas DECIMAL(10,2) NULL 
                          COMMENT 'Monto en divisas (USD) pagado por el cliente'";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return true;
            } catch (PDOException $e) {
                error_log("Error creando columna Monto_Divisas: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }
}
?>