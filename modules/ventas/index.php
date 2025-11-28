<?php
session_start();
require_once '../../config/database.php';
$db = new Database();

// Obtener datos necesarios
$clientes = $db->executeProcedure("sp_Cliente_Leer");
$productos = $db->executeProcedure("sp_Producto_Leer");

// Procesar venta
if ($_POST && isset($_POST['crear_factura'])) {
    // Crear factura
    $id_factura = $db->executeProcedure("sp_Factura_Crear", [
        $_POST['numero_factura'],
        $_POST['id_cliente'],
        $_POST['tipo_venta'],
        $_POST['observaciones']
    ]);
    
    if (!empty($id_factura[0]['id_factura'])) {
        $factura_id = $id_factura[0]['id_factura'];
        
        // Agregar detalles
        $productos_venta = $_POST['productos'];
        foreach ($productos_venta as $producto) {
            if (!empty($producto['id_producto']) && $producto['cantidad'] > 0) {
                $db->executeProcedure("sp_Factura_AgregarDetalle", [
                    $factura_id,
                    $producto['id_producto'],
                    $producto['cantidad'],
                    $producto['precio'],
                    $producto['descuento'] ?? 0
                ]);
            }
        }
        
        $mensaje = "Venta registrada exitosamente - Factura #" . $_POST['numero_factura'];
    } else {
        $mensaje = "Error al crear la factura";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Ventas</title>
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
                <li><a href="../compras/">Compras</a></li>
                <li><a href="#" class="active">Ventas</a></li>
                <li><a href="../inventario/">Inventario</a></li>
                <li><a href="../reportes/">Reportes</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php if (isset($mensaje)): ?>
            <div class="alert <?php echo strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h1>Registro de Ventas</h1>
            </div>

            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Número de Factura:</label>
                        <input type="text" name="numero_factura" class="form-control" value="FAC-<?php echo date('Ymd-His'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cliente:</label>
                        <select name="id_cliente" class="form-control" required>
                            <option value="">Seleccionar Cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Venta:</label>
                        <select name="tipo_venta" class="form-control" required>
                            <option value="CONTADO">Contado</option>
                            <option value="CREDITO">Crédito</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Observaciones:</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>

                <div class="card" style="margin-top: 1rem;">
                    <div class="card-header">
                        <h3>Detalles de la Venta</h3>
                        <button type="button" onclick="agregarProducto()" class="btn btn-primary">Agregar Producto</button>
                    </div>
                    
                    <div id="productos-container">
                        <div class="producto-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 0.5rem; align-items: end; margin-bottom: 1rem;">
                            <div class="form-group">
                                <label>Producto:</label>
                                <select name="productos[0][id_producto]" class="form-control" onchange="actualizarPrecio(this, 0)" required>
                                    <option value="">Seleccionar Producto</option>
                                    <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo $producto['id_producto']; ?>" data-precio="<?php echo $producto['precio_venta']; ?>">
                                        <?php echo $producto['nombre']; ?> (Stock: <?php echo $producto['cantidad']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad:</label>
                                <input type="number" name="productos[0][cantidad]" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Precio Unitario:</label>
                                <input type="number" name="productos[0][precio]" class="form-control precio" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Descuento:</label>
                                <input type="number" name="productos[0][descuento]" class="form-control" step="0.01" value="0.00">
                            </div>
                            <div class="form-group">
                                <label>Subtotal:</label>
                                <input type="text" class="form-control subtotal" readonly value="0.00">
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="removerProducto(this)" class="btn btn-danger">×</button>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #ddd;">
                        <h3>Total: L. <span id="total-venta">0.00</span></h3>
                    </div>
                </div>

                <div class="form-group" style="text-align: center; margin-top: 2rem;">
                    <button type="submit" name="crear_factura" class="btn btn-success" style="padding: 1rem 2rem; font-size: 1.1rem;">
                        Registrar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let productoCount = 1;

        function agregarProducto() {
            const container = document.getElementById('productos-container');
            const nuevoProducto = document.createElement('div');
            nuevoProducto.className = 'producto-item';
            nuevoProducto.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 0.5rem; align-items: end; margin-bottom: 1rem;';
            
            nuevoProducto.innerHTML = `
                <div class="form-group">
                    <label>Producto:</label>
                    <select name="productos[${productoCount}][id_producto]" class="form-control" onchange="actualizarPrecio(this, ${productoCount})" required>
                        <option value="">Seleccionar Producto</option>
                        <?php foreach ($productos as $producto): ?>
                        <option value="<?php echo $producto['id_producto']; ?>" data-precio="<?php echo $producto['precio_venta']; ?>">
                            <?php echo $producto['nombre']; ?> (Stock: <?php echo $producto['cantidad']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cantidad:</label>
                    <input type="number" name="productos[${productoCount}][cantidad]" class="form-control" min="1" value="1" required oninput="calcularSubtotal(this)">
                </div>
                <div class="form-group">
                    <label>Precio Unitario:</label>
                    <input type="number" name="productos[${productoCount}][precio]" class="form-control precio" step="0.01" required oninput="calcularSubtotal(this)">
                </div>
                <div class="form-group">
                    <label>Descuento:</label>
                    <input type="number" name="productos[${productoCount}][descuento]" class="form-control" step="0.01" value="0.00" oninput="calcularSubtotal(this)">
                </div>
                <div class="form-group">
                    <label>Subtotal:</label>
                    <input type="text" class="form-control subtotal" readonly value="0.00">
                </div>
                <div class="form-group">
                    <button type="button" onclick="removerProducto(this)" class="btn btn-danger">×</button>
                </div>
            `;
            
            container.appendChild(nuevoProducto);
            productoCount++;
        }

        function removerProducto(button) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                button.closest('.producto-item').remove();
                calcularTotal();
            }
        }

        function actualizarPrecio(select, index) {
            const precio = select.selectedOptions[0].getAttribute('data-precio');
            const precioInput = select.closest('.producto-item').querySelector('.precio');
            precioInput.value = precio || '0.00';
            calcularSubtotal(select);
        }

        function calcularSubtotal(input) {
            const item = input.closest('.producto-item');
            const cantidad = item.querySelector('input[name*="cantidad"]').value || 0;
            const precio = item.querySelector('.precio').value || 0;
            const descuento = item.querySelector('input[name*="descuento"]').value || 0;
            
            const subtotal = (cantidad * precio) - descuento;
            item.querySelector('.subtotal').value = subtotal.toFixed(2);
            
            calcularTotal();
        }

        function calcularTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('total-venta').textContent = total.toFixed(2);
        }

        // Inicializar cálculos
        document.addEventListener('DOMContentLoaded', function() {
            calcularTotal();
        });
    </script>
</body>
</html>