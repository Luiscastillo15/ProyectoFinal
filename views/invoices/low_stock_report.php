<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Stock Bajo - Sistema AguaZero C.A.</title>
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
            border-bottom: 3px solid #e74c3c;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .report-threshold {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
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
            border-left: 4px solid #e74c3c;
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
            background: #e74c3c;
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
        
        .status-critical {
            background: #fee;
            color: #c53030;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 9px;
        }
        
        .status-low {
            background: #fffbf0;
            color: #744210;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 9px;
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
        
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- Botones de acción -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir / Guardar PDF</button>
        <a href="index.php?action=reportes&method=lowStock" class="btn btn-secondary">⬅️ Volver</a>
    </div>

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name">SISTEMA AguaZero C.A.</div>
            <div class="report-title">⚠️ REPORTE DE PRODUCTOS CON STOCK BAJO</div>
            <div class="report-threshold">Umbral: ≤ <?php echo $threshold; ?> unidades</div>
            <div class="report-date">Generado el: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo count($products); ?></span>
                <span class="stat-label">⚠️ Productos Afectados</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= 5; })); ?></span>
                <span class="stat-label">🚨 Stock Crítico (≤5)</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo number_format(array_sum(array_map(function($p) { return $p['Precio'] * $p['Cantidad']; }, $products)), 2); ?></span>
                <span class="stat-label">💰 Valor Total Stock</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo array_sum(array_column($products, 'Cantidad')); ?></span>
                <span class="stat-label">📊 Unidades Totales</span>
            </div>
        </div>

        <!-- Alerta -->
        <?php if (!empty($products)): ?>
        <div class="alert">
            <strong>⚠️ Atención:</strong> Se encontraron <?php echo count($products); ?> productos con stock bajo que requieren reabastecimiento inmediato.
        </div>
        <?php endif; ?>

        <!-- Tabla de Datos -->
        <?php if (!empty($products)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th class="text-right">Precio Unit.</th>
                    <th>Unidad</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Valor Total</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): 
                    $valorProducto = $product['Precio'] * $product['Cantidad'];
                ?>
                <tr>
                    <td>#<?php echo $product['id_producto']; ?></td>
                    <td><strong><?php echo htmlspecialchars($product['Nombre']); ?></strong></td>
                    <td class="text-right">Bs <?php echo number_format($product['Precio'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['Unidad']); ?></td>
                    <td class="text-center"><strong style="color: <?php echo $product['Cantidad'] <= 5 ? '#e74c3c' : '#f39c12'; ?>;"><?php echo $product['Cantidad']; ?></strong></td>
                    <td class="text-right">Bs <?php echo number_format($valorProducto, 2); ?></td>
                    <td class="text-center">
                        <?php if ($product['Cantidad'] <= 5): ?>
                            <span class="status-critical">🚨 CRÍTICO</span>
                        <?php else: ?>
                            <span class="status-low">⚠️ BAJO</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #27ae60;">
            <h3>🎉 ¡Excelente!</h3>
            <p>No hay productos con stock bajo según el umbral establecido.</p>
            <p>Todos los productos tienen stock suficiente.</p>
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