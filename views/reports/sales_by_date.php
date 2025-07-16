<?php require_once 'views/layout/header.php'; ?>

<h2>📈 Reporte de Ventas por Fecha</h2>

<!-- Navegación de Reportes -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="index.php?action=reportes&method=salesByDate" 
       class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);">
        📈 Por Fechas
    </a>
    <a href="index.php?action=reportes&method=salesMonth" 
       class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        📅 Ventas del Mes
    </a>
    <a href="index.php?action=reportes&method=topProduct" 
       class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        🏆 Producto Más Vendido
    </a>
    <a href="index.php?action=reportes&method=topProducts" 
       class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        🏆 Top Productos
    </a>
</div>

<div class="form-container">
    <form action="index.php?action=reportes&method=salesByDate" method="post" id="reportForm">
        <div class="form-row">
            <div class="form-group">
                <label for="start_date">📅 Fecha Inicio:</label>
                <input type="date" id="start_date" name="start_date" required 
                       value="<?php echo isset($startDate) ? $startDate : ''; ?>">
            </div>
            <div class="form-group">
                <label for="end_date">📅 Fecha Fin:</label>
                <input type="date" id="end_date" name="end_date" required
                       value="<?php echo isset($endDate) ? $endDate : ''; ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: end; gap: 0.5rem;">
                <button type="submit" name="generate_report" class="btn btn-success">📊 Generar Reporte</button>
                <?php if (isset($sales) && !empty($sales)): ?>
                <a href="index.php?action=invoice&method=salesReport&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>" 
                   target="_blank" class="btn btn-warning">📄 Generar PDF</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php if (isset($sales)): ?>
    <div class="card">
        <h3>📊 Resultados del <?php echo date('d/m/Y', strtotime($startDate)); ?> al <?php echo date('d/m/Y', strtotime($endDate)); ?></h3>
        
        <!-- Estadísticas del período -->
        <div class="stats-grid" style="margin: 2rem 0;">
            <div class="stat-card">
                <span class="stat-number"><?php echo count($sales); ?></span>
                <span class="stat-label">📊 Total Ventas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo number_format($total, 2); ?></span>
                <span class="stat-label">💰 Total VOUCHERdo</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo count($sales) > 0 ? number_format($total / count($sales), 2) : '0.00'; ?></span>
                <span class="stat-label">📈 Promedio por Venta</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo count(array_unique(array_column($sales, 'Cedula_Rif'))); ?></span>
                <span class="stat-label">👥 Clientes Únicos</span>
            </div>
        </div>
        
        <?php if (!empty($sales)): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Cédula/RIF</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong>#<?php echo $sale['id_venta']; ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($sale['Fecha_Emision'])); ?></td>
                            <td><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></td>
                            <td><?php echo htmlspecialchars($sale['Cedula_Rif']); ?></td>
                            <td><strong style="color: #27ae60;">Bs <?php echo number_format($sale['Total'], 2); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: #e8f5e8; font-weight: bold;">
                            <td colspan="4" style="text-align: right;"><strong>TOTAL GENERAL:</strong></td>
                            <td><strong style="color: #27ae60; font-size: 1.1rem;">Bs <?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <strong>ℹ️ Sin resultados:</strong> No se encontraron ventas en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
// Configurar fechas por defecto
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    if (!document.getElementById('start_date').value) {
        document.getElementById('start_date').valueAsDate = firstDay;
    }
    if (!document.getElementById('end_date').value) {
        document.getElementById('end_date').valueAsDate = today;
    }
});

// Validación del formulario
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate > endDate) {
        e.preventDefault();
        alert('⚠️ La fecha de inicio no puede ser mayor que la fecha de fin.');
        return false;
    }
    
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays > 365) {
        if (!confirm('⚠️ Has seleccionado un período de más de un año. Esto podría generar un reporte muy grande. ¿Deseas continuar?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>