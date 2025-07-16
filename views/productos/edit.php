<?php require_once 'views/layout/header.php'; ?>

<h2>✏️ Editar Producto</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?action=productos&method=edit&id=<?php echo $product['id_producto']; ?>" method="post" id="productEditForm">
        <div class="form-row">
            <div class="form-group">
                <label for="nombre">📦 Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo htmlspecialchars($product['Nombre']); ?>"
                       placeholder="Ej: Botellón de Agua 20L">
            </div>
            
            <div class="form-group">
                <label for="precio">💰 Precio de Venta (Bs):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required 
                       value="<?php echo $product['Precio']; ?>"
                       placeholder="0.00">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="unidad">📏 Unidad de Medida:</label>
                <select id="unidad" name="unidad" required>
                    <option value="">Seleccione una unidad</option>
                    <option value="Unidad" <?php echo ($product['Unidad'] === 'Unidad') ? 'selected' : ''; ?>>Unidad</option>
                    <option value="Botellón" <?php echo ($product['Unidad'] === 'Botellón') ? 'selected' : ''; ?>>Botellón</option>
                    <option value="Litro" <?php echo ($product['Unidad'] === 'Litro') ? 'selected' : ''; ?>>Litro (L)</option>
                    <option value="Galón" <?php echo ($product['Unidad'] === 'Galón') ? 'selected' : ''; ?>>Galón</option>
                    <option value="Kilogramo" <?php echo ($product['Unidad'] === 'Kilogramo') ? 'selected' : ''; ?>>Kilogramo (Kg)</option>
                    <option value="Gramo" <?php echo ($product['Unidad'] === 'Gramo') ? 'selected' : ''; ?>>Gramo (g)</option>
                    <option value="Metro" <?php echo ($product['Unidad'] === 'Metro') ? 'selected' : ''; ?>>Metro (m)</option>
                    <option value="Caja" <?php echo ($product['Unidad'] === 'Caja') ? 'selected' : ''; ?>>Caja</option>
                    <option value="Paquete" <?php echo ($product['Unidad'] === 'Paquete') ? 'selected' : ''; ?>>Paquete</option>
                    <option value="Docena" <?php echo ($product['Unidad'] === 'Docena') ? 'selected' : ''; ?>>Docena</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cantidad">📊 Cantidad en Stock:</label>
                <input type="number" id="cantidad" name="cantidad" min="0" required 
                       value="<?php echo $product['Cantidad']; ?>"
                       placeholder="0">
            </div>
        </div>

        <!-- Sección de Proveedor -->
        <div class="card" style="margin: 2rem 0; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-left: 4px solid #3498db;">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem;">🏭 Información del Proveedor</h3>
            
            <div class="form-group">
                <label for="id_proveedor">🏭 Proveedor (Opcional):</label>
                <select id="id_proveedor" name="id_proveedor">
                    <option value="">Sin proveedor asignado</option>
                    <?php if (!empty($proveedores)): ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?php echo $proveedor['id_proveedor']; ?>" 
                                    <?php echo (isset($product['id_proveedor']) && $product['id_proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
                                <?php 
                                $iconos = [
                                    'Agua Purificada' => '💧',
                                    'Botellones' => '🏺',
                                    'Dispensadores' => '🚰',
                                    'Filtros' => '🔧',
                                    'Químicos' => '⚗️',
                                    'Envases' => '📦',
                                    'Equipos' => '🏭',
                                    'Otros' => '📋'
                                ];
                                $icono = $iconos[$proveedor['Tipo_Producto']] ?? '🏭';
                                echo $icono . ' ' . htmlspecialchars($proveedor['Nombre']) . ' - ' . htmlspecialchars($proveedor['Tipo_Producto']);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Selecciona el proveedor que suministra este producto
                </small>
            </div>

            <!-- Información del proveedor seleccionado -->
            <div id="proveedor-info" style="display: none; margin-top: 1rem; padding: 1rem; background: white; border-radius: 8px; border: 1px solid #dee2e6;">
                <h4 style="color: #2c3e50; margin-bottom: 0.8rem; font-size: 1rem;">📋 Información del Proveedor</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <strong>RIF:</strong><br>
                        <span id="proveedor-rif" style="color: #3498db;">-</span>
                    </div>
                    <div>
                        <strong>Contacto:</strong><br>
                        <span id="proveedor-contacto" style="color: #7f8c8d;">-</span>
                    </div>
                    <div>
                        <strong>Teléfono:</strong><br>
                        <span id="proveedor-telefono" style="color: #27ae60;">-</span>
                    </div>
                    <div>
                        <strong>Tipo de Producto:</strong><br>
                        <span id="proveedor-tipo" style="color: #f39c12;">-</span>
                    </div>
                </div>
            </div>

            <?php if (empty($proveedores)): ?>
                <div class="alert alert-warning" style="margin-top: 1rem;">
                    <strong>⚠️ No hay proveedores registrados</strong><br>
                    <a href="index.php?action=proveedores&method=add" style="color: #856404; text-decoration: underline;">
                        Haz clic aquí para agregar un proveedor
                    </a>
                </div>
            <?php else: ?>
                <div style="text-align: right; margin-top: 1rem;">
                    <a href="index.php?action=proveedores&method=add" class="btn" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease;">
                        ➕ Agregar Nuevo Proveedor
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Información:</strong> Puedes cambiar el proveedor asociado a este producto o dejarlo sin proveedor.
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Actualizar Producto
            </button>
            <a href="index.php?action=productos&method=list" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Datos de proveedores para JavaScript
const proveedoresData = <?php echo json_encode($proveedores ?? []); ?>;

// Mostrar información del proveedor seleccionado
document.getElementById('id_proveedor').addEventListener('change', function() {
    const proveedorId = this.value;
    const proveedorInfo = document.getElementById('proveedor-info');
    
    if (proveedorId && proveedoresData.length > 0) {
        const proveedor = proveedoresData.find(p => p.id_proveedor == proveedorId);
        
        if (proveedor) {
            document.getElementById('proveedor-rif').textContent = proveedor.RIF;
            document.getElementById('proveedor-contacto').textContent = proveedor.Contacto || 'Sin contacto';
            document.getElementById('proveedor-telefono').textContent = proveedor.Telefono || 'Sin teléfono';
            document.getElementById('proveedor-tipo').textContent = proveedor.Tipo_Producto;
            
            proveedorInfo.style.display = 'block';
        }
    } else {
        proveedorInfo.style.display = 'none';
    }
});

// Validación del formulario
document.getElementById('productEditForm').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const precio = parseFloat(document.getElementById('precio').value);
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const unidad = document.getElementById('unidad').value;
    
    if (nombre.length < 3) {
        e.preventDefault();
        alert('⚠️ El nombre del producto debe tener al menos 3 caracteres.');
        return false;
    }
    
    if (precio <= 0) {
        e.preventDefault();
        alert('⚠️ El precio debe ser mayor a 0.');
        return false;
    }
    
    if (cantidad < 0) {
        e.preventDefault();
        alert('⚠️ La cantidad no puede ser negativa.');
        return false;
    }
    
    if (!unidad) {
        e.preventDefault();
        alert('⚠️ Debe seleccionar una unidad de medida.');
        return false;
    }
    
    // Confirmación
    let confirmMessage = `¿Confirmar actualización del producto?\n\nNombre: ${nombre}\nPrecio: Bs ${precio.toFixed(2)}\nCantidad: ${cantidad}\nUnidad: ${unidad}`;
    
    const proveedorSelect = document.getElementById('id_proveedor');
    if (proveedorSelect.value) {
        const proveedorText = proveedorSelect.options[proveedorSelect.selectedIndex].text;
        confirmMessage += `\nProveedor: ${proveedorText}`;
    }
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});

// Inicializar información del proveedor si hay uno seleccionado
document.addEventListener('DOMContentLoaded', function() {
    const proveedorSelect = document.getElementById('id_proveedor');
    if (proveedorSelect.value) {
        proveedorSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>