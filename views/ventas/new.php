<?php require_once 'views/layout/header.php'; ?>

<h2>🛒 Nueva Venta</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<?php
    // Request de las tasas de cambio
    $curl = curl_init('https://pydolarve.org/api/v2/dollar');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response_exrate_usd = curl_exec($curl);
    curl_close($curl);
?>

<!-- Selección de Cliente - Mejorada -->
<div class="card">
    <h3>👤 Tipo de Cliente</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
        <!-- Opción 1: Cliente Registrado -->
        <div class="client-option" onclick="selectClientType('registered')" 
             style="padding: 1.5rem; border: 2px solid #ecf0f1; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center; background: linear-gradient(135deg, #f8f9fa, #ffffff);"
             data-type="registered">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">👤</div>
            <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Cliente Registrado</h4>
            <p style="color: #7f8c8d; font-size: 0.9rem;">Seleccionar de la base de datos</p>
        </div>
        
        <!-- Opción 2: Venta Directa -->
        <div class="client-option" onclick="selectClientType('direct')" 
             style="padding: 1.5rem; border: 2px solid #ecf0f1; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center; background: linear-gradient(135deg, #f8f9fa, #ffffff);"
             data-type="direct">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🛒</div>
            <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Venta Directa</h4>
            <p style="color: #7f8c8d; font-size: 0.9rem;">Sin registrar cliente</p>
        </div>
    </div>
    
    <!-- Formulario de Cliente Registrado -->
    <div id="registered-client-form" style="display: none;">
        <form action="index.php?action=ventas&method=new" method="post" id="client-form">
            <input type="hidden" name="client_type" value="registered">
            <div class="form-row">
                <div class="form-group">
                    <label for="client_search">🔍 Buscar Cliente por Cédula/RIF:</label>
                    <input type="text" id="client_search" placeholder="Ingrese cédula o RIF para buscar..." 
                           style="margin-bottom: 0.5rem;">
                    <div id="search_results" style="display: none; background: white; border: 1px solid #ddd; border-radius: 8px; max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
                </div>
                
                <div class="form-group">
                    <label for="cedula_rif">Cliente Seleccionado:</label>
                    <select id="cedula_rif" name="cedula_rif">
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['Cedula_Rif']; ?>" 
                                    <?php echo (isset($_POST['cedula_rif']) && $_POST['cedula_rif'] == $client['Cedula_Rif']) ? 'selected' : ''; ?>>
                                <?php echo $client['Cedula_Rif'] . ' - ' . $client['Nombre'] . ' ' . $client['Apellido']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Confirmación de Venta Directa -->
    <div id="direct-sale-form" style="display: none;">
        <div class="alert alert-info">
            <strong>🛒 Venta Directa Seleccionada</strong><br>
            Esta venta se registrará sin asociar a un cliente específico.
        </div>
        <input type="hidden" id="direct_sale_flag" value="true">
    </div>
</div>

<!-- Agregar Productos -->
<div class="card">
    <h3>📦 Agregar Productos al Carrito</h3>
    <form action="index.php?action=ventas&method=new" method="post" id="product-form">
        <!-- Campos ocultos para mantener el estado -->
        <input type="hidden" name="client_type" id="hidden_client_type" value="<?php echo isset($_POST['client_type']) ? $_POST['client_type'] : ''; ?>">
        <input type="hidden" name="cedula_rif" id="hidden_cedula_rif" value="<?php echo isset($_POST['cedula_rif']) ? $_POST['cedula_rif'] : ''; ?>">
        <input type="hidden" name="scroll_position" id="scroll_position">
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_search">🔍 Buscar Producto:</label>
                <input type="text" id="product_search" placeholder="Buscar por nombre o código...">
            </div>
            <div class="form-group">
                <label for="product_id">Producto:</label>
                <select id="product_id" name="product_id" required>
                    <option value="">Seleccione un producto</option>
                    <?php foreach ($products as $product): ?>
                        <?php if ($product['Cantidad'] > 0): ?>
                            <option value="<?php echo $product['id_producto']; ?>" 
                                    data-price="<?php echo $product['Precio']; ?>"
                                    data-stock="<?php echo $product['Cantidad']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['Nombre']); ?>">
                                <?php echo $product['Nombre'] . ' - Bs ' . number_format($product['Precio'], 2) . ' - Stock: ' . $product['Cantidad']; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Cantidad:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required>
            </div>
            <div class="form-group" style="display: flex; align-items: end;">
                <button type="submit" name="add_to_cart" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);">
                    ➕ Agregar al Carrito
                </button>
            </div>
        </div>
        
        <div id="product-info" style="display: none; margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div>
                    <strong>Producto:</strong><br>
                    <span id="product-name">-</span>
                </div>
                <div>
                    <strong>Precio:</strong><br>
                    Bs <span id="product-price">0.00</span>
                </div>
                <div>
                    <strong>Stock:</strong><br>
                    <span id="product-stock">0</span> unidades
                </div>
                <div>
                    <strong>Subtotal:</strong><br>
                    <span style="color: #27ae60; font-weight: bold;">Bs <span id="subtotal">0.00</span></span>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Carrito de Compra -->
