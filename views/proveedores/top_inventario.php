<h2>Proveedor con más inventario</h2>
<?php if ($proveedor): ?>
    <p><strong><?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?></strong> con <strong><?php echo $proveedor['total_inventario']; ?></strong> unidades en inventario.</p>
<?php else: ?>
    <p>No hay datos de inventario.</p>
<?php endif; ?> 