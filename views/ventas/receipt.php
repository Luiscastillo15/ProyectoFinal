<?php require_once 'views/layout/header.php'; ?>

<div style="max-width: 600px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="color: #27ae60; margin-bottom: 0.5rem;">✅ Venta Procesada Exitosamente</h2>
        <p style="color: #7f8c8d;">Venta #<?php echo $saleInfo['sale_id']; ?></p>
    </div>

    <!-- Resumen de la Venta -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h4 style="color: #2c3e50; margin-bottom: 1rem;">📋 Resumen de la Transacción</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <strong>Total de la venta:</strong><br>
                <span style="color: #3498db; font-size: 1.2rem; font-weight: bold;">Bs <?php echo number_format($saleInfo['total'], 2); ?></span>
            </div>
            <div>
                <strong>Cliente pagó:</strong><br>
                <span style="color: #27ae60; font-size: 1.2rem; font-weight: bold;">Bs <?php echo number_format($saleInfo['amount_paid'], 2); ?></span>
            </div>
            <div>
                <strong>Método de Pago:</strong><br>
                <span style="color: #7f8c8d;">
                    <?php 
                    $icons = [
                        'Efectivo' => '💵',
                        'Transferencia' => '🏦',
                        'Tarjeta' => '💳',
                        'Divisas' => '💱'
                    ];
                    echo ($icons[$saleInfo['payment_method']] ?? '💰') . ' ' . $saleInfo['payment_method'];
                    ?>
                </span>
            </div>
            <div>
                <strong>Fecha:</strong><br>
                <span style="color: #7f8c8d;"><?php echo date('d/m/Y H:i:s'); ?></span>
            </div>
        </div>
    </div>

    <!-- Información de Vuelto -->
    <?php if ($saleInfo['change'] > 0): ?>
        <div style="background: linear-gradient(135deg, #27ae60, #229954); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; text-align: center;">
            <h3 style="margin-bottom: 1rem;">💰 Vuelto a Entregar</h3>
            <div style="font-size: 2.5rem; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3); margin-bottom: 1rem;">
                Bs <?php echo number_format($saleInfo['change'], 2); ?>
            </div>
            <p style="opacity: 0.9; font-size: 1.1rem;">
                El cliente debe recibir este vuelto
            </p>
        </div>
    <?php elseif ($saleInfo['change'] == 0): ?>
        <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; text-align: center;">
            <h3 style="margin-bottom: 0.5rem;">✅ Pago Exacto</h3>
            <p style="opacity: 0.9; margin: 0;">No hay vuelto que entregar</p>
        </div>
    <?php elseif ($saleInfo['change'] < 0): ?>
        <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; text-align: center;">
            <h3 style="margin-bottom: 1rem;">⚠️ Pago Incompleto</h3>
            <div style="font-size: 2rem; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3); margin-bottom: 1rem;">
                Deuda: Bs <?php echo number_format(abs($saleInfo['change']), 2); ?>
            </div>
            <p style="opacity: 0.9; font-size: 1.1rem;">
                Se ha registrado una deuda pendiente
            </p>
        </div>
    <?php endif; ?>

    <!-- Información específica para divisas -->
    <?php if ($saleInfo['payment_method'] === 'Divisas'): ?>
        <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem; text-align: center;">💱 Detalles del Pago en Divisas</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="text-align: center;">
                    <div style="font-size: 0.9rem; opacity: 0.8;">Cliente pagó</div>
                    <div style="font-size: 1.8rem; font-weight: bold;">$<?php echo number_format($saleInfo['divisas_amount'], 2); ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.9rem; opacity: 0.8;">Tasa de cambio</div>
                    <div style="font-size: 1.4rem; font-weight: bold;">Bs <?php echo number_format($saleInfo['exchange_rate'], 2); ?></div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 0.9rem; opacity: 0.8;">Equivalente en bolívares</div>
                <div style="font-size: 1.6rem; font-weight: bold;">
                    Bs <?php echo number_format($saleInfo['divisas_amount'] * $saleInfo['exchange_rate'], 2); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="index.php?action=ventas&method=new" class="btn btn-success" style="margin-right: 1rem;">
            🛒 Nueva Venta
        </a>
        <a href="index.php?action=ventas&method=list" class="btn btn-secondary">
            📊 Ver Historial
        </a>
    </div>

    <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #ecf0f1;">
        <p style="color: #95a5a6; font-size: 0.9rem;">
            Gracias por usar nuestro sistema de ventas
        </p>
    </div>
</div>

<script>
// Auto-redirect después de 30 segundos
setTimeout(function() {
    if (confirm('¿Desea realizar otra venta?')) {
        window.location.href = 'index.php?action=ventas&method=new';
    } else {
        window.location.href = 'index.php?action=ventas&method=list';
    }
}, 30000);
</script>

<?php require_once 'views/layout/footer.php'; ?>