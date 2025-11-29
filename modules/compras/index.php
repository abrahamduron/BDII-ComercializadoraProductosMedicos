<?php
// compras/index.php
session_start();
require_once '../../config/database.php';
$db = new Database();

$mensaje = '';
$orden_editar = null;

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_orden'])) {
        $result = $db->executeProcedureSingle("sp_OrdenCompra_Crear", [
            $_POST['numero_orden'],
            $_POST['id_proveedor'],
            $_POST['fecha_esperada_entrega'],
            $_POST['observaciones'] ?? ''
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error: " . $result['error'];
        } elseif (isset($result['id_orden_compra'])) {
            $mensaje = "Orden de compra creada exitosamente con ID: " . $result['id_orden_compra'];
            
            // Agregar productos al detalle si se proporcionaron
            if (isset($_POST['productos']) && is_array($_POST['productos'])) {
                $productosAgregados = 0;
                foreach ($_POST['productos'] as $producto) {
                    if (!empty($producto['id_producto']) && !empty($producto['cantidad']) && !empty($producto['precio'])) {
                        $resultDetalle = $db->executeProcedureNoResult("sp_OrdenCompra_AgregarDetalle", [
                            $result['id_orden_compra'],
                            $producto['id_producto'],
                            $producto['cantidad'],
                            $producto['precio']
                        ]);
                        if (!isset($resultDetalle['error'])) {
                            $productosAgregados++;
                        }
                    }
                }
                $mensaje .= " con " . $productosAgregados . " productos agregados";
            }
        } else {
            $mensaje = "Orden de compra creada exitosamente";
        }
    }
    
    if (isset($_POST['aprobar_orden'])) {
        $result = $db->executeProcedureNoResult("sp_OrdenCompra_Aprobar", [$_POST['id_orden_compra']]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al aprobar: " . $result['error'];
        } else {
            $mensaje = "Orden de compra aprobada exitosamente";
        }
    }
}

// Obtener datos para los selects
$proveedores = $db->executeProcedure("sp_Proveedor_Leer");
$productos = $db->executeProcedure("sp_Producto_Leer");
$ordenes = $db->executeProcedure("sp_Consulta_EstadoOrdenesCompra");

