<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas - <?php echo $meses[$month]; ?> <?php echo $year; ?> - Sistema AguaZero C.A.</title>
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
            border-bottom: 3px solid #27ae60;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #27ae60;
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
            border-left: 4px solid #27ae60;
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
            background: #27ae60;
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
        
        .data-table .total-row {
            background: #e8f5e8;
            font-weight: bold;
            color: #27ae60;
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
        <a href="index.php?action=reportes&method=salesMonth" class="btn btn-secondary">⬅️ Volver</a>
    </div>

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name">SISTEMA AguaZero C.A.</div>
            <div class="report-title">📅 REPORTE DE VENTAS DEL MES</div>
            <div class="report-period"><?php echo $meses[$month]; ?> <?php echo $year; ?></div>
            <div class="report-date">Generado el: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $totalSales; ?></span>
                <span class="stat-label">🛒 Total Ventas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo number_format($total, 2); ?></span>
                <span class="stat-label">💰 Total VOUCHERdo</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo $totalSales > 0 ? number_format($total / $totalSales, 2) : '0.00'; ?></span>
                <span class="stat-label">📈 Promedio por Venta</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo count(array_unique(array_column($sales, 'Cedula_Rif'))); ?></span>
                <span class="stat-label">👥 Clientes Únicos</span>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <?php if (!empty($sales)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Cédula/RIF</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td>#<?php echo $sale['id_venta']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($sale['Fecha_Emision'])); ?></td>
                    <td><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></td>
                    <td><?php echo htmlspecialchars($sale['Cedula_Rif']); ?></td>
                    <td class="text-right">Bs <?php echo number_format($sale['Total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL DEL MES:</strong></td>
                    <td class="text-right"><strong>Bs <?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <h3>Sin resultados</h3>
            <p>No se encontraron ventas en <?php echo $meses[$month]; ?> de <?php echo $year; ?>.</p>
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