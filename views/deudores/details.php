<?php require_once 'views/layout/header.php'; ?>

<h2>📋 Detalles de Deuda</h2>

<!-- Información del Cliente -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>👤 Información del Cliente</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>Cliente:</strong><br>
            <span style="font-size: 1.2rem; color: #2c3e50;"><?php echo htmlspecialchars($debt['Nombre'] . ' ' . $debt['Apellido']); ?></span>
        </div>
        <div>
            <strong>Cédula/RIF:</strong><br>
            <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($debt['Cedula_Rif']); ?></span>
        </div>
        <div>
            <strong>Teléfono:</strong><br>
            <?php if (!empty($debt['Telefono'])): ?>
                <a href="tel:<?php echo $debt['Telefono']; ?>" style="color: #27ae60; text-decoration: none; font-size: 1.1rem;">
                    📱 <?php echo htmlspecialchars($debt['Telefono']); ?>
                </a>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin teléfono registrado</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Dirección:</strong><br>
            <span style="color: #7f8c8d;"><?php echo !empty($debt['Direccion']) ? htmlspecialchars($debt['Direccion']) : 'No registrada'; ?></span>
        </div>
    </div>
</div>

<!-- Resumen de la Deuda -->
<div class="stats-grid" style="margin-bottom: 2rem;">
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

<!-- Información de la Venta -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>🛒 Información de la Venta</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>ID de Venta:</strong><br>
            <span style="font-size: 1.1rem; color: #3498db;">#<?php echo $debt['id_venta']; ?></span>
        </div>
        <div>
            <strong>Fecha de Venta:</strong><br>
            <span style="color: #2c3e50;"><?php echo date('d/m/Y H:i', strtotime($debt['Fecha_Emision'])); ?></span>
        </div>
        <div>
            <strong>Estado de la Deuda:</strong><br>
            <span class="status-indicator status-low">⚠️ <?php echo htmlspecialchars($debt['Estado']); ?></span>
        </div>
        <div>
            <strong>Último Pago:</strong><br>
            <span style="color: #7f8c8d;">
                <?php echo !empty($debt['Fecha_Ultimo_Pago']) ? date('d/m/Y H:i', strtotime($debt['Fecha_Ultimo_Pago'])) : 'Sin pagos registrados'; ?>
            </span>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="index.php?action=ventas&method=details&id=<?php echo $debt['id_venta']; ?>" class="btn btn-secondary">
            📄 Ver Detalles de la Venta
        </a>
    </div>
</div>

<!-- Historial de Pagos -->
<div class="card">
    <h3>📋 Historial de Pagos</h3>
    
    <?php if (!empty($payments)): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Monto</th>
                        <th>Método de Pago</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($payment['Fecha_Pago'])); ?></td>
                        <td><strong style="color: #27ae60;">Bs <?php echo number_format($payment['Monto'], 2); ?></strong></td>
                        <td>
                            <?php 
                            $methodIcons = [
                                'Efectivo' => '💵',
                                'Transferencia' => '🏦',
                                'Tarjeta' => '💳',
                                'Divisas' => '💱',
                                'Cheque' => '📝'
                            ];
                            $icon = $methodIcons[$payment['Metodo_Pago']] ?? '💰';
                            echo $icon . ' ' . htmlspecialchars($payment['Metodo_Pago']);
                            ?>
                        </td>
                        <td>
                            <span class="status-indicator status-high">✅ Registrado</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <strong>Total de Pagos:</strong><br>
                    <span style="color: #27ae60; font-size: 1.2rem;">Bs <?php echo number_format(array_sum(array_column($payments, 'Monto')), 2); ?></span>
                </div>
                <div>
                    <strong>Número de Pagos:</strong><br>
                    <span style="color: #3498db; font-size: 1.2rem;"><?php echo count($payments); ?> pago(s)</span>
                </div>
                <div>
                    <strong>Promedio por Pago:</strong><br>
                    <span style="color: #7f8c8d; font-size: 1.2rem;">Bs <?php echo number_format(array_sum(array_column($payments, 'Monto')) / count($payments), 2); ?></span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: #7f8c8d;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
            <h4>No hay pagos registrados</h4>
            <p>Esta deuda aún no tiene pagos registrados</p>
        </div>
    <?php endif; ?>
</div>

<!-- Acciones -->
<div style="text-align: center; margin-top: 2rem;">
    <?php if ($debt['Estado'] === 'Pendiente'): ?>
        <a href="index.php?action=deudores&method=addPayment&id=<?php echo $debt['id_deuda']; ?>" 
           class="btn btn-success" style="margin-right: 1rem;">
            💰 Registrar Pago
        </a>
    <?php endif; ?>
    
    <a href="index.php?action=deudores&method=list" class="btn btn-secondary">
        ⬅️ Volver a Deudores
    </a>
</div>

<?php require_once 'views/layout/footer.php'; ?>