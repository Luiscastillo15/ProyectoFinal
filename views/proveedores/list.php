<?php require_once 'views/layout/header.php'; ?>

<h2>🏭 Gestión de Proveedores</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php
// Verificar productos con stock crítico
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= 5");
$stmt->execute();
$criticalStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= 10 AND s.Cantidad > 5");
$stmt->execute();
$lowStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener productos con stock crítico para mostrar detalles
$stmt = $db->prepare("SELECT p.Nombre, s.Cantidad FROM Producto p LEFT JOIN Stock s ON p.id_producto = s.id_producto WHERE s.Cantidad <= 5 ORDER BY s.Cantidad ASC LIMIT 5");
$stmt->execute();
$criticalProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Alerta de Stock Crítico -->
<?php if ($criticalStockCount > 0 || $lowStockCount > 0): ?>
<div class="alert" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); border-left: 4px solid #f39c12; margin-bottom: 2rem; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(243, 156, 18, 0.2);">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <div style="font-size: 2.5rem;">⚠️</div>
        <div>
            <h3 style="color: #856404; margin: 0; font-size: 1.3rem;">¡Atención! Stock Crítico Detectado</h3>
            <p style="color: #856404; margin: 0.5rem 0 0 0; font-size: 1rem;">Es necesario contactar a los proveedores para reabastecer productos</p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0;">
        <?php if ($criticalStockCount > 0): ?>
        <div style="background: rgba(231, 76, 60, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #e74c3c;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #e74c3c;"><?php echo $criticalStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #c0392b; font-weight: 600;">🚨 Stock Crítico (≤5)</div>
        </div>
        <?php endif; ?>
        
        <?php if ($lowStockCount > 0): ?>
        <div style="background: rgba(243, 156, 18, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #f39c12;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #f39c12;"><?php echo $lowStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #d68910; font-weight: 600;">⚡ Stock Bajo (6-10)</div>
        </div>
        <?php endif; ?>
        
        <div style="background: rgba(52, 152, 219, 0.1); padding: 1rem; border-radius: 8px; border-left: 3px solid #3498db;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;"><?php echo $criticalStockCount + $lowStockCount; ?></div>
            <div style="font-size: 0.9rem; color: #2980b9; font-weight: 600;">📦 Total Afectados</div>
        </div>
    </div>
    
    <?php if (!empty($criticalProducts)): ?>
    <div style="background: rgba(255, 255, 255, 0.8); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <h4 style="color: #856404; margin-bottom: 0.8rem; font-size: 1rem;">🚨 Productos más críticos:</h4>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <?php foreach ($criticalProducts as $product): ?>
            <span style="background: #e74c3c; color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                <?php echo htmlspecialchars($product['Nombre']); ?> (<?php echo $product['Cantidad']; ?>)
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; border: 1px;">
        <a href="index.php?action=proveedores&method=lowStock" 
           class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);">
            📋 Ver Reporte Completo
        </a>
        <a href="index.php?action=productos&method=list" 
           class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
            📦 Ver Inventario
        </a>
        <button class="btn" onclick="showCriticalProductProviders()" style="background: linear-gradient(135deg,rgb(10, 106, 58),rgb(49, 84, 44)); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">📞Contactar</button>        
    </div>
</div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../../controllers/models/ProveedorModel.php';
$proveedorModel = new ProveedorModel($db);
$proveedoresInventario = $proveedorModel->getAllProveedoresConInventarioYCompras();
?>
<!-- CDN Bootstrap 5 solo si no está en el header -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<?php if (!empty($proveedoresInventario)): ?>
    <h3 class="mt-4 mb-3">🏭 Inventario y Compras por Proveedor</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <?php foreach ($proveedoresInventario as $prov): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <?php echo htmlspecialchars($prov['nombre_proveedor']); ?>
                        </h5>
                        <p class="card-text mb-0">
                            <span class="fw-bold">Inventario actual:</span> <?php echo (int)$prov['total_inventario']; ?> unidades<br>
                            <span class="fw-bold">Total vendido:</span> <?php echo (int)$prov['total_vendido']; ?> unidades<br>
                            <span class="fw-bold">Total comprado históricamente:</span> <?php echo (int)$prov['total_comprado']; ?> unidades
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Navegación de Secciones -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="index.php?action=proveedores&method=list" 
       class="btn" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);">
        🏭 Lista de Proveedores
    </a>
    <a href="index.php?action=proveedores&method=lowStock" 
       class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        ⚠️ Productos con Stock Bajo
    </a>
