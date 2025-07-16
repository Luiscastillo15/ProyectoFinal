<?php
require_once 'models/SaleModel.php';
require_once 'models/SaleDetailModel.php';
require_once 'models/PaymentModel.php';
require_once 'models/PaymentDetailModel.php';
require_once 'models/ExchangeRateModel.php';
require_once 'models/DebtModel.php';
require_once 'models/ProductModel.php';
require_once 'controllers/AuthController.php';

class InvoiceController {
    private $saleModel;
    private $saleDetailModel;
    private $paymentModel;
    private $paymentDetailModel;
    private $exchangeRateModel;
    private $debtModel;
    private $productModel;

    public function __construct() {
        AuthController::checkAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $this->saleModel = new SaleModel($db);
        $this->saleDetailModel = new SaleDetailModel($db);
        $this->paymentModel = new PaymentModel($db);
        $this->paymentDetailModel = new PaymentDetailModel($db);
        $this->exchangeRateModel = new ExchangeRateModel($db);
        $this->debtModel = new DebtModel($db);
        $this->productModel = new ProductModel($db);
    }

    public function generate() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $saleId = $_GET['id'];
        $sale = $this->saleModel->getSaleWithClient($saleId);
        
        if (!$sale) {
            $_SESSION['error'] = "Venta no encontrada";
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $saleDetails = $this->saleDetailModel->getDetailsBySale($saleId);
        $payments = $this->paymentModel->getPaymentsBySale($saleId);
        $debt = $this->debtModel->getDebtBySale($saleId);
        
        $paymentDetails = [];
        foreach ($payments as $payment) {
            $paymentDetails[$payment['id_pago_venta']] = $this->paymentDetailModel->getDetailsByPayment($payment['id_pago_venta']);
        }
        
        require_once 'views/invoices/printable.php';
    }

    public function salesReport() {
        // Verificar que sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            require_once 'views/errors/access_denied.php';
            exit;
        }

        if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
            header('Location: index.php?action=reportes&method=salesByDate');
            exit;
        }

        $startDate = $_GET['start_date'];
        $endDate = $_GET['end_date'];
        
        $sales = $this->saleModel->getSalesByDateRange($startDate, $endDate);
        $total = array_sum(array_column($sales, 'Total'));
        
        require_once 'views/invoices/sales_report.php';
    }

    public function salesMonthReport() {
        // Verificar que sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            require_once 'views/errors/access_denied.php';
            exit;
        }

        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // Obtener ventas del mes
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT v.*, c.Nombre, c.Apellido 
                  FROM Venta v 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif
                  WHERE MONTH(v.Fecha_Emision) = :month 
                  AND YEAR(v.Fecha_Emision) = :year
                  ORDER BY v.Fecha_Emision DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = array_sum(array_column($sales, 'Total'));
        $totalSales = count($sales);
        
        // Array de nombres de meses
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        require_once 'views/invoices/sales_month_report.php';
    }

    public function topProductReport() {
        // Verificar que sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            require_once 'views/errors/access_denied.php';
            exit;
        }

        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Construir consulta según el período
        if ($period === 'month') {
            $query = "SELECT p.Nombre as producto_nombre, 
                             SUM(dv.Cantidad) as total_cantidad,
                             SUM(dv.Cantidad * dv.Precio_Unitario) as total_venta,
                             AVG(dv.Precio_Unitario) as precio_promedio,
                             COUNT(DISTINCT v.id_venta) as num_ventas
                      FROM DetalleVenta dv 
                      JOIN Producto p ON dv.id_producto = p.id_producto
                      JOIN Venta v ON dv.id_venta = v.id_venta
                      WHERE MONTH(v.Fecha_Emision) = :month 
                      AND YEAR(v.Fecha_Emision) = :year
                      GROUP BY dv.id_producto, p.Nombre
                      ORDER BY total_cantidad DESC
                      LIMIT 1";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
        } else {
            $query = "SELECT p.Nombre as producto_nombre, 
                             SUM(dv.Cantidad) as total_cantidad,
                             SUM(dv.Cantidad * dv.Precio_Unitario) as total_venta,
                             AVG(dv.Precio_Unitario) as precio_promedio,
                             COUNT(DISTINCT v.id_venta) as num_ventas
                      FROM DetalleVenta dv 
                      JOIN Producto p ON dv.id_producto = p.id_producto
                      JOIN Venta v ON dv.id_venta = v.id_venta
                      WHERE YEAR(v.Fecha_Emision) = :year
                      GROUP BY dv.id_producto, p.Nombre
                      ORDER BY total_cantidad DESC
                      LIMIT 1";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year);
        }
        
        $stmt->execute();
        $topProduct = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener top 5 productos
        if ($period === 'month') {
            $query = "SELECT p.Nombre as producto_nombre, 
                             SUM(dv.Cantidad) as total_cantidad,
                             SUM(dv.Cantidad * dv.Precio_Unitario) as total_venta
                      FROM DetalleVenta dv 
                      JOIN Producto p ON dv.id_producto = p.id_producto
                      JOIN Venta v ON dv.id_venta = v.id_venta
                      WHERE MONTH(v.Fecha_Emision) = :month 
                      AND YEAR(v.Fecha_Emision) = :year
                      GROUP BY dv.id_producto, p.Nombre
                      ORDER BY total_cantidad DESC
                      LIMIT 5";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
        } else {
            $query = "SELECT p.Nombre as producto_nombre, 
                             SUM(dv.Cantidad) as total_cantidad,
                             SUM(dv.Cantidad * dv.Precio_Unitario) as total_venta
                      FROM DetalleVenta dv 
                      JOIN Producto p ON dv.id_producto = p.id_producto
                      JOIN Venta v ON dv.id_venta = v.id_venta
                      WHERE YEAR(v.Fecha_Emision) = :year
                      GROUP BY dv.id_producto, p.Nombre
                      ORDER BY total_cantidad DESC
                      LIMIT 5";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year);
        }
        
        $stmt->execute();
        $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Array de nombres de meses
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        require_once 'views/invoices/top_product_report.php';
    }

    public function topProductsReport() {
        // Verificar que sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            require_once 'views/errors/access_denied.php';
            exit;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $products = $this->saleDetailModel->getTopSellingProducts($limit);
        
        require_once 'views/invoices/top_products_report.php';
    }

    public function lowStockReport() {
        // Verificar que sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            require_once 'views/errors/access_denied.php';
            exit;
        }

        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
        $products = $this->productModel->getProductsWithLowStock($threshold);
        
        require_once 'views/invoices/low_stock_report.php';
    }
}
?>