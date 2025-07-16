<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Ventas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <?php if (isset($_SESSION['id_usuario'])): ?>
    
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php endif; ?>
</head>
<body>
    <header>
        <h1>🏪 Sistema de Control de Ventas</h1>
        <nav>
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="index.php?action=welcome">🏠 Inicio</a>
                <a href="index.php?action=panel">📊 Panel de Control</a>
                <a href="index.php?action=productos&method=list">📦 Inventario</a>
                <a href="index.php?action=proveedores&method=list">🏭 Proveedores</a>
                <a href="index.php?action=clientes&method=list">👥 Clientes</a>
                <a href="index.php?action=ventas&method=list">🛒 Ventas</a>
                <a href="index.php?action=deudores&method=list">💳 Deudores</a>
                
                <?php 
                
                $roleName = strtolower($_SESSION['rol_nombre'] ?? '');
                if ($roleName === 'administrador'): 
                ?>
                <a href="index.php?action=reportes&method=salesByDate">📈 Reportes</a>
                <?php endif; ?>
                
                
                <div style="margin-left: auto; display: flex; align-items: center; gap: 1rem;">
                    <div style="text-align: right; color: #7f8c8d; font-size: 0.9rem;">
                        <div style="font-weight: 600; color: #2c3e50;">
                            👤 <?php 
                            $nombreCompleto = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario');
                            if (!empty($_SESSION['apellido'])) {
                                $nombreCompleto .= ' ' . htmlspecialchars($_SESSION['apellido']);
                            }
                            echo $nombreCompleto;
                            ?>
                        </div>
                        <div style="font-size: 0.8rem; margin-top: 0.2rem;">
                            <?php 
                            $roleIcon = '';
                            $roleColor = '';
                            
                            switch($roleName) {
                                case 'administrador':
                                    $roleIcon = '👑';
                                    $roleColor = '#e74c3c';
                                    break;
                                case 'vendedor':
                                    $roleIcon = '🛒';
                                    $roleColor = '#3498db';
                                    break;
                                default:
                                    $roleIcon = '👤';
                                    $roleColor = '#7f8c8d';
                            }
                            ?>
                            <span style="color: <?php echo $roleColor; ?>; font-weight: 600;">
                                <?php echo $roleIcon; ?> <?php echo ucfirst($_SESSION['rol_nombre'] ?? 'Usuario'); ?>
                            </span>
                        </div>
                    </div>
                    <a href="index.php?action=logout" 
                       style="background: linear-gradient(45deg,rgb(41, 244, 255),rgb(220, 143, 239)); padding: 0.5rem 1rem;" 
                       onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?')">
                       🚪 Cerrar Sesión
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>

    <script>
    <?php if (isset($_SESSION['id_usuario'])): ?>
    
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            
            window.location.reload();
        }
    });
    
    
    window.addEventListener('popstate', function(event) {
        
        fetch('index.php?action=check_session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        }).then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                window.location.href = 'index.php?action=login';
            }
        }).catch(() => {
            
            window.location.href = 'index.php?action=login';
        });
    });
    <?php endif; ?>
    </script>