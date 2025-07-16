<?php
// controllers/DashboardController.php

require_once 'controllers/AuthController.php';

class DashboardController {
    public function index() {
        // Verificar autenticación
        AuthController::checkAuth();
        
        // Cargar la vista del dashboard
        require_once 'views/dashboard.php';
    }
}
?>