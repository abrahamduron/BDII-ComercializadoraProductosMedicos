<?php
// inventario/index.php
session_start();
require_once '../../config/database.php';
$db = new Database();

$mensaje = '';
$producto_editar = null;

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_producto'])) {
        $result = $db->executeProcedureSingle("sp_Producto_Crear", [
            $_POST['codigo_producto'],
            $_POST['nombre'],
            $_POST['descripcion'] ?? '',
            $_POST['id_categoria'],
            $_POST['tipo'],
            $_POST['stock_minimo'],
            $_POST['precio_compra'],
            $_POST['precio_venta'],
            $_POST['precio_mayorista'],
            $_POST['requiere_receta'] ? 1 : 0
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error: " . $result['error'];
        } elseif (isset($result['id_producto'])) {
            $mensaje = "Producto creado exitosamente con ID: " . $result['id_producto'];
        } else {
            $mensaje = "Producto creado exitosamente";
        }
    }
    
    if (isset($_POST['editar_producto'])) {
        $result = $db->executeProcedureNoResult("sp_Producto_Actualizar", [
            $_POST['id_producto'],
            $_POST['codigo_producto'],
            $_POST['nombre'],
            $_POST['descripcion'] ?? '',
            $_POST['id_categoria'],
            $_POST['tipo'],
            $_POST['stock_minimo'],
            $_POST['precio_compra'],
            $_POST['precio_venta'],
            $_POST['precio_mayorista'],
            $_POST['requiere_receta'] ? 1 : 0
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al actualizar: " . $result['error'];
        } else {
            $mensaje = "Producto actualizado exitosamente";
        }
    }
    
    if (isset($_POST['eliminar_producto'])) {
        $result = $db->executeProcedureNoResult("sp_Producto_Eliminar", [$_POST['id_producto']]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al eliminar: " . $result['error'];
        } else {
            $mensaje = "Producto eliminado exitosamente";
        }
    }
}

// Obtener producto para editar
if (isset($_GET['editar'])) {
    $productos = $db->executeProcedure("sp_Producto_Leer", [$_GET['editar']]);
    if (!empty($productos) && !isset($productos['error'])) {
        $producto_editar = $productos[0];
    }
}

