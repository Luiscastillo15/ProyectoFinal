<?php require_once 'views/layout/header.php'; ?>

<h2>🏆 Productos Más Vendidos</h2>

<!-- Navegación de Reportes -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="index.php?action=reportes&method=salesByDate" 
       class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
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
       class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(155, 89, 182, 0.3);">
        🏆 Top Productos
    </a>
</div>

<div class="form-container">
    <form action="index.php?action=reportes&method=topProducts" method="get" id="topProductsForm">
        <!-- Campos ocultos para mantener la acción y método -->
        <input type="hidden" name="action" value="reportes">
        <input type="hidden" name="method" value="topProducts">
        
        <div class="form-row">
            <div class="form-group">
                <label for="limit">📊 Cantidad de Productos:</label>
                <select id="limit" name="limit" required>
                    <?php $currentLimit = isset($_GET['limit']) ? $_GET['limit'] : 10; ?>
                    <option value="10" <?php echo ($currentLimit == 10) ? 'selected' : ''; ?>>Top 10</option>
                    <option value="20" <?php echo ($currentLimit == 20) ? 'selected' : ''; ?>>Top 20</option>
                    <option value="50" <?php echo ($currentLimit == 50) ? 'selected' : ''; ?>>Top 50</option>
                    <option value="100" <?php echo ($currentLimit == 100) ? 'selected' : ''; ?>>Top 100</option>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: end; gap: 0.5rem;">
                <button type="submit" class="btn btn-success">📊 Generar Reporte</button>
                <?php if (isset($products) && !empty($products)): ?>
                <a href="index.php?action=invoice&method=topProductsReport&limit=<?php echo $currentLimit; ?>" 
                   target="_blank" class="btn btn-warning">📄 Generar PDF</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php if (isset($products)): ?>
    <div class="card">
        <h3>🏆 Top <?php echo count($products); ?> Productos Más Vendidos</h3>
        
        <!-- Estadísticas -->
        <div class="stats-grid" style="margin: 2rem 0;">
            <div class="stat-card">
                <span class="stat-number"><?php echo count($products); ?></span>
                <span class="stat-label">📦 Productos Listados</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo number_format(array_sum(array_column($products, 'total_cantidad'))); ?></span>
                <span class="stat-label">📊 Unidades Vendidas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo number_format(array_sum(array_column($products, 'total_venta')), 2); ?></span>
                <span class="stat-label">💰 Ingresos Generados</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo count($products) > 0 ? number_format(array_sum(array_column($products, 'total_venta')) / count($products), 2) : '0.00'; ?></span>
                <span class="stat-label">📈 Promedio por Producto</span>
            </div>
        </div>
        
        <?php if (!empty($products)): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Producto</th>
                            <th>Cantidad Vendida</th>
                            <th>Total Vendido</th>
                            <th>Precio Promedio</th>
                            <th>Participación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalGeneral = array_sum(array_column($products, 'total_venta'));
                        foreach ($products as $index => $product): 
                            $participacion = $totalGeneral > 0 ? ($product['total_venta'] / $totalGeneral) * 100 : 0;
                            $precioPromedio = $product['total_cantidad'] > 0 ? $product['total_venta'] / $product['total_cantidad'] : 0;
                        ?>
                        <tr>
                            <td>
                                <strong style="color: <?php 
                                    if ($index == 0) echo '#FFD700'; // Oro
                                    elseif ($index == 1) echo '#C0C0C0'; // Plata
                                    elseif ($index == 2) echo '#CD7F32'; // Bronce
                                    else echo '#3498db';
                                ?>;">
                                    <?php 
                                    if ($index == 0) echo '🥇';
                                    elseif ($index == 1) echo '🥈';
                                    elseif ($index == 2) echo '🥉';
                                    else echo '#' . ($index + 1);
                                    ?>
                                </strong>
                            </td>
                            <td><strong><?php echo htmlspecialchars($product['producto_nombre']); ?></strong></td>
                            <td><strong style="color: #3498db;"><?php echo number_format($product['total_cantidad']); ?></strong></td>
                            <td><strong style="color: #27ae60;">Bs <?php echo number_format($product['total_venta'], 2); ?></strong></td>
                            <td>Bs <?php echo number_format($precioPromedio, 2); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="background: #ecf0f1; height: 20px; width: 100px; border-radius: 10px; overflow: hidden;">
                                        <div style="background: linear-gradient(45deg, #3498db, #2980b9); height: 100%; width: <?php echo $participacion; ?>%; transition: width 0.3s ease;"></div>
                                    </div>
                                    <span style="font-size: 0.9rem; color: #7f8c8d;"><?php echo number_format($participacion, 1); ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info" style="margin-top: 2rem;">
                <strong>💡 Análisis de Productos:</strong>
                <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                    <li>Los productos más vendidos representan el <strong><?php echo number_format((array_sum(array_slice(array_column($products, 'total_venta'), 0, 5)) / $totalGeneral) * 100, 1); ?>%</strong> de las ventas totales (Top 5)</li>
                    <li>Considera mantener stock alto de los productos estrella</li>
                    <li>Analiza por qué algunos productos venden más que otros</li>
                    <li>Usa esta información para planificar promociones y descuentos</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <strong>ℹ️ Sin datos:</strong> No se encontraron productos vendidos en el sistema.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
// Prevenir envío duplicado del formulario
document.getElementById('topProductsForm').addEventListener('submit', function(e) {
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