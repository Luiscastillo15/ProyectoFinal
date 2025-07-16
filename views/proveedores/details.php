<?php require_once 'views/layout/header.php'; ?>

<h2>📋 Detalles del Proveedor</h2>

<!-- Información Principal -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>🏭 Información de la Empresa</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>RIF:</strong><br>
            <span style="font-size: 1.2rem; color: #3498db; font-weight: bold;"><?php echo htmlspecialchars($proveedor['RIF']); ?></span>
        </div>
        <div>
            <strong>Nombre de la Empresa:</strong><br>
            <span style="font-size: 1.1rem; color: #2c3e50; font-weight: bold;"><?php echo htmlspecialchars($proveedor['Nombre']); ?></span>
        </div>
        <div>
            <strong>Tipo de Producto:</strong><br>
            <span class="status-indicator status-high">
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
                $icono = $iconos[$proveedor['Tipo_Producto']] ?? '📋';
                echo $icono . ' ' . htmlspecialchars($proveedor['Tipo_Producto']);
                ?>
            </span>
        </div>
        <div>
            <strong>Estado:</strong><br>
            <span class="status-indicator status-high">✅ Activo</span>
        </div>
    </div>
</div>

<!-- Información de Contacto -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>📞 Información de Contacto</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>Persona de Contacto:</strong><br>
            <?php if (!empty($proveedor['Contacto'])): ?>
                <span style="color: #2c3e50; font-size: 1.1rem;">👤 <?php echo htmlspecialchars($proveedor['Contacto']); ?></span>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin contacto registrado</span>
            <?php endif; ?>
        </div>
        
        <div>
            <strong>Teléfono:</strong><br>
            <?php if (!empty($proveedor['Telefono'])): ?>
                <a href="tel:<?php echo $proveedor['Telefono']; ?>" style="color: #27ae60; text-decoration: none; font-size: 1.1rem;">
                    📱 <?php echo htmlspecialchars($proveedor['Telefono']); ?>
                </a>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin teléfono registrado</span>
            <?php endif; ?>
        </div>
        
        <div>
            <strong>Correo Electrónico:</strong><br>
            <?php if (!empty($proveedor['Correo'])): ?>
                <a href="mailto:<?php echo $proveedor['Correo']; ?>" style="color: #3498db; text-decoration: none; font-size: 1.1rem;">
                    📧 <?php echo htmlspecialchars($proveedor['Correo']); ?>
                </a>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin correo registrado</span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($proveedor['Direccion'])): ?>
    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #ecf0f1;">
        <strong>Dirección:</strong><br>
        <span style="color: #7f8c8d; line-height: 1.6;">🏠 <?php echo nl2br(htmlspecialchars($proveedor['Direccion'])); ?></span>
    </div>
    <?php endif; ?>
</div>

<!-- Información Adicional -->
<div class="card" style="margin-bottom: 2rem;">
    <h3>📊 Información del Sistema</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div>
            <strong>ID del Proveedor:</strong><br>
            <span style="color: #7f8c8d;">#<?php echo $proveedor['id_proveedor']; ?></span>
        </div>
        <div>
            <strong>Fecha de Registro:</strong><br>
            <span style="color: #7f8c8d;">
                <?php 
                if (isset($proveedor['Fecha_Registro'])) {
                    echo date('d/m/Y H:i', strtotime($proveedor['Fecha_Registro']));
                } else {
                    echo 'No disponible';
                }
                ?>
            </span>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="card" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
    <h3>⚡ Acciones Rápidas</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <?php if (!empty($proveedor['Telefono'])): ?>
        <a href="tel:<?php echo $proveedor['Telefono']; ?>" 
           class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; text-decoration: none; text-align: center; padding: 1rem; border-radius: 8px; transition: all 0.3s ease;">
            📱 Llamar
        </a>
        <?php endif; ?>
        
        <?php if (!empty($proveedor['Correo'])): ?>
        <a href="mailto:<?php echo $proveedor['Correo']; ?>" 
           class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; text-decoration: none; text-align: center; padding: 1rem; border-radius: 8px; transition: all 0.3s ease;">
            📧 Enviar Email
        </a>
        <?php endif; ?>
        
        <?php 
        // Solo mostrar botón de editar para administradores
        $isAdmin = isset($_SESSION['rol_nombre']) && strtolower($_SESSION['rol_nombre']) === 'administrador';
        if ($isAdmin): 
        ?>
        <a href="index.php?action=proveedores&method=edit&id=<?php echo $proveedor['id_proveedor']; ?>" 
           class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; text-decoration: none; text-align: center; padding: 1rem; border-radius: 8px; transition: all 0.3s ease;">
            ✏️ Editar
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Botones de Navegación -->
<div style="text-align: center; margin-top: 2rem;">
    <a href="index.php?action=proveedores&method=list" class="btn btn-secondary">
        ⬅️ Volver a Proveedores
    </a>
    
    <?php if ($isAdmin): ?>
    <a href="index.php?action=proveedores&method=add" class="btn btn-success" style="margin-left: 1rem;">
        ➕ Agregar Nuevo Proveedor
    </a>
    <?php endif; ?>
</div>

<!-- Información adicional para vendedores -->
<?php if (!$isAdmin): ?>
<div class="alert alert-info" style="margin-top: 2rem;">
    <strong>💡 Información:</strong> Como vendedor, puedes consultar toda la información de los proveedores. 
    Para modificar datos, contacta con el administrador del sistema.
</div>
<?php endif; ?>

<style>
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .card h3 {
        font-size: 1.2rem;
    }
    
    div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once 'views/layout/footer.php'; ?>