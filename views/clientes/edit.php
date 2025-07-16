<?php require_once 'views/layout/header.php'; ?>

<h2>✏️ Editar Cliente</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?action=clientes&method=edit&id=<?php echo $client['Cedula_Rif']; ?>" method="post" id="clientEditForm">
        <div class="form-row">
            <div class="form-group">
                <label for="cedula_rif">🆔 Cédula/RIF:</label>
                <input type="text" id="cedula_rif" name="cedula_rif" 
                       value="<?php echo htmlspecialchars($client['Cedula_Rif']); ?>" 
                       readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    La cédula/RIF no se puede modificar
                </small>
            </div>
            
            <div class="form-group">
                <label for="nombre">👤 Nombre:</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($client['Nombre']); ?>" required 
                       placeholder="Nombre del cliente">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="apellido">👤 Apellido:</label>
                <input type="text" id="apellido" name="apellido" 
                       value="<?php echo htmlspecialchars($client['Apellido']); ?>" 
                       placeholder="Apellido del cliente">
            </div>
            
            <div class="form-group">
                <label for="telefono">📱 Teléfono:</label>
                <input type="text" id="telefono" name="telefono" 
                       value="<?php echo htmlspecialchars($client['Telefono']); ?>" 
                       placeholder="Ej: 04121234567"
                       maxlength="11">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Formato: 11 dígitos (04121234567)
                </small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="direccion">🏠 Dirección:</label>
            <textarea id="direccion" name="direccion" 
                      placeholder="Dirección completa del cliente"><?php echo htmlspecialchars($client['Direccion']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="correo">📧 Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" 
                   value="<?php echo htmlspecialchars($client['Correo']); ?>" 
                   placeholder="cliente@ejemplo.com">
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Información:</strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <li><strong>Teléfono:</strong> Debe tener exactamente 11 dígitos y comenzar con 04</li>
                <li><strong>Correo:</strong> Debe tener un formato válido de email</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Actualizar Cliente
            </button>
            <a href="index.php?action=clientes&method=list" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<script>

document.getElementById('telefono').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, ''); // Solo números
    
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    this.value = value;
    
    
    const isValid = value.length === 11 && value.startsWith('04');
    this.style.borderColor = isValid || value.length === 0 ? '#27ae60' : '#e74c3c';
});


function validateTelefono(value) {
    return /^04\d{9}$/.test(value);
}


document.getElementById('clientEditForm').addEventListener('submit', function(e) {
    const telefono = document.getElementById('telefono').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    
    let errors = [];
    
   
    if (nombre.length < 2) {
        errors.push('El nombre debe tener al menos 2 caracteres');
    }
    
    
    if (telefono && !validateTelefono(telefono)) {
        errors.push('El teléfono debe tener 11 dígitos y comenzar con 04 (Ej: 04121234567)');
    }
    
    
    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Por favor corrige los siguientes errores:\n\n• ' + errors.join('\n• '));
        return false;
    }
    
   
    const confirmMessage = `¿Confirmar actualización del cliente?\n\nNombre: ${nombre}${telefono ? '\nTeléfono: ' + telefono : ''}`;
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nombre').focus();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>