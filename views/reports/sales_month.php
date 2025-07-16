<?php require_once 'views/layout/header.php'; ?>

<h2>📅 Ventas del Mes</h2>

<!-- Navegación de Reportes -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="index.php?action=reportes&method=salesByDate" 
       class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        📈 Por Fechas
    </a>
    <a href="index.php?action=reportes&method=salesMonth" 
       class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);">
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
    <form action="index.php?action=reportes&method=salesMonth" method="get" id="monthForm">
        <!-- Campos ocultos para mantener la acción y método -->
        <input type="hidden" name="action" value="reportes">
        <input type="hidden" name="method" value="salesMonth">
        
        <div class="form-row">
            <div class="form-group">
                <label for="month">📅 Mes:</label>
                <select id="month" name="month" required>
                    <?php 
                    $meses = [
                        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                    ];
                    $currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
                    foreach ($meses as $num => $nombre): 
                    ?>
                        <option value="<?php echo $num; ?>" <?php echo ($currentMonth == $num) ? 'selected' : ''; ?>>
                            <?php echo $nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="year">📅 Año:</label>
                <select id="year" name="year" required>
                    <?php 
                    $currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                    for ($i = date('Y'); $i >= date('Y') - 5; $i--): 
                    ?>
                        <option value="<?php echo $i; ?>" <?php echo ($currentYear == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: end; gap: 0.5rem;">
                <button type="submit" class="btn btn-success">📊 Generar Reporte</button>
                <?php if (isset($dataProcessed) && !empty($sales)): ?>
                <a href="index.php?action=invoice&method=salesMonthReport&month=<?php echo $month; ?>&year=<?php echo $year; ?>" 
                   target="_blank" class="btn btn-warning">📄 Generar PDF</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php if (isset($dataProcessed)): ?>
    <div class="card">
        <h3>📊 Ventas de <?php echo $meses[$month]; ?> <?php echo $year; ?></h3>
        
        <?php if (!empty($sales)): ?>
            <!-- Estadísticas del mes -->
            <div class="stats-grid" style="margin: 2rem 0;">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalSales; ?></span>
                    <span class="stat-label">🛒 Total Ventas</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">Bs <?php echo number_format($total, 2); ?></span>
                    <span class="stat-label">💰 Total VOUCHERdo</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">Bs <?php echo number_format($averageSale, 2); ?></span>
                    <span class="stat-label">📈 Promedio por Venta</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo count(array_unique(array_column($sales, 'Cedula_Rif'))); ?></span>
                    <span class="stat-label">👥 Clientes Únicos</span>
                </div>
            </div>
            
            <!-- Gráfico de ventas por día (simplificado) -->
            <?php if (!empty($dailySales)): ?>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
                <h4 style="color: #2c3e50; margin-bottom: 1rem;">📈 Ventas por Día del Mes</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.5rem;">
                    <?php 
                    $maxSales = max(array_column($dailySales, 'count'));
                    for ($day = 1; $day <= 31; $day++): 
                        $dayData = isset($dailySales[$day]) ? $dailySales[$day] : ['count' => 0, 'total' => 0];
                        $height = $maxSales > 0 ? ($dayData['count'] / $maxSales) * 100 : 0;
                    ?>
                    <div style="text-align: center;">
                        <div style="background: <?php echo $dayData['count'] > 0 ? '#3498db' : '#ecf0f1'; ?>; 
                                    height: <?php echo max(20, $height); ?>px; 
                                    border-radius: 4px; 
                                    margin-bottom: 0.25rem;
                                    transition: all 0.3s ease;"
                             title="Día <?php echo $day; ?>: <?php echo $dayData['count']; ?> ventas - Bs <?php echo number_format($dayData['total'], 2); ?>"></div>
                        <small style="font-size: 0.7rem; color: #7f8c8d;"><?php echo $day; ?></small>
                    </div>
                    <?php endfor; ?>
                </div>
                <p style="text-align: center; margin-top: 1rem; color: #7f8c8d; font-size: 0.9rem;">
                    Pasa el cursor sobre las barras para ver detalles
                </p>
            </div>
            <?php endif; ?>
            
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong>#<?php echo $sale['id_venta']; ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($sale['Fecha_Emision'])); ?></td>
                            <td><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></td>
                            <td><strong style="color: #27ae60;">Bs <?php echo number_format($sale['Total'], 2); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: #e8f5e8; font-weight: bold;">
                            <td colspan="3" style="text-align: right;"><strong>TOTAL DEL MES:</strong></td>
                            <td><strong style="color: #27ae60; font-size: 1.1rem;">Bs <?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje cuando no hay datos -->
            <div style="text-align: center; padding: 4rem; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px; border: 2px dashed #dee2e6;">
                <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.6;">📊</div>
                <h3 style="color: #6c757d; margin-bottom: 1rem; font-size: 1.5rem;">Sin Datos Registrados</h3>
                <p style="color: #6c757d; font-size: 1.1rem; margin-bottom: 2rem; line-height: 1.6;">
                    No se encontraron ventas en <strong><?php echo $meses[$month]; ?> de <?php echo $year; ?></strong>.<br>
                    Intenta seleccionar otro mes o año con actividad comercial.
                </p>
                
                <div style="background: white; padding: 1.5rem; border-radius: 10px; margin: 2rem auto; max-width: 400px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="color: #495057; margin-bottom: 1rem;">💡 Sugerencias:</h4>
                    <ul style="text-align: left; color: #6c757d; line-height: 1.8;">
                        <li>Verifica que haya ventas registradas en ese período</li>
                        <li>Prueba con el mes actual: <strong><?php echo $meses[date('m')]; ?> <?php echo date('Y'); ?></strong></li>
                        <li>Consulta otros reportes disponibles</li>
                    </ul>
                </div>
                
                <div style="margin-top: 2rem;">
                    <a href="index.php?action=reportes&method=salesByDate" class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 0.5rem;">
                        📈 Reporte por Fechas
                    </a>
                    <a href="index.php?action=ventas&method=list" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 0.5rem;">
                        🛒 Ver Todas las Ventas
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="index.php?action=reportes&method=salesByDate" class="btn btn-secondary">📈 Reporte por Fechas</a>
        <a href="index.php?action=reportes&method=topProduct" class="btn btn-secondary">🏆 Producto Más Vendido</a>
    </div>
<?php endif; ?>

<script>
// Prevenir envío duplicado del formulario
document.getElementById('monthForm').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '⏳ Generando...';
    
    // Re-habilitar después de 3 segundos por si hay error
    setTimeout(function() {
        submitButton.disabled = false;
        submitButton.innerHTML = '📊 Generar Reporte';
    }, 3000);
});
</script>

<?php require_once 'views/layout/footer.php'; ?>