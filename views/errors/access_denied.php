<?php require_once 'views/layout/header.php'; ?>

<div style="text-align: center; padding: 4rem 2rem;">
    <div class="card" style="max-width: 600px; margin: 0 auto; border-left: 4px solid #e74c3c;">
        <div style="color: #e74c3c; font-size: 4rem; margin-bottom: 1rem;">
            🚫
        </div>
        
        <h2 style="color: #e74c3c; margin-bottom: 1rem;">Acceso Denegado</h2>
        
        <div class="alert alert-danger" style="margin: 2rem 0;">
            <strong>⚠️ Permisos Insuficientes</strong><br>
            Solo los usuarios con rol de <strong>Administrador</strong> pueden acceder a esta sección.
        </div>
        
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
            <p style="color: #7f8c8d; margin-bottom: 1rem;">
                <strong>Tu rol actual:</strong> 
                <span style="color: <?php 
                    $roleName = strtolower($_SESSION['rol_nombre'] ?? '');
                    echo ($roleName === 'administrador') ? '#e74c3c' : '#3498db';
                ?>; font-weight: 600;">
                    <?php 
                    $roleIcon = ($roleName === 'administrador') ? '👑' : '🛒';
                    echo $roleIcon . ' ' . ucfirst($_SESSION['rol_nombre'] ?? 'Usuario'); 
                    ?>
                </span>
            </p>
            
            <p style="color: #7f8c8d; line-height: 1.6;">
                Los reportes contienen información sensible del negocio y están restringidos 
                únicamente para administradores del sistema.
            </p>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="index.php?action=dashboard" class="btn btn-primary" style="margin-right: 1rem;">
                🏠 Ir al Dashboard
            </a>
            
            <?php if (strtolower($_SESSION['rol_nombre'] ?? '') === 'vendedor'): ?>
            <a href="index.php?action=ventas&method=new" class="btn btn-success">
                🛒 Nueva Venta
            </a>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #ecf0f1;">
            <p style="color: #95a5a6; font-size: 0.9rem;">
                Si necesitas acceso a los reportes, contacta con el administrador del sistema.
            </p>
        </div>
    </div>
</div>

<style>
.card {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php require_once 'views/layout/footer.php'; ?>