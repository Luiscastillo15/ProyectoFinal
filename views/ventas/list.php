<?php require_once 'views/layout/header.php'; ?>

<h2>🛒 Sistema de Ventas</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Botones de Acción Principal -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="index.php?action=ventas&method=new" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; font-size: 1.1rem; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);">
        🛒 Nueva Venta
    </a>
    
    <?php 
    // Solo mostrar botones de gestión para administradores
    $isAdmin = isset($_SESSION['rol_nombre']) && strtolower($_SESSION['rol_nombre']) === 'administrador';
    if ($isAdmin): 
    ?>
    <a href="index.php?action=clientes&method=add" class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 1rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);">
        👤 Registrar Cliente
    </a>
    <?php endif; ?>
</div>

<!-- Filtros y Búsqueda -->
<div class="search-container">
    <div class="search-row">
        <div>
            <input type="text" id="sales-search" placeholder="🔍 Buscar ventas por cliente, ID o cédula..." 
                   style="margin-bottom: 0;">
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <select id="payment-filter" style="padding: 0.5rem;">
                <option value="">Todos los estados</option>
                <option value="Pagado">✅ Pagado</option>
            </select>
            <button onclick="clearFilters()" class="btn" style="background: linear-gradient(135deg, #95a5a6, #7f8c8d); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
                🔄 Limpiar
            </button>
        </div>
    </div>
</div>

<?php if (empty($sales)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
        <h3>No hay ventas registradas</h3>
        <p style="color: #7f8c8d; margin: 1rem 0;">Comienza registrando tu primera venta</p>
        <a href="index.php?action=ventas&method=new" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; font-size: 1.1rem; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);">
            🛒 Registrar Primera Venta
        </a>
    </div>
<?php else: ?>
    <!-- Estadísticas Rápidas -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <span class="stat-number"><?php echo count($sales); ?></span>
            <span class="stat-label">📊 Total Ventas</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">Bs <?php echo number_format(array_sum(array_column($sales, 'Total')), 2); ?></span>
            <span class="stat-label">💰 Ingresos totales</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #27ae60;"><?php echo count(array_filter($sales, function($s) { return $s['Estado_Pago'] === 'Pagado'; })); ?></span>
            <span class="stat-label">✅ Ventas Pagadas</span>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table id="sales-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Cédula/RIF</th>
                        <th>Total</th>
                        <th>Estado Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr data-payment-status="<?php echo $sale['Estado_Pago']; ?>">
                        <td><strong>#<?php echo $sale['id_venta']; ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($sale['Fecha_Emision'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></strong></td>
                        <td><?php echo htmlspecialchars($sale['Cedula_Rif']); ?></td>
                        <td><strong style="color: #27ae60;">Bs <?php echo number_format($sale['Total'], 2); ?></strong></td>
                        <td>
                            <?php if ($sale['Estado_Pago'] === 'Pagado'): ?>
                                <span class="status-indicator status-high">✅ Pagado</span>
                            <?php else: ?>
                                <span class="status-indicator status-low">⚠️ No Pagado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="index.php?action=ventas&method=details&id=<?php echo $sale['id_venta']; ?>" 
                                   class="action-view">👁️ Detalles</a>
                                
                                <?php 
                                // Solo mostrar eliminar para administradores
                                if ($isAdmin): 
                                ?>
                                <a href="index.php?action=ventas&method=delete&id=<?php echo $sale['id_venta']; ?>" 
                                   class="action-delete"
                                   onclick="return confirm('⚠️ ¿Estás seguro de eliminar esta venta?\n\n• Se restaurará el stock de los productos\n• Esta acción no se puede deshacer\n• ID Venta: #<?php echo $sale['id_venta']; ?>\n• Cliente: <?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?>\n• Total: Bs <?php echo number_format($sale['Total'], 2); ?>')">
                                   🗑️ Eliminar</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!$isAdmin): ?>
    <div class="alert alert-info" style="margin-top: 2rem;">
        <strong>💡 Información:</strong> Solo los administradores pueden eliminar ventas. 
        Como vendedor, puedes ver los detalles de todas las ventas registradas.
    </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// Búsqueda en tiempo real
document.getElementById('sales-search').addEventListener('input', function() {
    filterTable();
});

// Filtro por estado de pago
document.getElementById('payment-filter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('sales-search').value.toLowerCase();
    const paymentFilter = document.getElementById('payment-filter').value;
    const table = document.getElementById('sales-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const saleId = row.cells[0].textContent.toLowerCase();
        const clientName = row.cells[2].textContent.toLowerCase();
        const clientId = row.cells[3].textContent.toLowerCase();
        const paymentStatus = row.getAttribute('data-payment-status');
        
        // Verificar búsqueda de texto
        const matchesSearch = saleId.includes(searchTerm) || 
                             clientName.includes(searchTerm) || 
                             clientId.includes(searchTerm);
        
        // Verificar filtro de estado
        const matchesPaymentFilter = !paymentFilter || paymentStatus === paymentFilter;
        
        if (matchesSearch && matchesPaymentFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function clearFilters() {
    document.getElementById('sales-search').value = '';
    document.getElementById('payment-filter').value = '';
    filterTable();
}

// Efectos hover para las filas
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#sales-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
        });
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>