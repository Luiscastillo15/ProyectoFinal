<?php require_once 'views/layout/header.php'; ?>

<h2>✏️ Editar Proveedor</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?action=proveedores&method=edit&id=<?php echo $proveedor['id_proveedor']; ?>" method="post" id="proveedorEditForm">
        <div class="form-row">
            <div class="form-group">
                <label for="rif">🏢 RIF:</label>
                <input type="text" id="rif" name="rif" required 
                       value="<?php echo htmlspecialchars($proveedor['RIF']); ?>"
                       placeholder="Ej: J123456789"
                       maxlength="10"
                       style="text-transform: uppercase;">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Formato: J/G/V seguido de 9 dígitos
                </small>
            </div>
            
            <div class="form-group">
                <label for="nombre">🏭 Nombre de la Empresa:</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo htmlspecialchars($proveedor['Nombre']); ?>"
                       placeholder="Nombre de la empresa proveedora">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="contacto">👤 Persona de Contacto:</label>
                <input type="text" id="contacto" name="contacto" 
                       value="<?php echo htmlspecialchars($proveedor['Contacto']); ?>"
                       placeholder="Nombre del contacto principal">
            </div>
            
            <div class="form-group">
                <label for="telefono">📱 Teléfono:</label>
                <input type="text" id="telefono" name="telefono" 
                       value="<?php echo htmlspecialchars($proveedor['Telefono']); ?>"
                       placeholder="Ej: 04121234567"
                       maxlength="11">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Formato: 11 dígitos (04121234567)
                </small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="correo">📧 Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" 
                       value="<?php echo htmlspecialchars($proveedor['Correo']); ?>"
                       placeholder="contacto@empresa.com">
            </div>
            
            <div class="form-group">
                <label for="tipo_producto">💧 Tipo de Producto:</label>
                <select id="tipo_producto" name="tipo_producto" required>
                    <option value="">Seleccione el tipo</option>
                    <option value="Agua Purificada" <?php echo ($proveedor['Tipo_Producto'] === 'Agua Purificada') ? 'selected' : ''; ?>>💧 Agua Purificada</option>
                    <option value="Botellones" <?php echo ($proveedor['Tipo_Producto'] === 'Botellones') ? 'selected' : ''; ?>>🏺 Botellones</option>
                    <option value="Dispensadores" <?php echo ($proveedor['Tipo_Producto'] === 'Dispensadores') ? 'selected' : ''; ?>>🚰 Dispensadores</option>
                    <option value="Filtros" <?php echo ($proveedor['Tipo_Producto'] === 'Filtros') ? 'selected' : ''; ?>>🔧 Filtros y Repuestos</option>
                    <option value="Químicos" <?php echo ($proveedor['Tipo_Producto'] === 'Químicos') ? 'selected' : ''; ?>>⚗️ Químicos de Tratamiento</option>
                    <option value="Envases" <?php echo ($proveedor['Tipo_Producto'] === 'Envases') ? 'selected' : ''; ?>>📦 Envases y Tapas</option>
                    <option value="Equipos" <?php echo ($proveedor['Tipo_Producto'] === 'Equipos') ? 'selected' : ''; ?>>🏭 Equipos de Purificación</option>
                    <option value="Otros" <?php echo ($proveedor['Tipo_Producto'] === 'Otros') ? 'selected' : ''; ?>>📋 Otros</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="direccion">🏠 Dirección:</label>
            <textarea id="direccion" name="direccion" 
                      placeholder="Dirección completa de la empresa"><?php echo htmlspecialchars($proveedor['Direccion']); ?></textarea>
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Información:</strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <li><strong>RIF:</strong> Debe comenzar con J, G o V seguido de 9 dígitos</li>
                <li><strong>Teléfono:</strong> Debe tener exactamente 11 dígitos y comenzar con 04</li>
                <li><strong>Tipo de Producto:</strong> Categoría principal de productos relacionados con agua que suministra</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Actualizar Proveedor
            </button>
            <a href="index.php?action=proveedores&method=list" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Validación en tiempo real para RIF
document.getElementById('rif').addEventListener('input', function() {
    let value = this.value.toUpperCase().replace(/[^JGV0-9]/g, '');
    
    // Asegurar que comience con J, G o V
    if (value.length > 0 && !['J', 'G', 'V'].includes(value[0])) {
        value = 'J' + value.replace(/[JGV]/g, '');
    }
    
    // Limitar a 10 caracteres (1 letra + 9 dígitos)
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    this.value = value;
    
    // Validación visual
    const isValid = validateRif(value);
    this.style.borderColor = isValid ? '#27ae60' : '#e74c3c';
});

// Validación en tiempo real para teléfono
document.getElementById('telefono').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, ''); // Solo números
    
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    this.value = value;
    
    // Validación visual
    const isValid = value.length === 11 && value.startsWith('04');
    this.style.borderColor = isValid || value.length === 0 ? '#27ae60' : '#e74c3c';
});

// Función para validar RIF
function validateRif(value) {
    return /^[JGV]\d{9}$/.test(value);
}

// Función para validar teléfono
function validateTelefono(value) {
    return /^04\d{9}$/.test(value);
}

// Validación del formulario antes de enviar
document.getElementById('proveedorEditForm').addEventListener('submit', function(e) {
    const rif = document.getElementById('rif').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const correo = document.getElementById('correo').value.trim();
    
    let errors = [];
    
    // Validar nombre
    if (nombre.length < 3) {
        errors.push('El nombre debe tener al menos 3 caracteres');
    }
    
    // Validar RIF
    if (!validateRif(rif)) {
        errors.push('El RIF debe tener el formato correcto (J/G/V seguido de 9 dígitos)');
    }
    
    // Validar teléfono (solo si se proporciona)
    if (telefono && !validateTelefono(telefono)) {
        errors.push('El teléfono debe tener 11 dígitos y comenzar con 04');
    }
    
    // Validar correo (solo si se proporciona)
    if (correo && !isValidEmail(correo)) {
        errors.push('El formato del correo electrónico no es válido');
    }
    
    // Mostrar errores si los hay
    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Por favor corrige los siguientes errores:\n\n• ' + errors.join('\n• '));
        return false;
    }
    
    // Confirmación antes de guardar
    const confirmMessage = `¿Confirmar actualización del proveedor?\n\nEmpresa: ${nombre}\nRIF: ${rif}`;
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});

// Función para validar email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Enfocar el primer campo al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nombre').focus();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>