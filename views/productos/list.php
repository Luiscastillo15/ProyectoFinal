<?php require_once 'views/layout/header.php'; ?>

<?php
// Obtener el proveedor con más inventario directamente aquí
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/models/ProveedorModel.php';
$database = new Database();
$db = $database->getConnection();
$proveedorModel = new ProveedorModel($db);
$proveedorTop = $proveedorModel->getProveedorConMasInventario();
?>

<?php
$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= p.Umbral_Critico");
$stmt->execute();
$criticalStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= p.Umbral_Bajo AND s.Cantidad > p.Umbral_Critico");
$stmt->execute();
$lowStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener productos con stock crítico para mostrar detalles
$stmt = $db->prepare("SELECT p.Nombre, s.Cantidad FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= p.Umbral_Critico ORDER BY s.Cantidad ASC LIMIT 5");
$stmt->execute();
$criticalProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- CDN Bootstrap 5 solo si no está en el header -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<?php if ($proveedorTop && !empty($proveedorTop['nombre_proveedor'])): ?>
    <div style="background: #e8f5e9; border-left: 6px solid #27ae60; padding: 1rem 1.5rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <strong>🏆 Proveedor con más inventario:</strong> <span style="color: #27ae60; font-weight: bold; font-size: 1.1rem;">
        <?php echo htmlspecialchars($proveedorTop['nombre_proveedor']); ?></span> <span style="color: #555;">con</span> <span style="font-weight: bold; color: #2d3436; font-size: 1.1rem;">
        <?php echo $proveedorTop['total_inventario']; ?></span> unidades.
    </div>
<?php endif; ?>

<h2>📦 Gestión de Inventario</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="stats-grid" style="margin-top: 2rem;">
    <div class="stat-card">
        <span class="stat-number"><?php echo count($products); ?></span>
        <span class="stat-label">📦 Total Productos</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] > $p['Umbral_Bajo']; })); ?></span>
        <span class="stat-label">✅ Stock Bueno</span>
    </div>
    <div class="stat-card">
        <span class="stat-number" style="color: #f39c12;"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= $p['Umbral_Bajo'] && $p['Cantidad'] > $p['Umbral_Critico']; })); ?></span>
        <span class="stat-label">⚡ Stock Bajo</span>
    </div>
    <div class="stat-card">
        <span class="stat-number" style="color: #e74c3c;"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= $p['Umbral_Critico']; })); ?></span>
        <span class="stat-label">⚠️ Stock Crítico</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo count(array_filter($products, function($p) { return !empty($p['Proveedor_Nombre']); })); ?></span>
        <span class="stat-label">🏭 Con Proveedor</span>
    </div>
</div>

<!-- Alerta de Stock Crítico -->
<?php if ($criticalStockCount > 0 || $lowStockCount > 0): ?>
<div class="alert" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); border-left: 4px solid #f39c12; margin-bottom: 2rem; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(243, 156, 18, 0.2);">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <div style="font-size: 2.5rem;">⚠️</div>
        <div>
            <h3 style="color: #856404; margin: 0; font-size: 1.3rem;">¡Atención! Stock Crítico Detectado</h3>
            <p style="color: #856404; margin: 0.5rem 0 0 0; font-size: 1rem;">Es necesario contactar a los proveedores para reabastecer productos</p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0;">
        <?php if ($criticalStockCount > 0): ?>
        <div style="background: rgba(231, 76, 60, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #e74c3c;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #e74c3c;"><?php echo $criticalStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #c0392b; font-weight: 600;">🚨 Stock Crítico</div>
        </div>
        <?php endif; ?>
        
        <?php if ($lowStockCount > 0): ?>
        <div style="background: rgba(243, 156, 18, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #f39c12;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #f39c12;"><?php echo $lowStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #d68910; font-weight: 600;">⚡ Stock Bajo</div>
        </div>
        <?php endif; ?>
        
        <div style="background: rgba(52, 152, 219, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #3498db;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;"><?php echo $criticalStockCount + $lowStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #2980b9; font-weight: 600;">📦 Total Afectados</div>
        </div>
    </div>
    
    <?php if (!empty($criticalProducts)): ?>
    <div style="background: rgba(255, 255, 255, 0.8); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <h4 style="color: #856404; margin-bottom: 0.8rem; font-size: 1rem;">🚨 Productos más críticos:</h4>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <?php foreach ($criticalProducts as $product): ?>
            <span style="background: #e74c3c; color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                <?php echo htmlspecialchars($product['Nombre']); ?> (<?php echo $product['Cantidad']; ?>)
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; border: 1px;">
        <a href="index.php?action=proveedores&method=lowStock" 
           class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);">
            📋 Ver Reporte Completo
        </a>
        <button class="btn" onclick="showCriticalProductProviders()" style="background: linear-gradient(135deg,rgb(10, 106, 58),rgb(49, 84, 44)); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">📞Contactar</button>        
    </div>
</div>
<?php endif; ?>