<div class="cart-container">
    <h3>🛒 Carrito de Compra</h3>
    
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unit.</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $index => $item): 
                        $total += $item['subtotal'];
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td>
                            <td>Bs <?php echo number_format($item['precio'], 2); ?></td>
                            <td><?php echo $item['cantidad']; ?></td>
                            <td><strong style="color: #27ae60;">Bs <?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            <td>
                                <form action="index.php?action=ventas&method=new" method="post" style="display: inline;">
                                    <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                    <input type="hidden" name="client_type" value="<?php echo isset($_POST['client_type']) ? $_POST['client_type'] : ''; ?>">
                                    <input type="hidden" name="cedula_rif" value="<?php echo isset($_POST['cedula_rif']) ? $_POST['cedula_rif'] : ''; ?>">
                                    <button type="submit" name="remove_item" class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.8rem; transition: all 0.3s ease;"
                                            onclick="return confirm('¿Eliminar este producto del carrito?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-total">
            💰 Total de la Venta: Bs <?php echo number_format($total, 2); ?>
        </div>

        <!-- Finalizar Venta - Interfaz Mejorada y Compacta -->
        <div class="card" style="margin-top: 1.5rem; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: 2px solid #3498db;">
            <h3 style="color: #2c3e50; text-align: center; margin-bottom: 1.5rem;">💳 Finalizar Venta</h3>
            
            <form action="index.php?action=ventas&method=new" method="post" id="finalize-form">
                <input type="hidden" name="final_total" value="<?php echo $total; ?>">
                <input type="hidden" name="client_type" value="<?php echo isset($_POST['client_type']) ? $_POST['client_type'] : ''; ?>">
                
                <!-- Método de Pago - Compacto -->
                <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #3498db;">
                    <h4 style="color: #2c3e50; margin-bottom: 0.8rem; font-size: 1rem;">💳 Método de Pago</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.8rem;">
                        <label class="payment-method-option" style="display: flex; align-items: center; padding: 0.8rem; border: 2px solid #ecf0f1; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                            <input type="radio" name="payment_method" value="Efectivo" required style="margin-right: 0.5rem;">
                            <span>💵 Efectivo</span>
                        </label>
                        <label class="payment-method-option" style="display: flex; align-items: center; padding: 0.8rem; border: 2px solid #ecf0f1; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                            <input type="radio" name="payment_method" value="Transferencia" required style="margin-right: 0.5rem;">
                            <span>🏦 Transferencia</span>
                        </label>
                        <label class="payment-method-option" style="display: flex; align-items: center; padding: 0.8rem; border: 2px solid #ecf0f1; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                            <input type="radio" name="payment_method" value="Tarjeta" required style="margin-right: 0.5rem;">
                            <span>💳 Tarjeta</span>
                        </label>
                        <label class="payment-method-option" style="display: flex; align-items: center; padding: 0.8rem; border: 2px solid #ecf0f1; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                            <input type="radio" name="payment_method" value="Divisas" required style="margin-right: 0.5rem;">
                            <span>💱 Divisas</span>
                        </label>
                    </div>
                </div>
                
                <!-- Monto Recibido - Más Compacto -->
                <div id="amount_paid_container" style="display: none; background: white; padding: 0.8rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #27ae60;">
                    <h4 style="color: #2c3e50; margin-bottom: 0.5rem; font-size: 0.9rem;">💰 Monto Recibido</h4>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="amount_paid" style="font-size: 0.8rem; margin-bottom: 0.3rem;">Cantidad que paga el cliente (Bs):</label>
                        <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" 
                               value="<?php echo $total; ?>" required 
                               style="font-size: 1rem; padding: 0.5rem; text-align: center; font-weight: bold; height: 40px;">
                    </div>
                </div>
                
                <!-- Cliente Final -->
                <input type="hidden" id="final_cedula_rif" name="cedula_rif" value="">
                
                <!-- Calculadora de Vuelto para Efectivo - Compacta -->
                <div id="change_calculator" style="display: none; background: white; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #f39c12;">
                    <h4 style="color: #2c3e50; margin-bottom: 0.8rem; font-size: 1rem;">💰 Cálculo de Vuelto</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.8rem;">
                        <div style="text-align: center; padding: 0.8rem; background: #f8f9fa; border-radius: 6px;">
                            <strong style="font-size: 0.8rem;">Total a pagar:</strong><br>
                            <span style="color: #3498db; font-size: 1.2rem; font-weight: bold;">Bs <?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="text-align: center; padding: 0.8rem; background: #f8f9fa; border-radius: 6px;">
                            <strong style="font-size: 0.8rem;">Cliente paga:</strong><br>
                            <span id="calc_amount_paid" style="color: #27ae60; font-size: 1.2rem; font-weight: bold;">Bs 0.00</span>
                        </div>
                        <div style="text-align: center; padding: 0.8rem; background: #f8f9fa; border-radius: 6px;">
                            <strong style="font-size: 0.8rem;">Vuelto:</strong><br>
                            <span id="calc_change" style="color: #e74c3c; font-size: 1.2rem; font-weight: bold;">Bs 0.00</span>
                        </div>
                    </div>
                    
                    <div id="change_alert" style="margin-top: 0.8rem; padding: 0.8rem; border-radius: 6px; text-align: center; font-size: 0.9rem;">
                        <!-- Aquí se mostrará si hay vuelto o si falta dinero -->
                    </div>
                </div>
                
                <!-- Campos específicos para divisas - Compactos -->
                <div id="divisas_fields" style="display: none; background: white; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #9b59b6;">
                    <div class="alert alert-info" style="margin-bottom: 1rem; padding: 0.8rem; font-size: 0.9rem;">
                        <strong>💱 Pago en Divisas</strong><br>
                        Complete la información de la tasa de cambio y el monto en divisas.
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="divisas_amount" style="font-size: 0.9rem;">💵 Monto en Divisas (USD):</label>
                            <input type="number" id="divisas_amount" name="divisas_amount" step="0.01" min="0" 
                                   placeholder="Ej: 10.50" style="font-size: 1rem; padding: 0.6rem;">
                        </div>
                        
                        <div class="form-group">
                            <label for="exchange_rate" style="font-size: 0.9rem;">💱 Tasa de Cambio (Bs por USD):</label>
                            <input type="number" id="exchange_rate" name="exchange_rate" step="0.01" min="0" 
                                   placeholder="Ej: 36.50" style="font-size: 1rem; padding: 0.6rem;" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="exchange_time" style="font-size: 0.9rem;">🕐 Hora:</label>
                        <input type="time" id="exchange_time" name="exchange_time" value="<?php echo date('H:i'); ?>" style="padding: 0.6rem;">
                    </div>
                    
                    <!-- Calculadora de divisas - Compacta -->
                    <div id="divisas_calculator" style="display: none; background: #f8f9fa; padding: 1rem; border-radius: 6px; margin: 1rem 0;">
                        <h4 style="color: #2c3e50; margin-bottom: 0.8rem; font-size: 1rem;">🧮 Cálculo de Divisas</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.8rem;">
                            <div style="text-align: center; padding: 0.8rem; background: white; border-radius: 6px;">
                                <strong style="font-size: 0.8rem;">Total a pagar:</strong><br>
                                <span style="color: #3498db; font-size: 1.1rem;">Bs <?php echo number_format($total, 2); ?></span>
                            </div>
                            <div style="text-align: center; padding: 0.8rem; background: white; border-radius: 6px;">
                                <strong style="font-size: 0.8rem;">Monto en divisas:</strong><br>
                                <span id="calc_divisas_amount" style="color: #27ae60; font-size: 1.1rem;">$0.00</span>
                            </div>
                            <div style="text-align: center; padding: 0.8rem; background: white; border-radius: 6px;">
                                <strong style="font-size: 0.8rem;">Equivalente en Bs:</strong><br>
                                <span id="calc_bs_equivalent" style="color: #f39c12; font-size: 1.1rem;">Bs 0.00</span>
                            </div>
                            <div style="text-align: center; padding: 0.8rem; background: white; border-radius: 6px;">
                                <strong style="font-size: 0.8rem;">Vuelto en Bs:</strong><br>
                                <span id="calc_divisas_change" style="color: #e74c3c; font-size: 1.1rem; font-weight: bold;">Bs 0.00</span>
                            </div>
                        </div>
                        
                        <div id="divisas_change_alert" style="display: none; margin-top: 0.8rem; padding: 0.8rem; border-radius: 6px; text-align: center; font-size: 0.9rem;">
                            <!-- Aquí se mostrará si hay vuelto o si falta dinero -->
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <button disabled type="submit" name="finalize_sale" class="btn" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; border: none; font-size: 1.1rem; padding: 1rem 2.5rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);">
                        ✅ Finalizar Venta
                    </button>
                    <a href="index.php?action=ventas&method=new" class="btn" style="background: linear-gradient(135deg, #95a5a6, #7f8c8d); color: white; border: none; margin-left: 1rem; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                        🔄 Limpiar Carrito
                    </a>
                </div>
            </form>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: #7f8c8d;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🛒</div>
            <h4>El carrito está vacío</h4>
            <p>Agrega productos para comenzar una nueva venta</p>
        </div>
    <?php endif; ?>
