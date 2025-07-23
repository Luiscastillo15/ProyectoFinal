<?php require_once 'views/layout/header.php'; ?>

<h2>⚠️ Productos con Stock Bajo</h2>

<!-- <div class="form-container">
    <form action="index.php?action=reportes&method=lowStock" method="get" id="stockForm">
        <div class="form-row">
            <div class="form-group">
                <label for="threshold">📊 Umbral de Stock:</label>
                <input type="number" id="threshold" name="threshold" min="1" max="100" 
                       value="<?php echo isset($threshold) ? $threshold : 10; ?>" 
                       placeholder="Cantidad mínima">
                <small style="color: #7f8c8d; font-size: 0.8rem;">
                    Productos con stock igual o menor a este número
                </small>
            </div>
            <div class="form-group" style="display: flex; align-items: end; gap: 0.5rem;">
                <button type="submit" name="generate_report" class="btn btn-success">🔍 Filtrar</button>
                <?php if (isset($products) && !empty($products)): ?>
                <button type="submit" name="generate_pdf" class="btn btn-warning">📄 Generar PDF</button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div> -->

<?php if (isset($products)): ?>
    <div class="card">
        <h3>📦 Productos con Stock Bajo</h3>
        
        <!-- Estadísticas -->
        <div class="stats-grid" style="margin: 2rem 0;">
            <div class="stat-card">
                <span class="stat-number"><?php echo count($products); ?></span>
                <span class="stat-label">⚠️ Productos Afectados</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['Cantidad'] <= $p['Umbral_Critico']; })); ?></span>
                <span class="stat-label">🚨 Stock Crítico</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">Bs <?php echo number_format(array_sum(array_map(function($p) { return $p['Precio'] * $p['Cantidad']; }, $products)), 2); ?></span>
                <span class="stat-label">💰 Valor Total Stock</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo array_sum(array_column($products, 'Cantidad')); ?></span>
                <span class="stat-label">📊 Unidades Totales</span>
            </div>
        </div>
        
        <?php if (!empty($products)): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Unidad</th>
                            <th>Stock Actual</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><strong>#<?php echo $product['id_producto']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($product['Nombre']); ?></strong></td>
                            <td>Bs <?php echo number_format($product['Precio'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['Unidad']); ?></td>
                            <td>
                                <span style="color: <?php echo $product['Cantidad'] <= $product['Umbral_Critico'] ? '#e74c3c' : '#f39c12'; ?>; font-weight: bold;">
                                    <?php echo $product['Cantidad']; ?>
                                </span>
                            </td>
                            <td><strong>Bs <?php echo number_format($product['Precio'] * $product['Cantidad'], 2); ?></strong></td>
                            <td>
                                <?php if ($product['Cantidad'] <= 5): ?>
                                    <span style="background: #fee; color: #c53030; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        🚨 CRÍTICO
                                    </span>
                                <?php else: ?>
                                    <span style="background: #fffbf0; color: #744210; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        ⚠️ BAJO
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-warning" style="margin-top: 2rem;">
                <strong>💡 Recomendaciones:</strong>
                <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                    <li>Contacta a los proveedores para reabastecer productos críticos</li>
                    <li>Considera promociones para productos con stock bajo</li>
                    <li>Revisa la demanda histórica antes de hacer pedidos</li>
                    <li>Establece alertas automáticas para evitar desabastecimiento</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <strong>🎉 ¡Excelente!</strong> No hay productos con stock bajo según el umbral establecido.
                <br>Todos los productos tienen stock suficiente.
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="index.php?action=productos&method=list" class="btn btn-secondary">📦 Ver Todos los Productos</a>
        <a href="index.php?action=reportes&method=salesByDate" class="btn btn-secondary">📈 Ver Reporte de Ventas</a>
    </div>
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

// Validación del formulario
document.getElementById('stockForm').addEventListener('submit', function(e) {
    // const threshold = parseInt(document.getElementById('threshold').value);
    
    // if (threshold < 1 || threshold > 100) {
    //     e.preventDefault();
    //     alert('⚠️ El umbral debe estar entre 1 y 100.');
    //     return false;
    // }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>