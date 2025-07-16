<?php require_once 'views/layout/header.php'; ?>

<h2>🏆 Producto Más Vendido</h2>

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
       class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);">
        🏆 Producto Más Vendido
    </a>
    <a href="index.php?action=reportes&method=topProducts" 
       class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        🏆 Top Productos
    </a>
</div>

<div class="form-container">
    <form action="index.php?action=reportes&method=topProduct" method="get" id="productForm">
        <!-- Campos ocultos para mantener la acción y método -->
        <input type="hidden" name="action" value="reportes">
        <input type="hidden" name="method" value="topProduct">
        
        <div class="form-row">
            <div class="form-group">
                <label for="period">📊 Período:</label>
                <select id="period" name="period" onchange="togglePeriodFields()" required>
                    <?php $currentPeriod = isset($_GET['period']) ? $_GET['period'] : 'month'; ?>
                    <option value="month" <?php echo ($currentPeriod === 'month') ? 'selected' : ''; ?>>📅 Mensual</option>
                    <option value="year" <?php echo ($currentPeriod === 'year') ? 'selected' : ''; ?>>📅 Anual</option>
                </select>
            </div>
            
            <div class="form-group" id="month-field">
                <label for="month">📅 Mes:</label>
                <select id="month" name="month">
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
                <button type="submit" class="btn btn-success">🏆 Buscar</button>
                <?php if (isset($dataProcessed) && $topProduct): ?>
                <a href="index.php?action=invoice&method=topProductReport&month=<?php echo $month; ?>&year=<?php echo $year; ?>&period=<?php echo $period; ?>" 
                   target="_blank" class="btn btn-warning">📄 Generar PDF</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php if (isset($dataProcessed)): ?>
    <div class="card">
        <h3>🏆 Producto Más Vendido - 
            <?php 
            if (isset($period) && $period === 'month') {
                echo $meses[$month] . ' ' . $year;
            } else {
                echo 'Año ' . $year;
            }
            ?>
        </h3>
        
        <?php if ($topProduct): ?>
            <!-- Producto ganador -->
            <div style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; padding: 2rem; border-radius: 15px; margin: 2rem 0; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🥇</div>
                <h2 style="margin-bottom: 1rem; font-size: 2rem;"><?php echo htmlspecialchars($topProduct['producto_nombre']); ?></h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                    <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px;">
                        <div style="font-size: 1.8rem; font-weight: bold;"><?php echo number_format($topProduct['total_cantidad']); ?></div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Unidades Vendidas</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px;">
                        <div style="font-size: 1.8rem; font-weight: bold;">Bs <?php echo number_format($topProduct['total_venta'], 2); ?></div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Total VOUCHERdo</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px;">
                        <div style="font-size: 1.8rem; font-weight: bold;">Bs <?php echo number_format($topProduct['precio_promedio'], 2); ?></div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Precio Promedio</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px;">
                        <div style="font-size: 1.8rem; font-weight: bold;"><?php echo number_format($topProduct['num_ventas']); ?></div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Ventas Realizadas</div>
                    </div>
                </div>
            </div>
            
            <!-- Top 5 productos para comparación -->
            <?php if (!empty($topProducts)): ?>
            <div style="margin-top: 2rem;">
                <h4 style="color: #2c3e50; margin-bottom: 1rem;">📊 Top 5 Productos del Período</h4>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Posición</th>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Total Vendido</th>
                                <th>Participación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalGeneral = array_sum(array_column($topProducts, 'total_venta'));
                            foreach ($topProducts as $index => $product): 
                                $participacion = $totalGeneral > 0 ? ($product['total_venta'] / $totalGeneral) * 100 : 0;
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
            </div>
            <?php endif; ?>
            
            <div class="alert alert-info" style="margin-top: 2rem;">
                <strong>💡 Análisis del Producto Más Vendido:</strong>
                <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                    <li>Este producto representa el <strong><?php echo !empty($topProducts) ? number_format(($topProduct['total_venta'] / array_sum(array_column($topProducts, 'total_venta'))) * 100, 1) : '100'; ?>%</strong> de las ventas del período</li>
                    <li>Se vendió en <strong><?php echo $topProduct['num_ventas']; ?></strong> transacciones diferentes</li>
                    <li>Considera mantener stock alto de este producto estrella</li>
                    <li>Analiza qué hace exitoso a este producto para aplicarlo a otros</li>
                </ul>
            </div>
        <?php else: ?>
            <!-- Mensaje cuando no hay datos -->
            <div style="text-align: center; padding: 4rem; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px; border: 2px dashed #dee2e6;">
                <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.6;">🏆</div>
                <h3 style="color: #6c757d; margin-bottom: 1rem; font-size: 1.5rem;">Sin Datos Registrados</h3>
                <p style="color: #6c757d; font-size: 1.1rem; margin-bottom: 2rem; line-height: 1.6;">
                    No se encontraron productos vendidos en 
                    <strong>
                        <?php 
                        if (isset($period) && $period === 'month') {
                            echo $meses[$month] . ' de ' . $year;
                        } else {
                            echo 'el año ' . $year;
                        }
                        ?>
                    </strong>.<br>
                    Intenta seleccionar otro período con actividad comercial.
                </p>
                
                <div style="background: white; padding: 1.5rem; border-radius: 10px; margin: 2rem auto; max-width: 400px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="color: #495057; margin-bottom: 1rem;">💡 Sugerencias:</h4>
                    <ul style="text-align: left; color: #6c757d; line-height: 1.8;">
                        <li>Verifica que haya ventas registradas en ese período</li>
                        <li>Prueba con el mes actual: <strong><?php echo $meses[date('m')]; ?> <?php echo date('Y'); ?></strong></li>
                        <li>Cambia a período anual para ver más datos</li>
                        <li>Consulta otros reportes disponibles</li>
                    </ul>
                </div>
                
                <div style="margin-top: 2rem;">
                    <a href="index.php?action=reportes&method=salesMonth" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 0.5rem;">
                        📅 Ventas del Mes
                    </a>
                    <a href="index.php?action=reportes&method=topProducts" class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 0.5rem;">
                        🏆 Top Productos
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="index.php?action=reportes&method=salesMonth" class="btn btn-secondary">📅 Ventas del Mes</a>
        <a href="index.php?action=reportes&method=topProducts" class="btn btn-secondary">🏆 Top Productos</a>
    </div>
<?php endif; ?>

<script>
function togglePeriodFields() {
    const period = document.getElementById('period').value;
    const monthField = document.getElementById('month-field');
    
    if (period === 'year') {
        monthField.style.display = 'none';
    } else {
        monthField.style.display = 'block';
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    togglePeriodFields();
});

// Prevenir envío duplicado del formulario
document.getElementById('productForm').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '⏳ Buscando...';
    
    // Re-habilitar después de 3 segundos por si hay error
    setTimeout(function() {
        submitButton.disabled = false;
        submitButton.innerHTML = '🏆 Buscar';
    }, 3000);
});
</script>

<?php require_once 'views/layout/footer.php'; ?>