</div>

<script>
const saleTotal = <?php echo $total ?? 0; ?>;
const clientsData = <?php echo json_encode($clients); ?>;
let selectedClientType = '<?php echo isset($_POST['client_type']) ? $_POST['client_type'] : ''; ?>';

// Restaurar el estado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Restaurar tipo de cliente seleccionado
    if (selectedClientType) {
        selectClientType(selectedClientType, false);
        
        // Si hay un cliente seleccionado, también restaurarlo
        const selectedClient = '<?php echo isset($_POST['cedula_rif']) ? $_POST['cedula_rif'] : ''; ?>';
        if (selectedClient) {
            document.getElementById('final_cedula_rif').value = selectedClient;
            document.getElementById('hidden_cedula_rif').value = selectedClient;
            if (selectedClientType === 'registered') {
                document.getElementById('cedula_rif').value = selectedClient;
            }
        }
    }

    document.scrollingElement.scrollTop = <?php echo isset($_POST['scroll_position']) ? $_POST['scroll_position'] : '0'; ?>;
    document.getElementById('exchange_rate').value = JSON.parse(`<?php echo $response_exrate_usd ?>`).monitors.bcv.price.toFixed(2);
});

// Selección de tipo de cliente
function selectClientType(type, updateHidden = true) {
    selectedClientType = type;
    
    // Resetear estilos
    document.querySelectorAll('.client-option').forEach(option => {
        option.style.borderColor = '#ecf0f1';
        option.style.background = 'linear-gradient(135deg, #f8f9fa, #ffffff)';
        option.style.transform = 'scale(1)';
    });
    
    // Aplicar estilo seleccionado
    const selectedOption = document.querySelector(`[data-type="${type}"]`);
    if (selectedOption) {
        selectedOption.style.borderColor = '#3498db';
        selectedOption.style.background = 'linear-gradient(135deg, #e3f2fd, #f8f9fa)';
        selectedOption.style.transform = 'scale(1.02)';
        selectedOption.style.boxShadow = '0 4px 12px rgba(52, 152, 219, 0.2)';
    }
    
    // Actualizar campos ocultos
    if (updateHidden) {
        document.getElementById('hidden_client_type').value = type;
        
        // Actualizar todos los formularios con el tipo de cliente
        document.querySelectorAll('input[name="client_type"]').forEach(input => {
            input.value = type;
        });
    }
    
    if (type === 'registered') {
        document.getElementById('registered-client-form').style.display = 'block';
        document.getElementById('direct-sale-form').style.display = 'none';
        if (document.getElementById('final_cedula_rif')) document.getElementById('final_cedula_rif').value = '';
        document.getElementById('hidden_cedula_rif').value = '';
    } else {
        document.getElementById('registered-client-form').style.display = 'none';
        document.getElementById('direct-sale-form').style.display = 'block';
        if (document.getElementById('final_cedula_rif')) document.getElementById('final_cedula_rif').value = 'VENTA_DIRECTA';
        document.getElementById('hidden_cedula_rif').value = 'VENTA_DIRECTA';
        
        // Actualizar todos los campos ocultos de cedula_rif
        document.querySelectorAll('input[name="cedula_rif"]').forEach(input => {
            if (input.type === 'hidden') {
                input.value = 'VENTA_DIRECTA';
            }
        });
    }
}