</div>

<div class="search-container">
    <div class="search-row">
        <div>
            <input type="text" id="proveedor-search" placeholder="🔍 Buscar proveedores por nombre, RIF o tipo..." 
                   style="margin-bottom: 0;">
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <select id="tipo-filter" style="padding: 0.5rem;">
                <option value="">Todos los tipos</option>
                <option value="Agua Purificada">💧 Agua Purificada</option>
                <option value="Botellones">🏺 Botellones</option>
                <option value="Dispensadores">🚰 Dispensadores</option>
                <option value="Filtros">🔧 Filtros y Repuestos</option>
                <option value="Químicos">⚗️ Químicos de Tratamiento</option>
                <option value="Envases">📦 Envases y Tapas</option>
                <option value="Equipos">🏭 Equipos de Purificación</option>
                <option value="Otros">📋 Otros</option>
            </select>
            <?php 
            // Solo mostrar botón de agregar para administradores
            $isAdmin = isset($_SESSION['rol_nombre']) && strtolower($_SESSION['rol_nombre']) === 'administrador';
            if ($isAdmin): 
            ?>
            <a href="index.php?action=proveedores&method=add" class="btn btn-success">➕ Agregar Proveedor</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (empty($proveedores)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <h3>🏭 No hay proveedores registrados</h3>
        <p style="color: #7f8c8d; margin: 1rem 0;">
            <?php if ($isAdmin): ?>
                Comienza agregando tu primer proveedor de agua
            <?php else: ?>
                No hay proveedores disponibles en el sistema
            <?php endif; ?>
        </p>
        <?php if ($isAdmin): ?>
        <a href="index.php?action=proveedores&method=add" class="btn btn-success">➕ Agregar Primer Proveedor</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div style="overflow-x: auto;">
            <table id="proveedores-table">
                <thead>
                    <tr>
                        <th>RIF</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Tipo de Producto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $proveedor): ?>
                    <tr data-tipo="<?php echo htmlspecialchars($proveedor['Tipo_Producto']); ?>">
                        <td><strong><?php echo htmlspecialchars($proveedor['RIF']); ?></strong></td>
                        <td><strong><?php echo htmlspecialchars($proveedor['Nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($proveedor['Contacto']); ?></td>
                        <td>
                            <?php if (!empty($proveedor['Telefono'])): ?>
                                <a href="tel:<?php echo $proveedor['Telefono']; ?>" style="color: #27ae60; text-decoration: none;">
                                    📱 <?php echo htmlspecialchars($proveedor['Telefono']); ?>
                                </a>
                            <?php else: ?>
                                <span style="color: #95a5a6;">Sin teléfono</span>
                            <?php endif; ?>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <span class="status-indicator status-high">✅ Activo</span>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="index.php?action=proveedores&method=details&id=<?php echo $proveedor['id_proveedor']; ?>" 
                                   class="action-view">👁️ Ver</a>
                                <?php if ($isAdmin): ?>
                                <a href="index.php?action=proveedores&method=edit&id=<?php echo $proveedor['id_proveedor']; ?>" 
                                   class="action-edit">✏️ Editar</a>
                                <a href="index.php?action=proveedores&method=delete&id=<?php echo $proveedor['id_proveedor']; ?>" 
                                   class="action-delete"
                                   onclick="return confirm('⚠️ ¿Estás seguro de eliminar este proveedor?\n\nProveedor: <?php echo htmlspecialchars($proveedor['Nombre']); ?>\nRIF: <?php echo htmlspecialchars($proveedor['RIF']); ?>\n\nEsta acción no se puede deshacer.')">
                                   🗑️ Eliminar</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid" style="margin-top: 2rem;">
        <div class="stat-card">
            <span class="stat-number"><?php echo count($proveedores); ?></span>
            <span class="stat-label">🏭 Total Proveedores</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo count(array_filter($proveedores, function($p) { return !empty($p['Telefono']); })); ?></span>
            <span class="stat-label">📱 Con Teléfono</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo count(array_filter($proveedores, function($p) { return !empty($p['Correo']); })); ?></span>
            <span class="stat-label">📧 Con Email</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo count(array_unique(array_column($proveedores, 'Tipo_Producto'))); ?></span>
            <span class="stat-label">📦 Tipos Diferentes</span>
        </div>
    </div>

    <?php if (!$isAdmin): ?>
    <div class="alert alert-info" style="margin-top: 2rem;">
        <strong>💡 Información:</strong> Como vendedor, puedes consultar la información de los proveedores pero no modificarla. 
        Solo los administradores pueden agregar, editar o eliminar proveedores.
    </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// Función para mostrar proveedores de productos críticos
function showCriticalProductProviders() {
    // Obtener productos con stock crítico y sus proveedores
    fetch('index.php?action=proveedores&method=getCriticalProviders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.providers.length > 0) {
            showProviderModal(data.providers);
        } else {
            alert('No se encontraron proveedores para los productos con stock crítico.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al obtener información de proveedores.');
    });
}

// Función para mostrar modal con proveedores
function showProviderModal(providers) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    let providersHtml = '';
    providers.forEach(provider => {
        const productos = provider.productos.map(p => `<span style="background: #e74c3c; color: white; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem; margin: 0.1rem;">${p.nombre} (${p.stock})</span>`).join(' ');
        
        providersHtml += `
            <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid #e74c3c;">
                <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.1rem;">🏭 ${provider.nombre}</h4>
                        <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">RIF: ${provider.rif}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        ${provider.telefono ? `<a href="tel:${provider.telefono}" style="background: #27ae60; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">📱 Llamar</a>` : ''}
                        <a href="index.php?action=proveedores&method=details&id=${provider.id}" style="background: #3498db; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">👁️ Ver Detalles</a>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #e74c3c; font-size: 0.9rem;">🚨 Productos con stock crítico:</strong><br>
                    <div style="margin-top: 0.5rem;">${productos}</div>
                </div>
                ${provider.contacto ? `<p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;"><strong>Contacto:</strong> ${provider.contacto}</p>` : ''}
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 15px; max-width: 800px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div style="padding: 2rem; border-bottom: 1px solid #ecf0f1; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 15px 15px 0 0;">
                <h3 style="margin: 0; font-size: 1.5rem; text-align: center;">📞 Proveedores de Productos Críticos</h3>
                <p style="margin: 0.5rem 0 0 0; text-align: center; opacity: 0.9; font-size: 1rem;">Contacta directamente con los proveedores para reabastecer</p>
            </div>
            <div style="padding: 2rem;">
                ${providersHtml}
                <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                    <button onclick="this.closest('.modal-overlay').remove()" style="background: linear-gradient(135deg, #95a5a6, #7f8c8d); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        ✖️ Cerrar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    modal.className = 'modal-overlay';
    document.body.appendChild(modal);
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Búsqueda en tiempo real
document.getElementById('proveedor-search').addEventListener('input', function() {
    filterTable();
});

// Filtro por tipo
document.getElementById('tipo-filter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('proveedor-search').value.toLowerCase();
    const tipoFilter = document.getElementById('tipo-filter').value;
    const table = document.getElementById('proveedores-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const rif = row.cells[0].textContent.toLowerCase();
        const nombre = row.cells[1].textContent.toLowerCase();
        const contacto = row.cells[2].textContent.toLowerCase();
        const telefono = row.cells[3].textContent.toLowerCase();
        const tipo = row.getAttribute('data-tipo');
        
        // Verificar búsqueda de texto
        const matchesSearch = rif.includes(searchTerm) || 
                             nombre.includes(searchTerm) || 
                             contacto.includes(searchTerm) ||
                             telefono.includes(searchTerm) ||
                             tipo.toLowerCase().includes(searchTerm);
        
        // Verificar filtro de tipo
        const matchesTipo = !tipoFilter || tipo === tipoFilter;
        
        if (matchesSearch && matchesTipo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Efectos hover para las filas
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#proveedores-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
        });
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>