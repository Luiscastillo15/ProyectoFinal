<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Productos Más Vendidos - Sistema AguaZero C.A.</title>
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
            border-bottom: 3px solid #3498db;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .report-date {
            font-size: 10px;
            color: #95a5a6;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        
        .data-table th {
            background: #3498db;
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
        <a href="index.php?action=reportes&method=topProducts" class="btn btn-secondary">⬅️ Volver</a>
    </div>

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name">SISTEMA AguaZero C.A.</div>
            <div class="report-title">🏆 TOP <?php echo count($products); ?> PRODUCTOS MÁS VENDIDOS</div>
            <div class="report-date">Generado el: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
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

        <!-- Tabla de Datos -->
        <?php if (!empty($products)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Producto</th>
                    <th class="text-center">Cantidad Vendida</th>
                    <th class="text-right">Total Vendido</th>
                    <th class="text-right">Precio Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): 
                    $precioPromedio = $product['total_cantidad'] > 0 ? $product['total_venta'] / $product['total_cantidad'] : 0;
                ?>
                <tr>
                    <td class="text-center">
                        <span class="position-medal">
                            <?php 
                            if ($index == 0) echo '🥇 1°';
                            elseif ($index == 1) echo '🥈 2°';
                            elseif ($index == 2) echo '🥉 3°';
                            else echo '#' . ($index + 1);
                            ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($product['producto_nombre']); ?></strong></td>
                    <td class="text-center"><strong><?php echo number_format($product['total_cantidad']); ?></strong></td>
                    <td class="text-right"><strong>Bs <?php echo number_format($product['total_venta'], 2); ?></strong></td>
                    <td class="text-right">Bs <?php echo number_format($precioPromedio, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <h3>Sin datos</h3>
            <p>No se encontraron productos vendidos en el sistema.</p>
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