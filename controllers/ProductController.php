<?php
require_once 'models/ProductModel.php';
require_once 'models/StockModel.php';
require_once 'models/ProveedorModel.php';

class ProductController {
    private $productModel;
    private $stockModel;
    private $proveedorModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new ProductModel($db);
        $this->stockModel = new StockModel($db);
        $this->proveedorModel = new ProveedorModel($db);
    }

    public function index() {
        $this->list();
    }

    public function list() {
        $products = $this->productModel->getAllProductsWithStock();
        require_once 'views/productos/list.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $unidad = $_POST['unidad'];
            $cantidad = $_POST['cantidad'];
            $id_proveedor = !empty($_POST['id_proveedor']) ? $_POST['id_proveedor'] : null;
            $umbral_bajo = isset($_POST['umbral_bajo']) ? (int)$_POST['umbral_bajo'] : 15;
            $umbral_critico = isset($_POST['umbral_critico']) ? (int)$_POST['umbral_critico'] : 5;

            // Iniciar transacción
            $this->productModel->beginTransaction();
            
            try {
                $productId = $this->productModel->createProduct($nombre, $precio, $unidad, $id_proveedor, $umbral_bajo, $umbral_critico);
                $this->stockModel->createStock($productId, $cantidad);
                
                $this->productModel->commit();
                $_SESSION['success'] = "Producto agregado exitosamente";
                header('Location: index.php?action=productos&method=list');
                exit;
            } catch (Exception $e) {
                $this->productModel->rollBack();
                $error = "Error al crear el producto: " . $e->getMessage();
                $proveedores = $this->proveedorModel->getAllProveedores();
                require_once 'views/productos/add.php';
            }
        } else {
            $proveedores = $this->proveedorModel->getAllProveedores();
            require_once 'views/productos/add.php';
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=productos&method=list');
            exit;
        }

        $id = $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $unidad = $_POST['unidad'];
            $cantidad = $_POST['cantidad'];
            $umbral_bajo = isset($_POST['umbral_bajo']) ? (int)$_POST['umbral_bajo'] : 15;
            $umbral_critico = isset($_POST['umbral_critico']) ? (int)$_POST['umbral_critico'] : 5;
            $id_proveedor = !empty($_POST['id_proveedor']) ? $_POST['id_proveedor'] : null;

            $this->productModel->beginTransaction();
            
            try {
                $this->productModel->updateProduct($id, $nombre, $precio, $unidad, $id_proveedor, $umbral_bajo, $umbral_critico);
                $this->stockModel->updateStock($id, $cantidad);
                
                $this->productModel->commit();
                $_SESSION['success'] = "Producto actualizado exitosamente";
                header('Location: index.php?action=productos&method=list');
                exit;
            } catch (Exception $e) {
                $this->productModel->rollBack();
                $error = "Error al actualizar el producto: " . $e->getMessage();
                $product = $this->productModel->getProductWithStock($id);
                $proveedores = $this->proveedorModel->getAllProveedores();
                require_once 'views/productos/edit.php';
            }
        } else {
            $product = $this->productModel->getProductWithStock($id);
            $proveedores = $this->proveedorModel->getAllProveedores();
            require_once 'views/productos/edit.php';
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->productModel->beginTransaction();
            
            try {
                $this->stockModel->deleteStockByProduct($id);
                $this->productModel->deleteProduct($id);
                
                $this->productModel->commit();
                $_SESSION['success'] = "Producto eliminado exitosamente";
            } catch (Exception $e) {
                $this->productModel->rollBack();
                if (str_contains($e->getMessage(), "1451")) {
                    $_SESSION['error'] = "Error al eliminar el producto: no se puede eliminar un producto que ya ha sido vendido.";
                } else {
                    $_SESSION['error'] = "Error al eliminar el producto: " . $e->getMessage();
                }
            }
        }
        
        header('Location: index.php?action=productos&method=list');
        exit;
    }
}
?>