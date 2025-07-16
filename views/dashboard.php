<?php
require_once 'views/layout/header.php';

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto");
$stmt->execute();
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Cliente");
$stmt->execute();
$totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Venta WHERE MONTH(Fecha_Emision) = MONTH(CURRENT_DATE()) AND YEAR(Fecha_Emision) = YEAR(CURRENT_DATE())");
$stmt->execute();
$monthSales = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COALESCE(SUM(Total), 0) as total FROM Venta WHERE MONTH(Fecha_Emision) = MONTH(CURRENT_DATE()) AND YEAR(Fecha_Emision) = YEAR(CURRENT_DATE())");
$stmt->execute();
$monthRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad < 10");
$stmt->execute();
$lowStockProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT v.*, c.Nombre, c.Apellido FROM Venta v JOIN Cliente c ON v.Cedula_Rif = c.Cedula_Rif ORDER BY v.Fecha_Emision DESC LIMIT 5");
$stmt->execute();
$recentSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isAdmin = isset($_SESSION['rol_nombre']) && strtolower($_SESSION['rol_nombre']) === 'administrador';

// Arreglar el error del apellido
$nombreCompleto = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario');
if (!empty($_SESSION['apellido'])) {
    $nombreCompleto .= ' ' . htmlspecialchars($_SESSION['apellido']);
}
?>

<h2>📊 Panel de Control</h2>

<div class="alert alert-info">
    <strong>¡Bienvenido de vuelta, <?php echo $nombreCompleto; ?>!</strong> 
    Aquí tienes un resumen de tu negocio.
</div>

<div class="stats-grid">
    <div class="stat-card hover-lift">
        <span class="stat-number"><?php echo number_format($totalProducts); ?></span>
        <span class="stat-label">📦 Productos Registrados</span>
    </div>
    
    <div class="stat-card hover-lift">
        <span class="stat-number"><?php echo number_format($totalClients); ?></span>
        <span class="stat-label">👥 Clientes Activos</span>
    </div>
    
    <div class="stat-card hover-lift">
        <span class="stat-number"><?php echo number_format($monthSales); ?></span>
        <span class="stat-label">🛒 Ventas Este Mes</span>
    </div>
    
    <div class="stat-card hover-lift">
        <span class="stat-number">Bs <?php echo number_format($monthRevenue, 2); ?></span>
        <span class="stat-label">💰 Ingresos del Mes</span>
    </div>
    
    <?php if ($lowStockProducts > 0): ?>
    <div class="stat-card hover-lift" style="border-left: 4px solid #e74c3c;">
        <span class="stat-number" style="color: #e74c3c;"><?php echo $lowStockProducts; ?></span>
        <span class="stat-label">⚠️ Stock Bajo</span>
    </div>
    <?php endif; ?>
</div>

<div class="dashboard-sections">
    
    <section class="quick-actions">
        <h3>🚀 Acciones Rápidas</h3>
        <ul>
            <li><a href="index.php?action=ventas&method=new">🛒 Registrar Nueva Venta</a></li>
            
            <?php if ($isAdmin): ?>
            <li><a href="index.php?action=productos&method=add">📦 Agregar Producto</a></li>
            <li><a href="index.php?action=clientes&method=add">👤 Registrar Cliente</a></li>
            <li><a href="index.php?action=reportes&method=salesByDate">📈 Ver Reportes</a></li>
            <?php if ($lowStockProducts > 0): ?>
            <li><a href="index.php?action=reportes&method=lowStock" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">⚠️ Revisar Stock Bajo</a></li>
            <?php endif; ?>
            <?php else: ?>
            <li><a href="index.php?action=productos&method=list">📦 Ver Productos</a></li>
            <li><a href="index.php?action=clientes&method=list">👥 Ver Clientes</a></li>
            <li><a href="index.php?action=ventas&method=list">📊 Ver Mis Ventas</a></li>
            <?php endif; ?>
        </ul>
    </section>

    <section class="card">
        <h3>📋 Últimas Ventas</h3>
        <?php if (!empty($recentSales)): ?>
            <div style="overflow-x: auto;">
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td>#<?php echo $sale['id_venta']; ?></td>
                            <td><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></td>
                            <td><strong>Bs <?php echo number_format($sale['Total'], 2); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($sale['Fecha_Emision'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php?action=ventas&method=list" class="btn">Ver Todas las Ventas</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #7f8c8d; padding: 2rem;">
                📝 No hay ventas registradas aún. <br>
                <a href="index.php?action=ventas&method=new">¡Registra tu primera venta!</a>
            </p>
        <?php endif; ?>
    </section>
</div>

<?php if ($lowStockProducts > 0 && $isAdmin): ?>
<div class="alert alert-warning">
    <strong>⚠️ Atención:</strong> Tienes <?php echo $lowStockProducts; ?> producto(s) con stock bajo. 
    <a href="index.php?action=reportes&method=lowStock">Ver detalles</a>
</div>
<?php elseif ($lowStockProducts > 0): ?>
<div class="alert alert-warning">
    <strong>⚠️ Atención:</strong> Hay <?php echo $lowStockProducts; ?> producto(s) con stock bajo. 
    Contacta con el administrador para más detalles.
</div>
<?php endif; ?>

<?php if (!$isAdmin): ?>
<div class="alert alert-info">
    <strong>💡 Información:</strong> Como vendedor, tienes acceso a las funciones de venta y consulta. 
    Los reportes detallados están disponibles solo para administradores.
</div>
<?php endif; ?>

<div class="card" style="text-align: center; margin-top: 2rem;">
    <h3>💡 Consejos para tu Negocio</h3>
    <p style="color: #7f8c8d; line-height: 1.8;">
        <?php if ($isAdmin): ?>
        • Revisa regularmente el stock de tus productos más vendidos<br>
        • Mantén actualizada la información de tus clientes<br>
        • Genera reportes mensuales para analizar el rendimiento<br>
        • Considera ofrecer descuentos a clientes frecuentes
        <?php else: ?>
        • Mantén un buen servicio al cliente en cada venta<br>
        • Verifica siempre el stock antes de confirmar una venta<br>
        • Registra correctamente los datos del cliente<br>
        • Consulta con el administrador sobre promociones disponibles
        <?php endif; ?>
    </p>
</div>

<?php require_once 'views/layout/footer.php'; ?>