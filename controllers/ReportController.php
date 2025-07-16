<?php
require_once 'models/SaleModel.php';
require_once 'models/SaleDetailModel.php';
require_once 'models/ProductModel.php';
require_once 'models/StockModel.php';
require_once 'controllers/AuthController.php';

class ReportController {
    private $saleModel;
    private $saleDetailModel;
    private $productModel;
    private $stockModel;

    public function __construct() {
        // Verificar que el usuario esté autenticado y sea administrador
        AuthController::checkAuth();
        $this->checkAdminAccess();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $this->saleModel = new SaleModel($db);
        $this->saleDetailModel = new SaleDetailModel($db);
        $this->productModel = new ProductModel($db);
        $this->stockModel = new StockModel($db);
    }

    private function checkAdminAccess() {
        // Verificar que el usuario tenga rol de administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            // Mostrar página de acceso denegado
            $this->showAccessDenied();
            exit;
        }
    }

    private function showAccessDenied() {
        require_once 'views/errors/access_denied.php';
    }

    public function salesByDate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            
            // Verificar si se solicita PDF
            if (isset($_POST['generate_pdf'])) {
                header('Location: index.php?action=invoice&method=salesReport&start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
                exit;
            }
            
            $sales = $this->saleModel->getSalesByDateRange($startDate, $endDate);
            $total = array_sum(array_column($sales, 'Total'));
            
            require_once 'views/reports/sales_by_date.php';
        } else {
            require_once 'views/reports/sales_by_date.php';
        }
    }

    public function salesMonth() {
        // Obtener el mes y año actual o el especificado
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // Verificar si se solicita PDF
        if (isset($_GET['generate_pdf'])) {
            header('Location: index.php?action=invoice&method=salesMonthReport&month=' . $month . '&year=' . $year);
            exit;
        }
        
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
        
        // Calcular estadísticas
        $total = array_sum(array_column($sales, 'Total'));
        $totalSales = count($sales);
        $averageSale = $totalSales > 0 ? $total / $totalSales : 0;
        
        // Obtener ventas por día del mes
        $dailySales = [];
        foreach ($sales as $sale) {
            $day = date('j', strtotime($sale['Fecha_Emision']));
            if (!isset($dailySales[$day])) {
                $dailySales[$day] = ['count' => 0, 'total' => 0];
            }
            $dailySales[$day]['count']++;
            $dailySales[$day]['total'] += $sale['Total'];
        }
        
        // Array de nombres de meses para la vista
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        // Marcar que se han procesado los datos
        $dataProcessed = true;
        
        require_once 'views/reports/sales_month.php';
    }

    public function topProduct() {
        // Obtener el período especificado o usar el mes actual
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        
        // Verificar si se solicita PDF
        if (isset($_GET['generate_pdf'])) {
            header('Location: index.php?action=invoice&method=topProductReport&month=' . $month . '&year=' . $year . '&period=' . $period);
            exit;
        }
        
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
            // Período anual
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
        
        // Obtener top 5 productos para comparación
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
        
        // Array de nombres de meses para la vista
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        // Marcar que se han procesado los datos
        $dataProcessed = true;
        
        require_once 'views/reports/top_product.php';
    }

    public function topProducts() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        // Verificar si se solicita PDF
        if (isset($_GET['generate_pdf'])) {
            header('Location: index.php?action=invoice&method=topProductsReport&limit=' . $limit);
            exit;
        }
        
        $products = $this->saleDetailModel->getTopSellingProducts($limit);
        require_once 'views/reports/top_products.php';
    }

    public function lowStock() {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
        
        // Verificar si se solicita PDF
        if (isset($_GET['generate_pdf'])) {
            header('Location: index.php?action=invoice&method=lowStockReport&threshold=' . $threshold);
            exit;
        }
        
        $products = $this->productModel->getProductsWithLowStock($threshold);
        require_once 'views/reports/low_stock.php';
    }
}
?>