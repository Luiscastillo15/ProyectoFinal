<?php


require_once 'models/UserModel.php';
require_once 'models/RolModel.php';

class AuthController {
    private $userModel;
    private $rolModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
        $this->rolModel = new RolModel($db);
    }

    public function login() {
        
        if (self::isAuthenticated()) {
            $this->redirectBasedOnRole($_SESSION['id_rol']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? '';
            $contraseña = $_POST['contraseña'] ?? '';
            
            $user = $this->userModel->getUserById($id_usuario);
            
            if ($user && $user['Contraseña'] === $contraseña) {
                
                session_regenerate_id(true);
                
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['cedula'] = $user['Cedula'];
                $_SESSION['nombre'] = $user['Nombre'];
                $_SESSION['apellido'] = $user['Apellido'];
                $_SESSION['id_rol'] = $user['id_rol'];
                $_SESSION['login_time'] = time();
                
                $role = $this->rolModel->getRoleById($user['id_rol']);
                $_SESSION['rol_nombre'] = $role['Nombre'];
                
                $this->userModel->updateLastLogin($user['Cedula']);
                
                $this->redirectBasedOnRole($user['id_rol']);
                return;
            } else {
                $_SESSION['error'] = "Credenciales incorrectas";
            }
        }
        
        require_once 'views/auth/login.php';
    }

    public function logout() {
        if (isset($_SESSION['cedula'])) {
            $this->userModel->registerLogout($_SESSION['cedula']);
        }
        
        
        $_SESSION = array();
        
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        
        session_destroy();
        
        
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        
        header('Location: index.php?action=login');
        exit;
    }

    private function redirectBasedOnRole($id_rol) {
        $role = $this->rolModel->getRoleById($id_rol);
        $location = 'index.php?action=welcome'; // Cambiar a welcome por defecto
        
        switch(strtolower($role['Nombre'])) {
            case 'administrador':
                $location = 'index.php?action=welcome';
                break;
            case 'vendedor':
                $location = 'index.php?action=ventas&method=new';
                break;
        }
        
        header("Location: $location");
        exit;
    }

    public static function isAuthenticated() {
        
        if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        $session_timeout = 8 * 60 * 60; // 8 horas en segundos
        if (time() - $_SESSION['login_time'] > $session_timeout) {
            return false;
        }
        
        return true;
    }

    public static function checkAuth($requiredRoles = []) {
        
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        if (!self::isAuthenticated()) {
            
            session_unset();
            session_destroy();
            header('Location: index.php?action=login');
            exit;
        }
        
        if (!empty($requiredRoles) && !in_array($_SESSION['id_rol'], $requiredRoles)) {
            header('Location: index.php?action=unauthorized');
            exit;
        }
        
        
        $_SESSION['login_time'] = time();
    }
}
?>