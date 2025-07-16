<?php require_once 'views/layout/header.php'; ?>

<h2>💰 Registrar Pago de Deuda</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<!-- Información del Cliente y Deuda -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>👤 Información del Cliente</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>Cliente:</strong><br>
            <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($debt['Nombre'] . ' ' . $debt['Apellido']); ?></span>
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
            <strong>Fecha de Venta:</strong><br>
            <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo date('d/m/Y H:i', strtotime($debt['Fecha_Emision'])); ?></span>
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
        <span class="stat-label">✅ Ya Pagado</span>
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

<!-- Formulario de Pago -->
<div class="form-container">
    <h3>💳 Registrar Nuevo Pago</h3>
    
    <form action="index.php?action=deudores&method=addPayment&id=<?php echo $debt['id_deuda']; ?>" method="post" id="paymentForm">
        <div class="form-row">
            <div class="form-group">
                <label for="payment_amount">💰 Monto del Pago (Bs):</label>
                <input type="number" id="payment_amount" name="payment_amount" 
                       step="0.01" min="0.01" max="<?php echo $debt['Monto_Deuda']; ?>" 
                       value="<?php echo $debt['Monto_Deuda']; ?>" required 
                       placeholder="0.00">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Máximo: Bs <?php echo number_format($debt['Monto_Deuda'], 2); ?>
                </small>
            </div>
            
            <div class="form-group">
                <label for="payment_method">💳 Método de Pago:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Seleccione método</option>
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Transferencia">🏦 Transferencia</option>
                    <option value="Tarjeta">💳 Tarjeta</option>
                    <option value="Divisas">💱 Divisas</option>
                    <option value="Cheque">📝 Cheque</option>
                </select>
            </div>
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Información:</strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <li>Puedes registrar pagos parciales</li>
                <li>El sistema calculará automáticamente la deuda restante</li>
                <li>Si pagas el total, la deuda se marcará como "Pagado"</li>
            </ul>
        </div>
        
        <div id="payment-preview" style="display: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0;">
            <h4 style="color: #2c3e50; margin-bottom: 1rem;">📋 Resumen del Pago</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <strong>Monto a pagar:</strong><br>
                    <span id="preview-amount" style="color: #27ae60; font-size: 1.2rem;">Bs 0.00</span>
                </div>
                <div>
                    <strong>Deuda restante:</strong><br>
                    <span id="preview-remaining" style="color: #e74c3c; font-size: 1.2rem;">Bs 0.00</span>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Registrar Pago
            </button>
            <a href="index.php?action=deudores&method=list" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Historial de Pagos -->
<?php if (!empty($payments)): ?>
<div class="card" style="margin-top: 2rem;">
    <h3>📋 Historial de Pagos</h3>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Método</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($payment['Fecha_Pago'])); ?></td>
                    <td><strong style="color: #27ae60;">Bs <?php echo number_format($payment['Monto'], 2); ?></strong></td>
                    <td><?php echo htmlspecialchars($payment['Metodo_Pago']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
const maxDebt = <?php echo $debt['Monto_Deuda']; ?>;

// Actualizar preview del pago
function updatePaymentPreview() {
    const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
    const remaining = maxDebt - paymentAmount;
    
    document.getElementById('preview-amount').textContent = 'Bs ' + paymentAmount.toFixed(2);
    document.getElementById('preview-remaining').textContent = 'Bs ' + Math.max(0, remaining).toFixed(2);
    
    const preview = document.getElementById('payment-preview');
    if (paymentAmount > 0) {
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    
    // Cambiar color según si es pago total o parcial
    const remainingSpan = document.getElementById('preview-remaining');
    if (remaining <= 0) {
        remainingSpan.style.color = '#27ae60';
        remainingSpan.innerHTML = '<strong>✅ DEUDA SALDADA</strong>';
    } else {
        remainingSpan.style.color = '#e74c3c';
        remainingSpan.textContent = 'Bs ' + remaining.toFixed(2);
    }
}

// Event listeners
document.getElementById('payment_amount').addEventListener('input', function() {
    const value = parseFloat(this.value) || 0;
    
    // Validar que no exceda la deuda
    if (value > maxDebt) {
        this.value = maxDebt.toFixed(2);
    }
    
    updatePaymentPreview();
});

// Validación del formulario
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
    const paymentMethod = document.getElementById('payment_method').value;
    
    if (paymentAmount <= 0) {
        e.preventDefault();
        alert('⚠️ El monto del pago debe ser mayor a 0.');
        return false;
    }
    
    if (paymentAmount > maxDebt) {
        e.preventDefault();
        alert('⚠️ El monto del pago no puede ser mayor a la deuda pendiente.');
        return false;
    }
    
    if (!paymentMethod) {
        e.preventDefault();
        alert('⚠️ Debe seleccionar un método de pago.');
        return false;
    }
    
    // Confirmación
    const remaining = maxDebt - paymentAmount;
    let message = `¿Confirmar el registro del pago?\n\n`;
    message += `Cliente: <?php echo htmlspecialchars($debt['Nombre'] . ' ' . $debt['Apellido']); ?>\n`;
    message += `Monto a pagar: Bs ${paymentAmount.toFixed(2)}\n`;
    message += `Método: ${paymentMethod}\n`;
    
    if (remaining <= 0) {
        message += `\n✅ Esta deuda quedará COMPLETAMENTE SALDADA`;
    } else {
        message += `\nDeuda restante: Bs ${remaining.toFixed(2)}`;
    }
    
    if (!confirm(message)) {
        e.preventDefault();
        return false;
    }
});

// Inicializar preview
document.addEventListener('DOMContentLoaded', function() {
    updatePaymentPreview();
    document.getElementById('payment_amount').focus();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>