// Debug: Verificar estructura de datos
// echo "<pre>"; print_r($ordenes); echo "</pre>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Compras</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">Comercializadora Médica</div>
            <ul class="nav-menu">
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../clientes/">Clientes</a></li>
                <li><a href="../proveedores/">Proveedores</a></li>
                <li><a href="../productos/">Productos</a></li>
                <li><a href="#" class="active">Compras</a></li>
                <li><a href="../ventas/">Ventas</a></li>
                <li><a href="../inventario/">Inventario</a></li>
                <li><a href="../reportes/">Reportes</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php if ($mensaje): ?>
            <div class="alert <?php echo strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h1>Gestión de Compras</h1>
                <button onclick="mostrarModal('modalOrden')" class="btn btn-primary">
                    Nueva Orden de Compra
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Número Orden</th>
                        <th>Proveedor</th>
                        <th>Fecha Orden</th>
                        <th>Fecha Esperada</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($ordenes['error'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #e74c3c;">
                                Error: <?php echo $ordenes['error']; ?>
                            </td>
                        </tr>
                    <?php elseif (!empty($ordenes) && is_array($ordenes)): ?>
                        <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($orden['numero_orden'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($orden['proveedor'] ?? 'N/A'); ?></td>
                            <td><?php echo isset($orden['fecha_orden']) ? date('d/m/Y', strtotime($orden['fecha_orden'])) : 'N/A'; ?></td>
                            <td><?php echo isset($orden['fecha_esperada_entrega']) ? date('d/m/Y', strtotime($orden['fecha_esperada_entrega'])) : 'N/A'; ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    $estado = $orden['estado'] ?? 'PENDIENTE';
                                    switch($estado) {
                                        case 'APROBADA': echo 'success'; break;
                                        case 'PENDIENTE': echo 'warning'; break;
                                        case 'RECIBIDA': echo 'info'; break;
                                        case 'CANCELADA': echo 'danger'; break;
                                        default: echo 'secondary';
                                    }
                                ?>">
                                    <?php echo $estado; ?>
                                </span>
                            </td>
                            <td>L. <?php echo number_format($orden['total'] ?? 0, 2); ?></td>
                            <td>
                                <?php if (($orden['estado'] ?? 'PENDIENTE') == 'PENDIENTE'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id_orden_compra" value="<?php echo $orden['id'] ?? ''; ?>">
                                        <button type="submit" name="aprobar_orden" class="btn btn-success" 
                                                onclick="return confirm('¿Está seguro de aprobar esta orden de compra?')">
                                            Aprobar
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="detalle_orden.php?id=<?php echo $orden['id'] ?? ''; ?>" class="btn btn-primary">Ver Detalle</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No hay órdenes de compra registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nueva orden de compra -->
    <div id="modalOrden" class="modal">
        <div class="card" style="margin: 2% auto; width: 95%; max-width: 900px;">
            <div class="card-header">
                <h2>Nueva Orden de Compra</h2>
                <span onclick="cerrarModal('modalOrden')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST" id="formOrden">
                <div class="form-row">
                    <div class="form-group">
                        <label>Número de Orden: *</label>
                        <input type="text" name="numero_orden" class="form-control" required
                               pattern="[A-Za-z0-9-]+" title="Solo letras, números y guiones">
                    </div>
                    <div class="form-group">
                        <label>Proveedor: *</label>
                        <select name="id_proveedor" class="form-control" required>
                            <option value="">Seleccione un proveedor</option>
                            <?php if (is_array($proveedores) && !isset($proveedores['error'])): ?>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?php echo $proveedor['id'] ?? ''; ?>">
                                        <?php echo htmlspecialchars($proveedor['nombre'] ?? ''); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha Esperada de Entrega: *</label>
                        <input type="date" name="fecha_esperada_entrega" class="form-control" required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Observaciones:</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Productos de la Orden</h3>
                        <button type="button" onclick="agregarProducto()" class="btn btn-secondary">Agregar Producto</button>
                    </div>
                    <div id="productos-container">
                        <!-- Los productos se agregarán aquí dinámicamente -->
                    </div>
                </div>
                
                <div class="form-group">
                    <small style="color: #666;">* Campos obligatorios</small>
                </div>
                <div class="form-group">
                    <button type="submit" name="crear_orden" class="btn btn-success">Crear Orden de Compra</button>
                    <button type="button" onclick="cerrarModal('modalOrden')" class="btn btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let productoCounter = 0;
        
        function mostrarModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            // Limpiar productos al abrir el modal
            document.getElementById('productos-container').innerHTML = '';
            productoCounter = 0;
            // Agregar un producto por defecto
            agregarProducto();
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        }

        function agregarProducto() {
            productoCounter++;
            const container = document.getElementById('productos-container');
            
            const productoHTML = `
                <div class="form-row producto-item" id="producto-${productoCounter}">
                    <div class="form-group">
                        <label>Producto:</label>
                        <select name="productos[${productoCounter}][id_producto]" class="form-control producto-select" required>
                            <option value="">Seleccione un producto</option>
                            <?php if (is_array($productos) && !isset($productos['error'])): ?>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo $producto['id_producto'] ?? ''; ?>" data-precio="<?php echo $producto['precio_compra'] ?? 0; ?>">
                                        <?php echo htmlspecialchars($producto['nombre'] ?? ''); ?> - L. <?php echo number_format($producto['precio_compra'] ?? 0, 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad:</label>
                        <input type="number" name="productos[${productoCounter}][cantidad]" class="form-control cantidad-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario:</label>
                        <input type="number" name="productos[${productoCounter}][precio]" class="form-control precio-input" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" onclick="eliminarProducto(${productoCounter})" class="btn btn-danger" style="margin-top: 8px;">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', productoHTML);
            
            // Agregar event listeners para el cambio de producto
            const selectElement = document.querySelector(`#producto-${productoCounter} .producto-select`);
            const precioInput = document.querySelector(`#producto-${productoCounter} .precio-input`);
            
            selectElement.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const precio = selectedOption.getAttribute('data-precio');
                if (precio && precioInput) {
                    precioInput.value = precio;
                }
            });
        }

        function eliminarProducto(id) {
            const elemento = document.getElementById(`producto-${id}`);
            if (elemento) {
                elemento.remove();
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.pathname);
                }
            }
        }
    </script>
</body>
</html>