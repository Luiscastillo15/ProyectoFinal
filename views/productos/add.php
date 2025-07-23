<?php require_once 'views/layout/header.php'; ?>

<h2>➕ Agregar Nuevo Producto</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?action=productos&method=add" method="post" id="productForm">
        <div class="form-row">
            <div class="form-group">
                <label for="nombre">📦 Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Ej: Botellón de Agua 20L"
                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="precio">💰 Precio de Venta (Bs):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required 
                       placeholder="0.00"
                       value="<?php echo isset($_POST['precio']) ? $_POST['precio'] : ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="unidad">📏 Unidad de Medida:</label>
                <select id="unidad" name="unidad" required>
                    <option value="">Seleccione una unidad</option>
                    <option value="Unidad" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Unidad') ? 'selected' : ''; ?>>Unidad</option>
                    <option value="Botellón" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Botellón') ? 'selected' : ''; ?>>Botellón</option>
                    <option value="Litro" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Litro') ? 'selected' : ''; ?>>Litro (L)</option>
                    <option value="Galón" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Galón') ? 'selected' : ''; ?>>Galón</option>
                    <option value="Kilogramo" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Kilogramo') ? 'selected' : ''; ?>>Kilogramo (Kg)</option>
                    <option value="Gramo" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Gramo') ? 'selected' : ''; ?>>Gramo (g)</option>
                    <option value="Metro" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Metro') ? 'selected' : ''; ?>>Metro (m)</option>
                    <option value="Caja" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Caja') ? 'selected' : ''; ?>>Caja</option>
                    <option value="Paquete" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Paquete') ? 'selected' : ''; ?>>Paquete</option>
                    <option value="Docena" <?php echo (isset($_POST['unidad']) && $_POST['unidad'] === 'Docena') ? 'selected' : ''; ?>>Docena</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cantidad">📊 Cantidad Inicial en Stock:</label>
                <input type="number" id="cantidad" name="cantidad" min="0" required 
                       placeholder="0"
                       value="<?php echo isset($_POST['cantidad']) ? $_POST['cantidad'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="umbral_bajo">⚠️ Umbral de Stock Bajo:</label>
                <input type="number" id="umbral_bajo" name="umbral_bajo" min="0" required 
                       placeholder="10"
                       value="<?php echo isset($_POST['umbral_bajo']) ? $_POST['umbral_bajo'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="umbral_critico">🚨 Umbral de Stock Crítico:</label>
                <input type="number" id="umbral_critico" name="umbral_critico" min="0" required 
                       placeholder="5"
                       value="<?php echo isset($_POST['umbral_critico']) ? $_POST['umbral_critico'] : ''; ?>">
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
                                    <?php echo (isset($_POST['id_proveedor']) && $_POST['id_proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
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
            <strong>💡 Consejos para productos de agua:</strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <li><strong>Nombre:</strong> Usa nombres descriptivos como "Botellón de Agua 20L", "Agua Purificada 500ml"</li>
                <li><strong>Precio:</strong> Ingresa el precio de venta al público en bolívares (Bs)</li>
                <li><strong>Unidad:</strong> Para agua usa "Botellón", "Litro" o "Unidad" según corresponda</li>
                <li><strong>Stock:</strong> La cantidad inicial que tienes disponible para vender</li>
                <li><strong>Proveedor:</strong> Asocia el producto con su proveedor para mejor control</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Guardar Producto
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

// Validación del precio
document.getElementById('precio').addEventListener('input', function() {
    let value = this.value;
    if (value && !isNaN(value)) {
        console.log('Precio: Bs ' + parseFloat(value).toFixed(2));
    }
});

// Validación del formulario
document.getElementById('productForm').addEventListener('submit', function(e) {
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
    let confirmMessage = `¿Confirmar registro del producto?\n\nNombre: ${nombre}\nPrecio: Bs ${precio.toFixed(2)}\nCantidad: ${cantidad}\nUnidad: ${unidad}`;
    
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