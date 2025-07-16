<?php require_once 'views/layout/header.php'; ?>

<h2>➕ Agregar Nuevo Cliente</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?action=clientes&method=add" method="post" id="clientForm">
        <div class="form-row">
            <div class="form-group">
                <label for="cedula_rif">🆔 Cédula/RIF:</label>
                <input type="text" id="cedula_rif" name="cedula_rif" required 
                       placeholder="Ej: 12345678 o J123456789"
                       maxlength="10">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Cédula: 8 dígitos | RIF: J + 9 dígitos
                </small>
            </div>
            
            <div class="form-group">
                <label for="nombre">👤 Nombre:</label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Nombre del cliente">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="apellido">👤 Apellido:</label>
                <input type="text" id="apellido" name="apellido" 
                       placeholder="Apellido del cliente">
            </div>
            
            <div class="form-group">
                <label for="telefono">📱 Teléfono:</label>
                <input type="text" id="telefono" name="telefono" 
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
                      placeholder="Dirección completa del cliente"></textarea>
        </div>
        
        <div class="form-group">
            <label for="correo">📧 Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" 
                   placeholder="cliente@ejemplo.com">
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Información:</strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <li><strong>Cédula:</strong> Debe tener exactamente 8 dígitos</li>
                <li><strong>RIF:</strong> Debe comenzar con J seguido de 9 dígitos</li>
                <li><strong>Teléfono:</strong> Debe tener exactamente 11 dígitos</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ✅ Guardar Cliente
            </button>
            <a href="index.php?action=clientes&method=list" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Validación en tiempo real para cédula/RIF
document.getElementById('cedula_rif').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, ''); // Solo números
    
    // Si empieza con J, permitir J + números
    if (this.value.toUpperCase().startsWith('J')) {
        value = 'J' + value;
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
    } else {
        // Solo números para cédula
        if (value.length > 8) {
            value = value.substring(0, 8);
        }
    }
    
    this.value = value;
    
    // Validación visual
    const isValid = validateCedulaRif(value);
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

// Función para validar cédula/RIF
function validateCedulaRif(value) {
    if (value.toUpperCase().startsWith('J')) {
        // RIF: J + 9 dígitos
        return /^J\d{9}$/.test(value.toUpperCase());
    } else {
        // Cédula: 8 dígitos
        return /^\d{8}$/.test(value);
    }
}

// Función para validar teléfono
function validateTelefono(value) {
    return /^04\d{9}$/.test(value);
}

// Validación del formulario antes de enviar
document.getElementById('clientForm').addEventListener('submit', function(e) {
    const cedulaRif = document.getElementById('cedula_rif').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    
    let errors = [];
    
    // Validar nombre
    if (nombre.length < 2) {
        errors.push('El nombre debe tener al menos 2 caracteres');
    }
    
    // Validar cédula/RIF
    if (!validateCedulaRif(cedulaRif)) {
        if (cedulaRif.toUpperCase().startsWith('J')) {
            errors.push('El RIF debe tener el formato: J seguido de 9 dígitos (Ej: J123456789)');
        } else {
            errors.push('La cédula debe tener exactamente 8 dígitos (Ej: 12345678)');
        }
    }
    
    // Validar teléfono (solo si se proporciona)
    if (telefono && !validateTelefono(telefono)) {
        errors.push('El teléfono debe tener 11 dígitos y comenzar con 04 (Ej: 04121234567)');
    }
    
    // Mostrar errores si los hay
    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Por favor corrige los siguientes errores:\n\n• ' + errors.join('\n• '));
        return false;
    }
    
    // Confirmación antes de guardar
    const confirmMessage = `¿Confirmar registro del cliente?\n\nNombre: ${nombre}\nCédula/RIF: ${cedulaRif}${telefono ? '\nTeléfono: ' + telefono : ''}`;
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});

// Formatear campos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Enfocar el primer campo
    document.getElementById('cedula_rif').focus();
});
</script>

<?php require_once 'views/layout/footer.php'; ?>