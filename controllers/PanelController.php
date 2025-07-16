<?php
require_once 'controllers/AuthController.php';

class PanelController {
    public function index() {
        // Verificar autenticación
        AuthController::checkAuth();
        
        // Cargar la vista del panel de control
        require_once 'views/panel.php';
    }
}
?>