<?php
require_once 'models/DebtModel.php';
require_once 'models/SaleModel.php';
require_once 'controllers/AuthController.php';

class DebtController {
    private $debtModel;
    private $saleModel;

    public function __construct() {
        AuthController::checkAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $this->debtModel = new DebtModel($db);
        $this->saleModel = new SaleModel($db);
    }

    public function index() {
        $this->list();
    }

    public function list() {
        $debts = $this->debtModel->getAllDebts();
        require_once 'views/deudores/list.php';
    }

    public function addPayment() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=deudores&method=list');
            exit;
        }

        $debtId = $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentAmount = $_POST['payment_amount'];
            $paymentMethod = $_POST['payment_method'];

            try {
                $this->debtModel->addPayment($debtId, $paymentAmount, $paymentMethod);
                $_SESSION['success'] = "Pago registrado correctamente";
                header('Location: index.php?action=deudores&method=list');
                exit;
            } catch (Exception $e) {
                $error = "Error al registrar el pago: " . $e->getMessage();
            }
        }

        // Obtener información de la deuda
        $debt = $this->getDebtWithDetails($debtId);
        $payments = $this->debtModel->getDebtPayments($debtId);
        
        require_once 'views/deudores/add_payment.php';
    }

    public function details() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=deudores&method=list');
            exit;
        }

        $debtId = $_GET['id'];
        $debt = $this->getDebtWithDetails($debtId);
        $payments = $this->debtModel->getDebtPayments($debtId);
        
        require_once 'views/deudores/details.php';
    }

    private function getDebtWithDetails($debtId) {
        $query = "SELECT d.*, v.Fecha_Emision, v.id_venta, c.Nombre, c.Apellido, c.Cedula_Rif, c.Telefono, c.Direccion 
                  FROM Deuda d 
                  JOIN Venta v ON d.id_venta = v.id_venta 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif 
                  WHERE d.id_deuda = :debt_id";
        
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':debt_id', $debtId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>