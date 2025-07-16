<?php require_once 'views/layout/header.php'; ?>

<h2>💳 Gestión de Deudores</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="search-container">
    <div class="search-row">
        <div>
            <input type="text" id="debt-search" placeholder="🔍 Buscar deudores por cliente o cédula..." 
                   style="margin-bottom: 0;">
        </div>
        <div>
            <a href="index.php?action=ventas&method=new" class="btn btn-success">🛒 Nueva Venta</a>
        </div>
    </div>
</div>

<?php if (empty($debts)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <h3>🎉 ¡Excelente! No hay deudas pendientes</h3>
        <p style="color: #7f8c8d; margin: 1rem 0;">Todos los clientes están al día con sus pagos</p>
        <a href="index.php?action=ventas&method=new" class="btn btn-success">🛒 Registrar Nueva Venta</a>
    </div>
<?php else: ?>
    <div class="card">
        <div style="overflow-x: auto;">
            <table id="debts-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cédula/RIF</th>
                        <th>Teléfono</th>
                        <th>Fecha Venta</th>
                        <th>Total Venta</th>
                        <th>Pagado</th>
                        <th>Deuda</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($debts as $debt): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($debt['Nombre'] . ' ' . $debt['Apellido']); ?></strong></td>
                        <td><?php echo htmlspecialchars($debt['Cedula_Rif']); ?></td>
                        <td>
                            <?php if (!empty($debt['Telefono'])): ?>
                                <a href="tel:<?php echo $debt['Telefono']; ?>" style="color: #27ae60; text-decoration: none;">
                                    📱 <?php echo htmlspecialchars($debt['Telefono']); ?>
                                </a>
                            <?php else: ?>
                                <span style="color: #95a5a6;">Sin teléfono</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($debt['Fecha_Emision'])); ?></td>
                        <td><strong>Bs <?php echo number_format($debt['Monto_Total'], 2); ?></strong></td>
                        <td style="color: #27ae60;"><strong>Bs <?php echo number_format($debt['Monto_Pagado'], 2); ?></strong></td>
                        <td style="color: #e74c3c;"><strong>Bs <?php echo number_format($debt['Monto_Deuda'], 2); ?></strong></td>
                        <td>
                            <span class="status-indicator status-low">
                                ⚠️ Pendiente
                            </span>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="index.php?action=deudores&method=addPayment&id=<?php echo $debt['id_deuda']; ?>" 
                                   class="action-edit">💰 Registrar Pago</a>
                                <a href="index.php?action=deudores&method=details&id=<?php echo $debt['id_deuda']; ?>" 
                                   class="action-view">👁️ Detalles</a>
                                <a href="index.php?action=ventas&method=details&id=<?php echo $debt['id_venta']; ?>" 
                                   class="action-view">📄 Ver Venta</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estadísticas de Deudas -->
    <div class="stats-grid" style="margin-top: 2rem;">
        <div class="stat-card">
            <span class="stat-number"><?php echo count($debts); ?></span>
            <span class="stat-label">👥 Clientes con Deuda</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #e74c3c;">Bs <?php echo number_format(array_sum(array_column($debts, 'Monto_Deuda')), 2); ?></span>
            <span class="stat-label">💳 Total Adeudado</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #27ae60;">Bs <?php echo number_format(array_sum(array_column($debts, 'Monto_Pagado')), 2); ?></span>
            <span class="stat-label">💰 Total Pagado</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">Bs <?php echo number_format(array_sum(array_column($debts, 'Monto_Total')), 2); ?></span>
            <span class="stat-label">📊 Total Ventas</span>
        </div>
    </div>

    <div class="alert alert-info" style="margin-top: 2rem;">
        <strong>💡 Consejos para la gestión de deudas:</strong>
        <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
            <li>Contacta regularmente a los clientes con deudas pendientes</li>
            <li>Ofrece facilidades de pago para recuperar las deudas</li>
            <li>Registra todos los pagos parciales para mantener un control exacto</li>
            <li>Considera establecer políticas de crédito para futuras ventas</li>
        </ul>
    </div>
<?php endif; ?>

<script>
// Búsqueda en tiempo real
document.getElementById('debt-search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('debts-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const clientName = row.cells[0].textContent.toLowerCase();
        const clientId = row.cells[1].textContent.toLowerCase();
        const phone = row.cells[2].textContent.toLowerCase();
        
        if (clientName.includes(searchTerm) || clientId.includes(searchTerm) || phone.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Efectos hover para las filas
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#debts-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#fff5f5';
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