// Búsqueda de clientes por cédula/RIF
document.getElementById('client_search').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    const resultsDiv = document.getElementById('search_results');
    
    if (searchTerm.length >= 3) {
        const filteredClients = clientsData.filter(client => 
            client.Cedula_Rif.toLowerCase().includes(searchTerm.toLowerCase()) ||
            client.Nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
            client.Apellido.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        if (filteredClients.length > 0) {
            let html = '';
            filteredClients.forEach(client => {
                html += `
                    <div style="padding: 0.8rem; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" 
                         onclick="selectClient('${client.Cedula_Rif}', '${client.Nombre} ${client.Apellido}')"
                         onmouseover="this.style.background='#f8f9fa'" 
                         onmouseout="this.style.background='white'">
                        <strong>${client.Cedula_Rif}</strong> - ${client.Nombre} ${client.Apellido}
                        ${client.Telefono ? '<br><small style="color: #7f8c8d;">📱 ' + client.Telefono + '</small>' : ''}
                    </div>
                `;
            });
            resultsDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
        } else {
            resultsDiv.innerHTML = '<div style="padding: 1rem; text-align: center; color: #7f8c8d;">No se encontraron clientes</div>';
            resultsDiv.style.display = 'block';
        }
    } else {
        resultsDiv.style.display = 'none';
    }
});

