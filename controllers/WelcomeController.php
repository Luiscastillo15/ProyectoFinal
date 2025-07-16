<?php
require_once 'controllers/AuthController.php';

class WelcomeController {
    public function index() {
        // Verificar autenticación
        AuthController::checkAuth();
        
        // Cargar la vista de bienvenida
        require_once 'views/welcome.php';
    }
}
?>