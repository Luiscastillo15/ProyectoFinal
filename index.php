<?php
session_start();

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

// Obtener la acción y método de la URL
$action = $_GET['action'] ?? 'login';
$method = $_GET['method'] ?? 'index';

// Enrutador principal
try {
    switch ($action) {
        case 'login':
            require_once 'controllers/AuthController.php';
            $controller = new AuthController();
            $controller->login();
            break;
            
        case 'logout':
            require_once 'controllers/AuthController.php';
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case 'welcome':
            require_once 'controllers/WelcomeController.php';
            $controller = new WelcomeController();
            $controller->index();
            break;
            
        case 'dashboard':
        case 'panel':
            require_once 'controllers/DashboardController.php';
            $controller = new DashboardController();
            $controller->index();
            break;
            
        case 'productos':
            require_once 'controllers/ProductController.php';
            $controller = new ProductController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
            break;
            
        case 'clientes':
            require_once 'controllers/ClientController.php';
            $controller = new ClientController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
            break;
            
        case 'proveedores':
            require_once 'controllers/ProveedorController.php';
            $controller = new ProveedorController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
            break;
            
        case 'ventas':
            require_once 'controllers/SaleController.php';
            $controller = new SaleController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
            break;
            
        case 'deudores':
            require_once 'controllers/DebtController.php';
            $controller = new DebtController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
            break;
            
        case 'reportes':
            require_once 'controllers/ReportController.php';
            $controller = new ReportController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->salesByDate();
            }
            break;
            
        case 'invoice':
            require_once 'controllers/InvoiceController.php';
            $controller = new InvoiceController();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->generate();
            }
            break;
            
        case 'check_session':
            // Endpoint para verificar sesión vía AJAX
            header('Content-Type: application/json');
            echo json_encode(['authenticated' => AuthController::isAuthenticated()]);
            exit;
            
        default:
            // Si no hay acción válida, redirigir al login
            if (!AuthController::isAuthenticated()) {
                header('Location: index.php?action=login');
            } else {
                header('Location: index.php?action=welcome');
            }
            exit;
    }
} catch (Exception $e) {
    // Manejo de errores
    error_log("Error en la aplicación: " . $e->getMessage());
    
    if (!headers_sent()) {
        if (!AuthController::isAuthenticated()) {
            header('Location: index.php?action=login');
        } else {
            $_SESSION['error'] = "Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.";
            header('Location: index.php?action=welcome');
        }
    }
    exit;
}
?>