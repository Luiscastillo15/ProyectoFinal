<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto Más Vendido - Sistema AguaZero C.A.</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #f39c12;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #f39c12;
            margin-bottom: 10px;
        }
        
        .report-period {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .report-date {
            font-size: 10px;
            color: #95a5a6;
        }
        
        .winner-section {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .winner-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .winner-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .winner-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .winner-stat {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 8px;
        }
        
        .winner-stat-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .winner-stat-label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        
        .data-table th {
            background: #f39c12;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .position-medal {
            font-weight: bold;
            font-size: 12px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Botones de acción -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir / Guardar PDF</button>
        <a href="index.php?action=reportes&method=topProduct" class="btn btn-secondary">⬅️ Volver</a>
    </div>

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name">SISTEMA AguaZero C.A.</div>
            <div class="report-title">🏆 PRODUCTO MÁS VENDIDO</div>
            <div class="report-period">
                <?php 
                if ($period === 'month') {
                    echo $meses[$month] . ' ' . $year;
                } else {
                    echo 'Año ' . $year;
                }
                ?>
            </div>
            <div class="report-date">Generado el: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <?php if ($topProduct): ?>
            <!-- Producto Ganador -->
            <div class="winner-section">
                <div class="winner-icon">🥇</div>
                <div class="winner-name"><?php echo htmlspecialchars($topProduct['producto_nombre']); ?></div>
                
                <div class="winner-stats">
                    <div class="winner-stat">
                        <span class="winner-stat-number"><?php echo number_format($topProduct['total_cantidad']); ?></span>
                        <span class="winner-stat-label">Unidades Vendidas</span>
                    </div>
                    <div class="winner-stat">
                        <span class="winner-stat-number">Bs <?php echo number_format($topProduct['total_venta'], 2); ?></span>
                        <span class="winner-stat-label">Total VOUCHERdo</span>
                    </div>
                    <div class="winner-stat">
                        <span class="winner-stat-number">Bs <?php echo number_format($topProduct['precio_promedio'], 2); ?></span>
                        <span class="winner-stat-label">Precio Promedio</span>
                    </div>
                    <div class="winner-stat">
                        <span class="winner-stat-number"><?php echo number_format($topProduct['num_ventas']); ?></span>
                        <span class="winner-stat-label">Ventas Realizadas</span>
                    </div>
                </div>
            </div>

            <!-- Top 5 Productos -->
            <?php if (!empty($topProducts)): ?>
            <h3 style="color: #2c3e50; margin-bottom: 15px;">📊 Top 5 Productos del Período</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Producto</th>
                        <th class="text-center">Cantidad Vendida</th>
                        <th class="text-right">Total Vendido</th>
                        <th class="text-right">Participación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalGeneral = array_sum(array_column($topProducts, 'total_venta'));
                    foreach ($topProducts as $index => $product): 
                        $participacion = $totalGeneral > 0 ? ($product['total_venta'] / $totalGeneral) * 100 : 0;
                    ?>
                    <tr>
                        <td class="text-center">
                            <span class="position-medal">
                                <?php 
                                if ($index == 0) echo '🥇';
                                elseif ($index == 1) echo '🥈';
                                elseif ($index == 2) echo '🥉';
                                else echo '#' . ($index + 1);
                                ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($product['producto_nombre']); ?></strong></td>
                        <td class="text-center"><strong><?php echo number_format($product['total_cantidad']); ?></strong></td>
                        <td class="text-right"><strong>Bs <?php echo number_format($product['total_venta'], 2); ?></strong></td>
                        <td class="text-right"><?php echo number_format($participacion, 1); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                <h3>Sin datos</h3>
                <p>No se encontraron productos vendidos en el período seleccionado.</p>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema de Control de Ventas AguaZero C.A.</strong></p>
            <p>Reporte generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>