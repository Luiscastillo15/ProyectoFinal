<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOUCHER #<?php echo $sale['id_venta']; ?> - Sistema AguaZero C.A.</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
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
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3498db;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .company-details {
            font-size: 10px;
            color: #7f8c8d;
            line-height: 1.6;
        }
        
        .invoice-title {
            text-align: right;
            flex: 1;
        }
        
        .invoice-number {
            font-size: 28px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .invoice-date {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .invoice-status {
            display: inline-block;
            padding: 5px 15px;
            background: #27ae60;
            color: white;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .client-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .client-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .client-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .client-field {
            font-size: 11px;
        }
        
        .client-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 2px;
        }
        
        .client-value {
            color: #6c757d;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        
        .items-table th {
            background: #3498db;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }
        
        .totals-table .total-label {
            background: #f8f9fa;
            font-weight: bold;
            text-align: right;
        }
        
        .totals-table .total-amount {
            text-align: right;
            font-weight: bold;
        }
        
        .totals-table .grand-total {
            background: #3498db;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        
        .payment-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #27ae60;
        }
        
        .payment-title {
            font-size: 14px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 15px;
        }
        
        .payment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .payment-field {
            font-size: 11px;
        }
        
        .debt-info {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #ffc107;
        }
        
        .debt-title {
            font-size: 14px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 15px;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
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
        
        @media (max-width: 768px) {
            .invoice-header {
                flex-direction: column;
                text-align: center;
            }
            
            .invoice-title {
                text-align: center;
                margin-top: 20px;
            }
            
            .client-details {
                grid-template-columns: 1fr;
            }
            
            .payment-details {
                grid-template-columns: 1fr;
            }
            
            .totals-section {
                justify-content: center;
            }
            
            .no-print {
                position: relative;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Botones de acción (solo visible en pantalla) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir / Guardar PDF</button>
        <a href="index.php?action=ventas&method=details&id=<?php echo $sale['id_venta']; ?>" class="btn btn-secondary">⬅️ Volver</a>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <img src="assets/imagenes/Imagen de WhatsApp 2025-06-20 a las 23.47.39_e7804b75.jpg" 
                     alt="Logo AguaZero C.A." class="company-logo">
                <div class="company-name">AguaZero C.A.</div>
                <div class="company-subtitle">Sistema de Control de Ventas</div>
                <div class="company-details">
                    📍 Av.Enrique Tejera<br>
                    📱 +58 412-855.0868 | 📧 aguazeroca@gmail.com<br>
                </div>
            </div>
            <div class="invoice-title">
                <div class="invoice-number">VOUCHER #<?php echo str_pad($sale['id_venta'], 6, '0', STR_PAD_LEFT); ?></div>
                <div class="invoice-date">📅 <?php echo date('d/m/Y H:i:s', strtotime($sale['Fecha_Emision'])); ?></div>
                <div class="invoice-status">✅ <?php echo $sale['Estado']; ?></div>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="client-info">
            <div class="client-title">👤 INFORMACIÓN DEL CLIENTE</div>
            <div class="client-details">
                <div class="client-field">
                    <div class="client-label">Cliente:</div>
                    <div class="client-value"><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></div>
                </div>
                <div class="client-field">
                    <div class="client-label">Cédula/RIF:</div>
                    <div class="client-value"><?php echo htmlspecialchars($sale['Cedula_Rif']); ?></div>
                </div>
                <?php if (!empty($sale['Telefono'])): ?>
                <div class="client-field">
                    <div class="client-label">Teléfono:</div>
                    <div class="client-value">📱 <?php echo htmlspecialchars($sale['Telefono']); ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($sale['Correo'])): ?>
                <div class="client-field">
                    <div class="client-label">Email:</div>
                    <div class="client-value">📧 <?php echo htmlspecialchars($sale['Correo']); ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($sale['Direccion'])): ?>
                <div class="client-field" style="grid-column: 1 / -1;">
                    <div class="client-label">Dirección:</div>
                    <div class="client-value">🏠 <?php echo htmlspecialchars($sale['Direccion']); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Producto</th>
                    <th style="width: 15%;" class="text-center">Cantidad</th>
                    <th style="width: 17.5%;" class="text-right">Precio Unit.</th>
                    <th style="width: 17.5%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalCalculado = 0;
                foreach ($saleDetails as $detail): 
                    $subtotal = $detail['Precio_Unitario'] * $detail['Cantidad'];
                    $totalCalculado += $subtotal;
                ?>
                <tr>
                    <td class="font-bold"><?php echo htmlspecialchars($detail['producto_nombre']); ?></td>
                    <td class="text-center"><?php echo $detail['Cantidad']; ?></td>
                    <td class="text-right">Bs <?php echo number_format($detail['Precio_Unitario'], 2); ?></td>
                    <td class="text-right font-bold">Bs <?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">Bs <?php echo number_format($totalCalculado, 2); ?></td>
                </tr>
                <tr>
                    <td class="total-label">IVA (0%):</td>
                    <td class="total-amount">Bs 0.00</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL:</td>
                    <td>Bs <?php echo number_format($sale['Total'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        </div>

        <!-- Información de Pago -->
        <?php if (!empty($payments)): ?>
        <div class="payment-info">
            <div class="payment-title">💳 INFORMACIÓN DE PAGO</div>
            <?php foreach ($payments as $payment): ?>
                <div class="payment-details">
                    <div class="payment-field">
                        <div class="client-label">Fecha de Pago:</div>
                        <div class="client-value"><?php echo date('d/m/Y H:i:s', strtotime($payment['Fecha'])); ?></div>
                    </div>
                    <div class="payment-field">
                        <div class="client-label">Monto Pagado:</div>
                        <div class="client-value font-bold">Bs <?php echo number_format($payment['Monto'], 2); ?></div>
                    </div>
                    <?php if (isset($paymentDetails[$payment['id_pago_venta']])): ?>
                        <?php foreach ($paymentDetails[$payment['id_pago_venta']] as $detail): ?>
                        <div class="payment-field">
                            <div class="client-label">Método de Pago:</div>
                            <div class="client-value">
                                <?php 
                                $methodIcons = [
                                    'Efectivo' => '💵',
                                    'Transferencia' => '🏦',
                                    'Tarjeta' => '💳',
                                    'Divisas' => '💱'
                                ];
                                $icon = $methodIcons[$detail['Metodo_Pago']] ?? '💰';
                                echo $icon . ' ' . htmlspecialchars($detail['Metodo_Pago']);
                                ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Información de Deuda (si existe) -->
        <?php if (isset($debt) && $debt && $debt['Estado'] === 'Pendiente'): ?>
        <div class="debt-info">
            <div class="debt-title">⚠️ DEUDA PENDIENTE</div>
            <div class="payment-details">
                <div class="payment-field">
                    <div class="client-label">Monto Total:</div>
                    <div class="client-value font-bold">Bs <?php echo number_format($debt['Monto_Total'], 2); ?></div>
                </div>
                <div class="payment-field">
                    <div class="client-label">Monto Pagado:</div>
                    <div class="client-value">Bs <?php echo number_format($debt['Monto_Pagado'], 2); ?></div>
                </div>
                <div class="payment-field">
                    <div class="client-label">Deuda Pendiente:</div>
                    <div class="client-value font-bold" style="color: #dc3545;">Bs <?php echo number_format($debt['Monto_Deuda'], 2); ?></div>
                </div>
                <div class="payment-field">
                    <div class="client-label">Estado:</div>
                    <div class="client-value">⚠️ <?php echo $debt['Estado']; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Gracias por su compra</strong></p>
            <p>Sistema de Control de Ventas AguaZero C.A. © <?php echo date('Y'); ?></p>
            <p>Esta es un voucher generado electrónicamente</p>
        </div>
    </div>

    <script>
        // Auto-focus para impresión
        window.addEventListener('load', function() {
            // Opcional: abrir automáticamente el diálogo de impresión
            // window.print();
        });

        // Atajos de teclado
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>