<div class="search-container">
    <div class="search-row">
        <div>
            <input type="text" id="product-search" placeholder="🔍 Buscar productos..." 
                   style="margin-bottom: 0;">
        </div>
        <div>
            <?php 
            // Solo mostrar botón de agregar para administradores
            $isAdmin = isset($_SESSION['rol_nombre']) && strtolower($_SESSION['rol_nombre']) === 'administrador';
            if ($isAdmin): 
            ?>
            <a href="index.php?action=productos&method=add" class="btn btn-success">➕ Agregar Producto</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (empty($products)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <h3>📦 No hay productos registrados</h3>
        <p style="color: #7f8c8d; margin: 1rem 0;">
            <?php if ($isAdmin): ?>
                Comienza agregando tu primer producto al inventario
            <?php else: ?>
                No hay productos disponibles en el inventario
            <?php endif; ?>
        </p>
        <?php if ($isAdmin): ?>
        <a href="index.php?action=productos&method=add" class="btn btn-success">➕ Agregar Primer Producto</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div style="overflow-x: auto;">
            <table id="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Unidad</th>
                        <th>Stock</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <?php if ($isAdmin): ?>
                        <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong>#<?php echo $product['id_producto']; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($product['Nombre']); ?></strong>
                        </td>
                        <td><strong>Bs <?php echo number_format($product['Precio'], 2); ?></strong></td>
                        <td><?php echo htmlspecialchars($product['Unidad']); ?></td>
                        <td>
                            <span class="status-indicator <?php 
                                if ($product['Cantidad'] <= $product['Umbral_Critico']) echo 'status-low';
                                elseif ($product['Cantidad'] <= $product['Umbral_Bajo']) echo 'status-medium';
                                else echo 'status-high';
                            ?>">
                                <?php echo $product['Cantidad']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($product['Proveedor_Nombre'])): ?>
                                <span style="background: #e8f5e8; color: #27ae60; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                    🏭 <?php echo htmlspecialchars($product['Proveedor_Nombre']); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #95a5a6; font-style: italic;">Sin proveedor</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['Cantidad'] <= 0): ?>
                                <span class="status-indicator status-low">🚫 Agotado</span>
                            <?php elseif ($product['Cantidad'] <= $product['Umbral_Critico']): ?>
                                <span class="status-indicator status-low">⚠️ Stock Crítico</span>
                            <?php elseif ($product['Cantidad'] <= $product['Umbral_Bajo']): ?>
                                <span class="status-indicator status-medium">⚡ Stock Bajo</span>
                            <?php else: ?>
                                <span class="status-indicator status-high">✅ Disponible</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isAdmin): ?>
                        <td>
                            <div class="action-links">
                                <a href="index.php?action=productos&method=edit&id=<?php echo $product['id_producto']; ?>" 
                                   class="action-edit">✏️ Editar</a>
                                <a href="index.php?action=productos&method=delete&id=<?php echo $product['id_producto']; ?>" 
                                   class="action-delete"
                                   onclick="return confirm('⚠️ ¿Estás seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.')">
                                   🗑️ Eliminar</a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!$isAdmin): ?>
    <div class="alert alert-info" style="margin-top: 2rem;">
        <strong>💡 Información:</strong> Como vendedor, puedes consultar el inventario pero no modificarlo. 
        Solo los administradores pueden agregar, editar o eliminar productos.
    </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// Función para mostrar proveedores de productos críticos
function showCriticalProductProviders() {
    // Obtener productos con stock crítico y sus proveedores
    fetch('index.php?action=proveedores&method=getCriticalProviders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.providers.length > 0) {
            showProviderModal(data.providers);
        } else {
            alert('No se encontraron proveedores para los productos con stock crítico.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al obtener información de proveedores.');
    });
}

// Función para mostrar modal con proveedores
function showProviderModal(providers) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    let providersHtml = '';
    providers.forEach(provider => {
        const productos = provider.productos.map(p => `<span style="background: #e74c3c; color: white; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem; margin: 0.1rem;">${p.nombre} (${p.stock})</span>`).join(' ');
        
        providersHtml += `
            <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid #e74c3c;">
                <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.1rem;">🏭 ${provider.nombre}</h4>
                        <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">RIF: ${provider.rif}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        ${provider.telefono ? `<a href="tel:${provider.telefono}" style="background: #27ae60; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">📱 Llamar</a>` : ''}
                        <a href="index.php?action=proveedores&method=details&id=${provider.id}" style="background: #3498db; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">👁️ Ver Detalles</a>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #e74c3c; font-size: 0.9rem;">🚨 Productos con stock crítico:</strong><br>
                    <div style="margin-top: 0.5rem;">${productos}</div>
                </div>
                ${provider.contacto ? `<p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;"><strong>Contacto:</strong> ${provider.contacto}</p>` : ''}
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 15px; max-width: 800px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div style="padding: 2rem; border-bottom: 1px solid #ecf0f1; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 15px 15px 0 0;">
                <h3 style="margin: 0; font-size: 1.5rem; text-align: center;">📞 Proveedores de Productos Críticos</h3>
                <p style="margin: 0.5rem 0 0 0; text-align: center; opacity: 0.9; font-size: 1rem;">Contacta directamente con los proveedores para reabastecer</p>
            </div>
            <div style="padding: 2rem;">
                ${providersHtml}
                <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                    <button onclick="this.closest('.modal-overlay').remove()" style="background: linear-gradient(135deg, #95a5a6, #7f8c8d); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        ✖️ Cerrar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    modal.className = 'modal-overlay';
    document.body.appendChild(modal);
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Búsqueda en tiempo real
document.getElementById('product-search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('products-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const productName = row.cells[1].textContent.toLowerCase();
        const productId = row.cells[0].textContent.toLowerCase();
        const proveedor = row.cells[5].textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || productId.includes(searchTerm) || proveedor.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>