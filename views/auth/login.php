<?php 
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Ventas - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>

<div class="login-page-container">
    <div class="login-content">

        <div class="login-logo">
            <img src="assets/imagenes/Imagen de WhatsApp 2025-06-20 a las 23.47.39_e7804b75.jpg" 
                 alt="Logo del Sistema" 
                 class="logo-image">
            <div class="logo-text">
                <h1>Sistema de Control</h1>
                <h2>de Ventas</h2>
                <p>Gestiona tu negocio de manera eficiente</p>
            </div>
        </div>

        <div class="login-form-container">
            <div class="login-form-header">
                <h2>🔐 Iniciar Sesión</h2>
                <p>Accede a tu cuenta para continuar</p>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    ⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=login" method="post" class="login-form">
                <div class="form-group">
                    <label for="id_usuario">👤 Usuario:</label>
                    <input type="text" id="id_usuario" name="id_usuario" required 
                           placeholder="Ingrese su usuario" autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="contraseña">🔑 Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required 
                           placeholder="Ingrese su contraseña" autocomplete="current-password">
                </div>
                
                <button type="submit" class="login-button">
                    🚀 Ingresar al Sistema
                </button>
            </form>
        </div>
    </div>
</div>

<script>
if (window.history && window.history.pushState) {
    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', function() {
        window.history.pushState(null, null, window.location.href);
    });
}

window.addEventListener('beforeunload', function() {
    if (window.performance && window.performance.navigation.type === 2) {
        window.location.reload();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('id_usuario').focus();
});
</script>

</body>
</html>