// Función para seleccionar cliente
function selectClient(cedula, nombre) {
    document.getElementById('cedula_rif').value = cedula;
    document.getElementById('final_cedula_rif').value = cedula;
    document.getElementById('hidden_cedula_rif').value = cedula;
    document.getElementById('client_search').value = cedula + ' - ' + nombre;
    document.getElementById('search_results').style.display = 'none';
    
    // Actualizar todos los campos ocultos de cedula_rif
    document.querySelectorAll('input[name="cedula_rif"]').forEach(input => {
        if (input.type === 'hidden') {
            input.value = cedula;
        }
    });
}

// Ocultar resultados al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('#client_search') && !e.target.closest('#search_results')) {
        document.getElementById('search_results').style.display = 'none';
    }
});

// Sincronizar selección de cliente
document.getElementById('cedula_rif').addEventListener('change', function() {
    if (document.getElementById('final_cedula_rif')) document.getElementById('final_cedula_rif').value = this.value;
    document.getElementById('hidden_cedula_rif').value = this.value;
    
    // Actualizar todos los campos ocultos de cedula_rif
    document.querySelectorAll('input[name="cedula_rif"]').forEach(input => {
        if (input.type === 'hidden') {
            input.value = this.value;
        }
    });
});

// Búsqueda de productos
document.getElementById('product_search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const select = document.getElementById('product_id');
    const options = select.options;
    
    let validOptions = 0;
    for (let i = 1; i < options.length; i++) {
        const text = options[i].text.toLowerCase();
        options[i].style.display = text.includes(searchTerm) ? '' : 'none';
        if (options[i].style.display !== 'none') {
            validOptions++;
        }
    }

    if (validOptions === 1) {
        // Si solo queda un producto visible, seleccionarlo automáticamente
        for (let i = 1; i < options.length; i++) {
            if (options[i].style.display !== 'none') {
                select.selectedIndex = i;
                document.getElementById('product_id').dispatchEvent(new Event('change'));
                break;
            }
        }
    } else {
        // Si hay más de un producto visible, no seleccionar ninguno
        select.selectedIndex = 0;
        document.getElementById('product-info').style.display = 'none';
    }

});