// Obtener categorías para el select
$categorias = $db->executeQuery("SELECT * FROM categorias_productos WHERE activo = 1");
$productos = $db->executeProcedure("sp_Producto_Leer");
$stock_bajo = $db->executeProcedure("sp_Producto_StockBajo");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
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
                <li><a href="../ventas/">Ventas</a></li>
                <li><a href="#" class="active">Inventario</a></li>
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
                <h1>Gestión de Inventario</h1>
                <button onclick="mostrarModal('modalProducto')" class="btn btn-primary">
                    Nuevo Producto
                </button>
                <a href="?stock_bajo=1" class="btn btn-warning">Ver Stock Bajo</a>
            </div>

            <?php if (isset($_GET['stock_bajo'])): ?>
                <div class="card">
                    <div class="card-header">
                        <h2>Productos con Stock Bajo</h2>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Diferencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($stock_bajo['error'])): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #e74c3c;">
                                        Error: <?php echo $stock_bajo['error']; ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($stock_bajo)): ?>
                                <?php foreach ($stock_bajo as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['codigo_producto']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td style="color: #e74c3c; font-weight: bold;"><?php echo $producto['cantidad']; ?></td>
                                    <td><?php echo $producto['stock_minimo']; ?></td>
                                    <td style="color: #e74c3c; font-weight: bold;">
                                        <?php echo $producto['stock_minimo'] - $producto['cantidad']; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No hay productos con stock bajo</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Requiere Receta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($productos['error'])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #e74c3c;">
                                Error: <?php echo $productos['error']; ?>
                            </td>
                        </tr>
                    <?php elseif (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['codigo_producto']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'N/A'); ?></td>
                            <td class="<?php echo $producto['cantidad'] <= $producto['stock_minimo'] ? 'text-danger' : ''; ?>">
                                <?php echo $producto['cantidad']; ?>
                            </td>
                            <td>L. <?php echo number_format($producto['precio_compra'], 2); ?></td>
                            <td>L. <?php echo number_format($producto['precio_venta'], 2); ?></td>
                            <td><?php echo $producto['requiere_receta'] ? 'Sí' : 'No'; ?></td>
                            <td>
                                <a href="?editar=<?php echo $producto['id_producto']; ?>" class="btn btn-primary">Editar</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                    <button type="submit" name="eliminar_producto" class="btn btn-danger" 
                                            onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No hay productos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nuevo/editar producto -->
    <div id="modalProducto" class="modal" style="display: <?php echo $producto_editar ? 'block' : 'none'; ?>;">
        <div class="card" style="margin: 5% auto; width: 90%; max-width: 700px;">
            <div class="card-header">
                <h2><?php echo $producto_editar ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
                <span onclick="cerrarModal('modalProducto')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST" id="formProducto">
                <?php if ($producto_editar): ?>
                    <input type="hidden" name="id_producto" value="<?php echo $producto_editar['id_producto']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Código Producto: *</label>
                        <input type="text" name="codigo_producto" class="form-control" 
                               value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['codigo_producto']) : ''; ?>" 
                               required pattern="[A-Za-z0-9-]+" title="Solo letras, números y guiones">
                    </div>
                    <div class="form-group">
                        <label>Nombre: *</label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['nombre']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?php echo $producto_editar ? htmlspecialchars($producto_editar['descripcion']) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Categoría: *</label>
                        <select name="id_categoria" class="form-control" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>" 
                                    <?php echo ($producto_editar && $producto_editar['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo: *</label>
                        <select name="tipo" class="form-control" required>
                            <option value="PRODUCTO_TERMINADO" <?php echo ($producto_editar && $producto_editar['tipo'] == 'PRODUCTO_TERMINADO') ? 'selected' : ''; ?>>Producto Terminado</option>
                            <option value="MATERIA_PRIMA" <?php echo ($producto_editar && $producto_editar['tipo'] == 'MATERIA_PRIMA') ? 'selected' : ''; ?>>Materia Prima</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock Mínimo: *</label>
                        <input type="number" name="stock_minimo" class="form-control" min="0" required
                               value="<?php echo $producto_editar ? $producto_editar['stock_minimo'] : '0'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Requiere Receta:</label>
                        <div style="margin-top: 8px;">
                            <input type="checkbox" name="requiere_receta" value="1" 
                                <?php echo ($producto_editar && $producto_editar['requiere_receta']) ? 'checked' : ''; ?>>
                            <label style="display: inline; margin-left: 5px;">Sí</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Precio Compra: *</label>
                        <input type="number" name="precio_compra" class="form-control" step="0.01" min="0" required
                               value="<?php echo $producto_editar ? $producto_editar['precio_compra'] : '0.00'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Precio Venta: *</label>
                        <input type="number" name="precio_venta" class="form-control" step="0.01" min="0" required
                               value="<?php echo $producto_editar ? $producto_editar['precio_venta'] : '0.00'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Precio Mayorista:</label>
                        <input type="number" name="precio_mayorista" class="form-control" step="0.01" min="0"
                               value="<?php echo $producto_editar ? $producto_editar['precio_mayorista'] : '0.00'; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <small style="color: #666;">* Campos obligatorios</small>
                </div>
                <div class="form-group">
                    <?php if ($producto_editar): ?>
                        <button type="submit" name="editar_producto" class="btn btn-success">Actualizar Producto</button>
                    <?php else: ?>
                        <button type="submit" name="crear_producto" class="btn btn-success">Guardar Producto</button>
                    <?php endif; ?>
                    <button type="button" onclick="cerrarModal('modalProducto')" class="btn btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
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