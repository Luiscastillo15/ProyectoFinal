<?php require_once 'views/layout/header.php'; ?>

<h2>📋 Detalles de Venta #<?php echo $sale['id_venta']; ?></h2>

<!-- Información de la Venta -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>🛒 Información de la Venta</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>ID de Venta:</strong><br>
            <span style="font-size: 1.2rem; color: #3498db;">#<?php echo $sale['id_venta']; ?></span>
        </div>
        <div>
            <strong>Fecha y Hora:</strong><br>
            <span style="color: #2c3e50;"><?php echo date('d/m/Y H:i:s', strtotime($sale['Fecha_Emision'])); ?></span>
        </div>
        <div>
            <strong>Estado:</strong><br>
            <span class="status-indicator status-high">✅ <?php echo htmlspecialchars($sale['Estado']); ?></span>
        </div>
        <div>
            <strong>Total de la Venta:</strong><br>
            <span style="font-size: 1.3rem; color: #27ae60; font-weight: bold;">Bs <?php echo number_format($sale['Total'], 2); ?></span>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="index.php?action=invoice&method=generate&id=<?php echo $sale['id_venta']; ?>" 
           class="btn btn-warning" target="_blank">
            📄 Generar VOUCHER PDF
        </a>
    </div>
</div>

<!-- Información del Cliente -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>👤 Información del Cliente</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>Cliente:</strong><br>
            <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($sale['Nombre'] . ' ' . $sale['Apellido']); ?></span>
        </div>
        <div>
            <strong>Cédula/RIF:</strong><br>
            <span style="color: #2c3e50;"><?php echo htmlspecialchars($sale['Cedula_Rif']); ?></span>
        </div>
        <div>
            <strong>Teléfono:</strong><br>
            <?php if (!empty($sale['Telefono'])): ?>
                <a href="tel:<?php echo $sale['Telefono']; ?>" style="color: #27ae60; text-decoration: none;">
                    📱 <?php echo htmlspecialchars($sale['Telefono']); ?>
                </a>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin teléfono registrado</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Email:</strong><br>
            <?php if (!empty($sale['Correo'])): ?>
                <a href="mailto:<?php echo $sale['Correo']; ?>" style="color: #3498db; text-decoration: none;">
                    📧 <?php echo htmlspecialchars($sale['Correo']); ?>
                </a>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin email registrado</span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($sale['Direccion'])): ?>
    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
        <strong>Dirección:</strong><br>
        <span style="color: #7f8c8d;"><?php echo htmlspecialchars($sale['Direccion']); ?></span>
    </div>
    <?php endif; ?>
</div>

<!-- Estado de Pago -->
<?php if (isset($debt) && $debt): ?>
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid #e74c3c;">
    <h3 style="color: #e74c3c;">💳 Estado de Pago - DEUDA PENDIENTE</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-number">Bs <?php echo number_format($debt['Monto_Total'], 2); ?></span>
            <span class="stat-label">💼 Total de la Venta</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #27ae60;">Bs <?php echo number_format($debt['Monto_Pagado'], 2); ?></span>
            <span class="stat-label">✅ Total Pagado</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #e74c3c;">Bs <?php echo number_format($debt['Monto_Deuda'], 2); ?></span>
            <span class="stat-label">⚠️ Deuda Pendiente</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo number_format(($debt['Monto_Pagado'] / $debt['Monto_Total']) * 100, 1); ?>%</span>
            <span class="stat-label">📊 Porcentaje Pagado</span>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="index.php?action=deudores&method=addPayment&id=<?php echo $debt['id_deuda']; ?>" 
           class="btn btn-warning">
            💰 Registrar Pago de Deuda
        </a>
        <a href="index.php?action=deudores&method=details&id=<?php echo $debt['id_deuda']; ?>" 
           class="btn btn-secondary">
            📋 Ver Detalles de Deuda
        </a>
    </div>
</div>
<?php else: ?>
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid #27ae60;">
    <h3 style="color: #27ae60;">✅ Estado de Pago - COMPLETAMENTE PAGADO</h3>
    <p style="color: #7f8c8d; text-align: center; margin: 1rem 0;">
        Esta venta ha sido pagada en su totalidad.
    </p>
</div>
<?php endif; ?>

<!-- Productos Vendidos -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>📦 Productos Vendidos</h3>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
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
                    <td><strong><?php echo htmlspecialchars($detail['producto_nombre']); ?></strong></td>
                    <td>Bs <?php echo number_format($detail['Precio_Unitario'], 2); ?></td>
                    <td><?php echo $detail['Cantidad']; ?></td>
                    <td><strong style="color: #27ae60;">Bs <?php echo number_format($subtotal, 2); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="3" style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td><strong style="color: #27ae60; font-size: 1.2rem;">Bs <?php echo number_format($totalCalculado, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Información de Pagos -->
<div class="card">
    <h3>💰 Información de Pagos</h3>
    
    <?php foreach ($payments as $payment): ?>
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <strong>Pago #<?php echo $payment['id_pago_venta']; ?></strong><br>
                    <span style="color: #7f8c8d;">ID del Pago</span>
                </div>
                <div>
                    <strong><?php echo date('d/m/Y H:i:s', strtotime($payment['Fecha'])); ?></strong><br>
                    <span style="color: #7f8c8d;">Fecha del Pago</span>
                </div>
                <div>
                    <strong style="color: #27ae60;">Bs <?php echo number_format($payment['Monto'], 2); ?></strong><br>
                    <span style="color: #7f8c8d;">Monto del Pago</span>
                </div>
            </div>
            
            <h4 style="margin: 1rem 0 0.5rem 0;">💳 Detalles del Pago</h4>
            <div style="overflow-x: auto;">
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th>Método de Pago</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentDetails[$payment['id_pago_venta']] as $detail): ?>
                        <tr>
                            <td>
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
                            </td>
                            <td><strong style="color: #27ae60;">Bs <?php echo number_format($detail['Monto'], 2); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($detail['Fecha'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Acciones -->
<div style="text-align: center; margin-top: 2rem;">
    <a href="index.php?action=ventas&method=list" class="btn btn-secondary">
        ⬅️ Volver a Ventas
    </a>
    
    <?php if (isset($debt) && $debt && $debt['Estado'] === 'Pendiente'): ?>
        <a href="index.php?action=deudores&method=list" class="btn btn-warning">
            💳 Ver Todos los Deudores
        </a>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>