// Información del producto seleccionado
document.getElementById('product_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const productInfo = document.getElementById('product-info');
    
    if (selectedOption.value) {
        const price = parseFloat(selectedOption.dataset.price);
        const stock = parseInt(selectedOption.dataset.stock);
        const name = selectedOption.dataset.name;
        
        document.getElementById('product-name').textContent = name;
        document.getElementById('product-price').textContent = price.toFixed(2);
        document.getElementById('product-stock').textContent = stock;
        document.getElementById('quantity').max = stock;
        
        updateSubtotal();
        productInfo.style.display = 'block';
    } else {
        productInfo.style.display = 'none';
    }
});

function updateSubtotal() {
    const select = document.getElementById('product_id');
    const quantity = document.getElementById('quantity');
    const subtotalSpan = document.getElementById('subtotal');
    
    if (select.selectedIndex > 0) {
        const price = parseFloat(select.options[select.selectedIndex].dataset.price);
        const qty = parseInt(quantity.value) || 0;
        const subtotal = price * qty;
        
        subtotalSpan.textContent = subtotal.toFixed(2);
    }
}

document.getElementById('quantity').addEventListener('input', updateSubtotal);

// Estilos para métodos de pago
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Resetear estilos
        document.querySelectorAll('.payment-method-option').forEach(option => {
            option.style.borderColor = '#ecf0f1';
            option.style.backgroundColor = 'white';
            option.style.transform = 'scale(1)';
            option.style.boxShadow = 'none';
        });
        
        // Aplicar estilo seleccionado
        this.closest('.payment-method-option').style.borderColor = '#3498db';
        this.closest('.payment-method-option').style.backgroundColor = '#f8f9fa';
        this.closest('.payment-method-option').style.transform = 'scale(1.02)';
        this.closest('.payment-method-option').style.boxShadow = '0 2px 8px rgba(52, 152, 219, 0.2)';
        
        // Mostrar/ocultar campos específicos
        const divisasFields = document.getElementById('divisas_fields');
        const changeCalculator = document.getElementById('change_calculator');
        const amountPaidField = document.getElementById('amount_paid');
        
        document.getElementById('amount_paid_container').style.display = 'block';
        
        if (this.value === 'Divisas') {
            divisasFields.style.display = 'block';
            changeCalculator.style.display = 'none';
            amountPaidField.readOnly = true;
            amountPaidField.style.backgroundColor = '#f8f9fa';
            amountPaidField.style.cursor = 'not-allowed';
        } else {
            divisasFields.style.display = 'none';
            changeCalculator.style.display = 'none';
            amountPaidField.readOnly = true;
            amountPaidField.style.backgroundColor = '#f8f9fa';
            amountPaidField.style.cursor = 'not-allowed';
            amountPaidField.value = saleTotal.toFixed(2);
            document.getElementById('divisas_calculator').style.display = 'none';
            updateChangeCalculation();
        }
    });
});

