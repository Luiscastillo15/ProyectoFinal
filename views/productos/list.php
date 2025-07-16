<?php require_once 'views/layout/header.php'; ?>

<h2>📦 Gestión de Inventario</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
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
                                if ($product['Cantidad'] <= 5) echo 'status-low';
                                elseif ($product['Cantidad'] <= 15) echo 'status-medium';
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
                            <?php if ($product['Cantidad'] <= 5): ?>
                                <span class="status-indicator status-low">⚠️ Stock Crítico</span>
                            <?php elseif ($product['Cantidad'] <= 15): ?>
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

    <div class="stats-grid" style="margin-top: 2rem;">
        <div class="stat-card">
            <span class="stat-number"><?php echo count($products); ?></span>
            <span class="stat-label">📦 Total Productos</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] > 15; })); ?></span>
            <span class="stat-label">✅ Stock Bueno</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #f39c12;"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= 15 && $p['Cantidad'] > 5; })); ?></span>
            <span class="stat-label">⚡ Stock Bajo</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #e74c3c;"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= 5; })); ?></span>
            <span class="stat-label">⚠️ Stock Crítico</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo count(array_filter($products, function($p) { return !empty($p['Proveedor_Nombre']); })); ?></span>
            <span class="stat-label">🏭 Con Proveedor</span>
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