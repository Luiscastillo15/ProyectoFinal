<?php
require_once 'models/ProveedorModel.php';
require_once 'models/ProductModel.php';
require_once 'controllers/AuthController.php';

class ProveedorController {
    private $proveedorModel;
    private $productModel;

    public function __construct() {
        // Verificar autenticación
        AuthController::checkAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        $this->proveedorModel = new ProveedorModel($db);
        $this->productModel = new ProductModel($db);
    }

    public function index() {
        $this->list();
    }

    public function list() {
        $proveedores = $this->proveedorModel->getAllProveedores();
        require_once 'views/proveedores/list.php';
    }

    public function lowStock() {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
        
        // Verificar si se solicita PDF
        if (isset($_GET['generate_pdf'])) {
            header('Location: index.php?action=invoice&method=lowStockReport&threshold=' . $threshold);
            exit;
        }
        
        $products = $this->productModel->getProductsWithLowStock($threshold);
        require_once 'views/proveedores/low_stock.php';
    }

    public function add() {
        // Solo administradores pueden agregar proveedores
        $this->checkAdminAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rif = strtoupper(trim($_POST['rif']));
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $correo = trim($_POST['correo']);
            $contacto = trim($_POST['contacto']);
            $tipo_producto = trim($_POST['tipo_producto']);

            // Validaciones
            $errors = $this->validateProveedorData($rif, $nombre, $telefono, $correo);
            
            // Verificar si el RIF ya existe
            if ($this->proveedorModel->checkRifExists($rif)) {
                $errors[] = "Ya existe un proveedor con este RIF";
            }

            if (empty($errors)) {
                if ($this->proveedorModel->createProveedor($rif, $nombre, $telefono, $direccion, $correo, $contacto, $tipo_producto)) {
                    $_SESSION['success'] = "Proveedor registrado exitosamente";
                    header('Location: index.php?action=proveedores&method=list');
                    exit;
                } else {
                    $error = "Error al registrar el proveedor";
                }
            } else {
                $error = implode(", ", $errors);
            }
        }
        
        require_once 'views/proveedores/add.php';
    }

    public function edit() {
        // Solo administradores pueden editar proveedores
        $this->checkAdminAccess();
        
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=proveedores&method=list');
            exit;
        }

        $id = $_GET['id'];
        $proveedor = $this->proveedorModel->getProveedor($id);
        
        if (!$proveedor) {
            $_SESSION['error'] = "Proveedor no encontrado";
            header('Location: index.php?action=proveedores&method=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rif = strtoupper(trim($_POST['rif']));
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $correo = trim($_POST['correo']);
            $contacto = trim($_POST['contacto']);
            $tipo_producto = trim($_POST['tipo_producto']);

            // Validaciones
            $errors = $this->validateProveedorData($rif, $nombre, $telefono, $correo);
            
            // Verificar si el RIF ya existe (excluyendo el actual)
            if ($this->proveedorModel->checkRifExists($rif, $id)) {
                $errors[] = "Ya existe otro proveedor con este RIF";
            }

            if (empty($errors)) {
                if ($this->proveedorModel->updateProveedor($id, $rif, $nombre, $telefono, $direccion, $correo, $contacto, $tipo_producto)) {
                    $_SESSION['success'] = "Proveedor actualizado exitosamente";
                    header('Location: index.php?action=proveedores&method=list');
                    exit;
                } else {
                    $error = "Error al actualizar el proveedor";
                }
            } else {
                $error = implode(", ", $errors);
            }
        }

        require_once 'views/proveedores/edit.php';
    }

    public function delete() {
        // Solo administradores pueden eliminar proveedores
        $this->checkAdminAccess();
        
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            if ($this->proveedorModel->deleteProveedor($id)) {
                $_SESSION['success'] = "Proveedor eliminado exitosamente";
            } else {
                $_SESSION['error'] = "Error al eliminar el proveedor";
            }
        }
        
        header('Location: index.php?action=proveedores&method=list');
        exit;
    }

    public function details() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=proveedores&method=list');
            exit;
        }

        $id = $_GET['id'];
        $proveedor = $this->proveedorModel->getProveedor($id);
        
        if (!$proveedor) {
            $_SESSION['error'] = "Proveedor no encontrado";
            header('Location: index.php?action=proveedores&method=list');
            exit;
        }

        require_once 'views/proveedores/details.php';
    }

    public function getCriticalProviders() {
        header('Content-Type: application/json');
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT p.id_producto, p.Nombre as producto_nombre, s.Cantidad as stock,
                             pr.id_proveedor, pr.RIF, pr.Nombre as proveedor_nombre, 
                             pr.Contacto, pr.Telefono, pr.Correo, pr.Tipo_Producto
                      FROM Producto p 
                      LEFT JOIN Stock s ON p.id_producto = s.id_producto
                      LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
                      WHERE s.Cantidad <= 5 AND pr.id_proveedor IS NOT NULL
                      ORDER BY s.Cantidad ASC, pr.Nombre ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $providers = [];
            foreach ($results as $row) {
                $providerId = $row['id_proveedor'];
                
                if (!isset($providers[$providerId])) {
                    $providers[$providerId] = [
                        'id' => $row['id_proveedor'],
                        'rif' => $row['RIF'],
                        'nombre' => $row['proveedor_nombre'],
                        'contacto' => $row['Contacto'],
                        'telefono' => $row['Telefono'],
                        'correo' => $row['Correo'],
                        'tipo_producto' => $row['Tipo_Producto'],
                        'productos' => []
                    ];
                }
                
                $providers[$providerId]['productos'][] = [
                    'id' => $row['id_producto'],
                    'nombre' => $row['producto_nombre'],
                    'stock' => $row['stock']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'providers' => array_values($providers)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    private function validateProveedorData($rif, $nombre, $telefono, $correo) {
        $errors = [];

        if (empty($rif)) {
            $errors[] = "El RIF es obligatorio";
        } elseif (!preg_match('/^[JGV]\d{9}$/', $rif)) {
            $errors[] = "El RIF debe tener el formato correcto (J/G/V seguido de 9 dígitos)";
        }

        if (empty($nombre)) {
            $errors[] = "El nombre es obligatorio";
        } elseif (strlen($nombre) < 3) {
            $errors[] = "El nombre debe tener al menos 3 caracteres";
        }

        if (!empty($telefono) && !preg_match('/^04\d{9}$/', $telefono)) {
            $errors[] = "El teléfono debe tener 11 dígitos y comenzar con 04";
        }

        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El formato del correo electrónico no es válido";
        }

        return $errors;
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['rol_nombre']) || strtolower($_SESSION['rol_nombre']) !== 'administrador') {
            $_SESSION['error'] = "Solo los administradores pueden realizar esta acción";
            header('Location: index.php?action=proveedores&method=list');
            exit;
        }
    }
}
?>