// Calculadora de vuelto para efectivo
function updateChangeCalculation() {
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    const change = amountPaid - saleTotal;
    
    document.getElementById('calc_amount_paid').textContent = 'Bs ' + amountPaid.toFixed(2);
    document.getElementById('calc_change').textContent = 'Bs ' + change.toFixed(2);
    
    const changeAlert = document.getElementById('change_alert');
    changeAlert.style.display = 'block';

    const submitBtn = document.querySelector('button[name="finalize_sale"]');
        console.log(submitBtn);
    
    if (amountPaid > saleTotal) {
        changeAlert.style.background = '#d4edda';
        changeAlert.style.color = '#155724';
        changeAlert.style.border = '1px solid #c3e6cb';
        changeAlert.innerHTML = `
            <strong>💰 Vuelto a entregar: Bs ${change.toFixed(2)}</strong><br>
            <small>El cliente paga Bs ${amountPaid.toFixed(2)} y debe recibir Bs ${change.toFixed(2)} de vuelto</small>
        `;
        submitBtn.disabled = false;
    } else if (amountPaid < saleTotal) {
        changeAlert.style.background = '#f8d7da';
        changeAlert.style.color = '#721c24';
        changeAlert.style.border = '1px solid #f5c6cb';
        changeAlert.innerHTML = `
            <strong>⚠️ Dinero insuficiente: Faltan Bs ${Math.abs(change).toFixed(2)}</strong><br>
            <small>El cliente necesita pagar Bs ${saleTotal.toFixed(2)} para cubrir el total</small>
        `;
        submitBtn.disabled = true;
    } else {
        changeAlert.style.background = '#d1ecf1';
        changeAlert.style.color = '#0c5460';
        changeAlert.style.border = '1px solid #bee5eb';
        changeAlert.innerHTML = `
            <strong>✅ Pago exacto: No hay vuelto</strong><br>
            <small>El monto cubre exactamente el total de la venta</small>
        `;
        submitBtn.disabled = false;
    }
}

// Calculadora de divisas
function updateDivisasCalculation() {
    const divisasAmount = parseFloat(document.getElementById('divisas_amount').value) || 0;
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 0;
    const calculator = document.getElementById('divisas_calculator');
    
    if (divisasAmount > 0 && exchangeRate > 0) {
        calculator.style.display = 'block';
        
        const bsEquivalent = divisasAmount * exchangeRate;
        const change = bsEquivalent - saleTotal;
        
        document.getElementById('calc_divisas_amount').textContent = '$' + divisasAmount.toFixed(2);
        document.getElementById('calc_bs_equivalent').textContent = 'Bs ' + bsEquivalent.toFixed(2);
        document.getElementById('calc_divisas_change').textContent = 'Bs ' + change.toFixed(2);
        
        document.getElementById('amount_paid').value = bsEquivalent.toFixed(2);
        
        const changeAlert = document.getElementById('divisas_change_alert');
        changeAlert.style.display = 'block';
        const submitBtn = document.querySelector('button[name="finalize_sale"]');
        console.log(submitBtn);
        
        if (bsEquivalent > saleTotal) {
            changeAlert.style.background = '#d4edda';
            changeAlert.style.color = '#155724';
            changeAlert.style.border = '1px solid #c3e6cb';
            changeAlert.innerHTML = `
                <strong>💰 Vuelto a entregar: Bs ${change.toFixed(2)}</strong><br>
                <small>El cliente paga $${divisasAmount.toFixed(2)} y debe recibir Bs ${change.toFixed(2)} de vuelto</small>
            `;
            submitBtn.disabled = false;
        } else if (bsEquivalent < saleTotal) {
            changeAlert.style.background = '#f8d7da';
            changeAlert.style.color = '#721c24';
            changeAlert.style.border = '1px solid #f5c6cb';
            changeAlert.innerHTML = `
                <strong>⚠️ Dinero insuficiente: Faltan Bs ${Math.abs(change).toFixed(2)}</strong><br>
                <small>El cliente necesita pagar $${(saleTotal / exchangeRate).toFixed(2)} para cubrir el total</small>
            `;
            submitBtn.disabled = true;
        } else {
            changeAlert.style.background = '#d1ecf1';
            changeAlert.style.color = '#0c5460';
            changeAlert.style.border = '1px solid #bee5eb';
            changeAlert.innerHTML = `
                <strong>✅ Pago exacto: No hay vuelto</strong><br>
                <small>El monto en divisas cubre exactamente el total de la venta</small>
            `;
            submitBtn.disabled = false;
        }
    } else {
        calculator.style.display = 'none';
    }
}

