<?php
require_once 'models/SaleModel.php';
require_once 'models/SaleDetailModel.php';
require_once 'models/PaymentModel.php';
require_once 'models/PaymentDetailModel.php';
require_once 'models/ExchangeRateModel.php';
require_once 'models/DebtModel.php';
require_once 'models/ClientModel.php';
require_once 'models/ProductModel.php';
require_once 'models/StockModel.php';

class SaleController {
    private $saleModel;
    private $saleDetailModel;
    private $paymentModel;
    private $paymentDetailModel;
    private $exchangeRateModel;
    private $debtModel;
    private $clientModel;
    private $productModel;
    private $stockModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        
        $this->saleModel = new SaleModel($db);
        $this->saleDetailModel = new SaleDetailModel($db);
        $this->paymentModel = new PaymentModel($db);
        $this->paymentDetailModel = new PaymentDetailModel($db);
        $this->exchangeRateModel = new ExchangeRateModel($db);
        $this->debtModel = new DebtModel($db);
        $this->clientModel = new ClientModel($db);
        $this->productModel = new ProductModel($db);
        $this->stockModel = new StockModel($db);
        
        // Verificar y crear la columna Monto_Divisas si no existe
        $this->exchangeRateModel->createDivisasColumnIfNotExists();
    }

    public function new() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_to_cart'])) {
                $this->addToCart();
            } elseif (isset($_POST['remove_item'])) {
                $this->removeFromCart();
            } elseif (isset($_POST['finalize_sale'])) {
                $this->finalizeSale();
            }
        } else {
            $this->showNewSaleForm();
        }
    }

    private function addToCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        
        $product = $this->productModel->getProductWithStock($productId);
        
        if ($product && $quantity > 0 && $quantity <= $product['Cantidad']) {
            $item = [
                'id_producto' => $product['id_producto'],
                'nombre' => $product['Nombre'],
                'precio' => $product['Precio'],
                'cantidad' => $quantity,
                'subtotal' => $product['Precio'] * $quantity
            ];
            
            $found = false;
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['id_producto'] == $productId) {
                    $cartItem['cantidad'] += $quantity;
                    $cartItem['subtotal'] = $cartItem['precio'] * $cartItem['cantidad'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = $item;
            }
        }
        
        $this->showNewSaleForm();
    }

    private function removeFromCart() {
        if (isset($_SESSION['cart'])) {
            $index = $_POST['item_index'];
            if (isset($_SESSION['cart'][$index])) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
        }
        $this->showNewSaleForm();
    }

    private function finalizeSale() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $this->showNewSaleForm();
            return;
        }

        $cedulaRif = $_POST['cedula_rif'];
        
        // Manejar ventas directas y cliente genérico
        if (empty($cedulaRif)) {
            $cedulaRif = 'VENTA_DIRECTA';
        } elseif ($cedulaRif === 'CLIENTE_GENERICO') {
            // Verificar si existe el cliente genérico, si no, crearlo
            $genericClient = $this->clientModel->getClient('CLIENTE_GENERICO');
            if (!$genericClient) {
                $this->clientModel->createClient('CLIENTE_GENERICO', 'Cliente', 'Genérico', '', '', '');
            }
        }

        // Para ventas directas, crear un cliente temporal
        if ($cedulaRif === 'VENTA_DIRECTA') {
            $directClient = $this->clientModel->getClient('VENTA_DIRECTA');
            if (!$directClient) {
                $this->clientModel->createClient('VENTA_DIRECTA', 'Venta', 'Directa', '', '', '');
            }
        }

        $total = array_sum(array_column($_SESSION['cart'], 'subtotal'));
        $paymentMethod = $_POST['payment_method'];
        $amountPaid = $_POST['amount_paid'];
        $exchangeRate = isset($_POST['exchange_rate']) ? $_POST['exchange_rate'] : null;
        $exchangeTime = isset($_POST['exchange_time']) ? $_POST['exchange_time'] : null;
        
        // Datos específicos para divisas
        $divisasAmount = isset($_POST['divisas_amount']) ? $_POST['divisas_amount'] : null;

        $this->saleModel->beginTransaction();
        
        try {
            $saleId = $this->saleModel->createSale($cedulaRif, $total);
            
            foreach ($_SESSION['cart'] as $item) {
                $this->saleDetailModel->createSaleDetail($saleId, $item['id_producto'], $item['cantidad'], $item['precio']);
                
                $currentStock = $this->stockModel->getStockByProduct($item['id_producto']);
                $newQuantity = $currentStock['Cantidad'] - $item['cantidad'];
                $this->stockModel->updateStock($item['id_producto'], $newQuantity);
            }
            
            $paymentId = $this->paymentModel->createPayment($saleId, $amountPaid);
            $paymentDetailId = $this->paymentDetailModel->createPaymentDetail(
                $paymentId, 
                $amountPaid, 
                $paymentMethod
            );
            
            // Guardar información de divisas si aplica
            if ($exchangeRate && $exchangeTime) {
                $this->exchangeRateModel->createExchangeRate(
                    $paymentDetailId, 
                    $exchangeRate, 
                    $exchangeTime,
                    $divisasAmount
                );
            }
            
            // Verificar si hay deuda pendiente
            if ($amountPaid < $total) {
                $this->debtModel->createDebt($saleId, $total, $amountPaid);
            }
            
            $this->saleModel->commit();
            unset($_SESSION['cart']);
            
            // Calcular vuelto
            $change = $amountPaid - $total;
            
            $_SESSION['sale_info'] = [
                'sale_id' => $saleId,
                'payment_method' => $paymentMethod,
                'divisas_amount' => $divisasAmount,
                'exchange_rate' => $exchangeRate,
                'change' => $change,
                'total' => $total,
                'amount_paid' => $amountPaid
            ];
            
            header('Location: index.php?action=ventas&method=receipt&id=' . $saleId);
            exit;
        } catch (Exception $e) {
            $this->saleModel->rollBack();
            $error = "Error al procesar la venta: " . $e->getMessage();
            $this->showNewSaleForm($error);
        }
    }

    public function receipt() {
        if (!isset($_GET['id']) || !isset($_SESSION['sale_info'])) {
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $saleId = $_GET['id'];
        $saleInfo = $_SESSION['sale_info'];
        
        // Limpiar la información de la sesión
        unset($_SESSION['sale_info']);
        
        require_once 'views/ventas/receipt.php';
    }

    private function showNewSaleForm($error = null) {
        $clients = $this->clientModel->getAllClients();
        $products = $this->productModel->getAllProductsWithStock();
        
        require_once 'views/ventas/new.php';
    }

    public function list() {
        // Obtener ventas con información de deuda
        $query = "SELECT v.*, c.Nombre, c.Apellido, 
                         CASE 
                             WHEN d.Estado = 'Pendiente' THEN 'No Pagado'
                             WHEN d.Estado = 'Pagado' THEN 'Pagado'
                             WHEN d.id_deuda IS NULL THEN 'Pagado'
                             ELSE 'Pagado'
                         END as Estado_Pago
                  FROM Venta v 
                  JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif
                  LEFT JOIN Deuda d ON v.id_venta = d.id_venta
                  ORDER BY v.Fecha_Emision DESC";
        
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once 'views/ventas/list.php';
    }

    public function details() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $saleId = $_GET['id'];
        $sale = $this->saleModel->getSaleWithClient($saleId);
        $saleDetails = $this->saleDetailModel->getDetailsBySale($saleId);
        $payments = $this->paymentModel->getPaymentsBySale($saleId);
        
        // Obtener información de deuda si existe
        $debt = $this->debtModel->getDebtBySale($saleId);
        
        $paymentDetails = [];
        foreach ($payments as $payment) {
            $paymentDetails[$payment['id_pago_venta']] = $this->paymentDetailModel->getDetailsByPayment($payment['id_pago_venta']);
        }
        
        require_once 'views/ventas/details.php';
    }

    public function delete() {
        // Verificar que el usuario sea administrador
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            $_SESSION['error'] = "Solo los administradores pueden eliminar ventas";
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        if (!isset($_GET['id'])) {
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $saleId = $_GET['id'];
        
        // Verificar que la venta existe
        $sale = $this->saleModel->getSaleWithClient($saleId);
        if (!$sale) {
            $_SESSION['error'] = "La venta no existe";
            header('Location: index.php?action=ventas&method=list');
            exit;
        }

        $this->saleModel->beginTransaction();
        
        try {
            // Obtener detalles de la venta para restaurar el stock
            $saleDetails = $this->saleDetailModel->getDetailsBySale($saleId);
            
            // Restaurar stock de productos
            foreach ($saleDetails as $detail) {
                $currentStock = $this->stockModel->getStockByProduct($detail['id_producto']);
                $newQuantity = $currentStock['Cantidad'] + $detail['Cantidad'];
                $this->stockModel->updateStock($detail['id_producto'], $newQuantity);
            }
            
            // Eliminar en orden correcto para respetar las claves foráneas
            
            // 1. Eliminar deudas y pagos de deuda
            $this->debtModel->deleteBySale($saleId);
            
            // 2. Eliminar tasas de cambio
            $this->exchangeRateModel->deleteByPaymentDetails($saleId);
            
            // 3. Eliminar detalles de pago
            $this->paymentDetailModel->deleteBySale($saleId);
            
            // 4. Eliminar pagos
            $this->paymentModel->deleteBySale($saleId);
            
            // 5. Eliminar detalles de venta
            $this->saleDetailModel->deleteBySale($saleId);
            
            // 6. Eliminar la venta
            $this->saleModel->deleteSale($saleId);
            
            $this->saleModel->commit();
            $_SESSION['success'] = "Venta eliminada correctamente. El stock ha sido restaurado.";
            
        } catch (Exception $e) {
            $this->saleModel->rollBack();
            $_SESSION['error'] = "Error al eliminar la venta: " . $e->getMessage();
        }
        
        header('Location: index.php?action=ventas&method=list');
        exit;
    }
}
?>