// Event listeners

// Validación del formulario de productos
document.getElementById('product-form').addEventListener('submit', function(e) {
    if (e.submitter && e.submitter.name === 'add_to_cart') {
        // Verificar que se haya seleccionado un tipo de cliente
        if (!selectedClientType) {
            e.preventDefault();
            alert('⚠️ Debe seleccionar un tipo de cliente antes de agregar productos.');
            return false;
        }
        
        // Si es cliente registrado, verificar que se haya seleccionado uno
        if (selectedClientType === 'registered') {
            const clientSelect = document.getElementById('cedula_rif');
            if (!clientSelect.value) {
                e.preventDefault();
                alert('⚠️ Debe seleccionar un cliente registrado.');
                return false;
            }
        }
        
        const select = document.getElementById('product_id');
        const quantity = document.getElementById('quantity');
        
        if (select.selectedIndex > 0) {
            const maxStock = parseInt(select.options[select.selectedIndex].dataset.stock);
            const requestedQty = parseInt(quantity.value);
            
            if (requestedQty > maxStock) {
                e.preventDefault();
                alert(`Solo hay ${maxStock} unidades disponibles de este producto.`);
                return false;
            }
        }

        document.getElementById('scroll_position').value = document.scrollingElement.scrollTop;
    }
});

// Validación del formulario de finalización
document.getElementById('finalize-form').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    
    if (!paymentMethod) {
        e.preventDefault();
        alert('⚠️ Debe seleccionar un método de pago.');
        return false;
    }
    
    if (!selectedClientType) {
        e.preventDefault();
        alert('⚠️ Debe seleccionar un tipo de cliente.');
        return false;
    }
    
    if (selectedClientType === 'registered') {
        const clientSelect = document.getElementById('cedula_rif');
        if (!clientSelect.value) {
            e.preventDefault();
            alert('⚠️ Debe seleccionar un cliente registrado.');
            return false;
        }
    }
    
    if (paymentMethod.value === 'Efectivo') {
        if (amountPaid < saleTotal) {
            alert(`⚠️ El dinero es insuficiente. Faltan Bs ${Math.abs(amountPaid - saleTotal).toFixed(2)}.`)
            e.preventDefault();
            return false;
        }
    }
    
    // Validaciones específicas para divisas
    if (paymentMethod.value === 'Divisas') {
        const divisasAmount = parseFloat(document.getElementById('divisas_amount').value) || 0;
        const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 0;

        if (amountPaid < saleTotal) {
            alert(`⚠️ El dinero es insuficiente. Faltan Bs ${Math.abs(amountPaid - saleTotal).toFixed(2)}.`)
            e.preventDefault();
            return false;
        }
        
        if (divisasAmount <= 0) {
            e.preventDefault();
            alert('⚠️ Debe ingresar el monto en divisas que paga el cliente.');
            document.getElementById('divisas_amount').focus();
            return false;
        }
        
        if (exchangeRate <= 0) {
            e.preventDefault();
            alert('⚠️ Debe ingresar la tasa de cambio.');
            document.getElementById('exchange_rate').focus();
            return false;
        }
    }
});

document.getElementById('amount_paid').addEventListener('input', function() {
    const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (selectedPaymentMethod && selectedPaymentMethod.value === 'Efectivo') {
        updateChangeCalculation();
    }
});

document.getElementById('divisas_amount').addEventListener('input', updateDivisasCalculation);
document.getElementById('exchange_rate').addEventListener('input', updateDivisasCalculation);
</script>

<?php require_once 'views/layout/footer.php'; ?>