<?php
session_start();
require_once '../../config/database.php';
$db = new Database();

// Obtener categorías
$categorias = $db->executeProcedure("SELECT * FROM categorias_productos");

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_producto'])) {
        $result = $db->executeProcedure("sp_Producto_Crear", [
            $_POST['codigo_producto'],
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['id_categoria'],
            $_POST['tipo'],
            $_POST['stock_minimo'],
            $_POST['precio_compra'],
            $_POST['precio_venta'],
            $_POST['precio_mayorista'],
            $_POST['requiere_receta'] ?? 0
        ]);
        $mensaje = empty($result['error']) ? "Producto creado exitosamente" : $result['error'];
    }
}

$productos = $db->executeProcedure("sp_Producto_Leer");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
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
                <li><a href="#" class="active">Productos</a></li>
                <li><a href="../compras/">Compras</a></li>
                <li><a href="../ventas/">Ventas</a></li>
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
                <h1>Gestión de Productos</h1>
                <button onclick="document.getElementById('modalProducto').style.display='block'" class="btn btn-primary">
                    Nuevo Producto
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Stock</th>
                        <th>Precio Venta</th>
                        <th>Requiere Receta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo $producto['codigo_producto']; ?></td>
                        <td><?php echo $producto['nombre']; ?></td>
                        <td><?php echo $producto['categoria_nombre']; ?></td>
                        <td><?php echo $producto['tipo']; ?></td>
                        <td style="color: <?php echo $producto['cantidad'] <= $producto['stock_minimo'] ? '#e74c3c' : '#27ae60'; ?>">
                            <?php echo $producto['cantidad']; ?>
                        </td>
                        <td>L. <?php echo number_format($producto['precio_venta'], 2); ?></td>
                        <td><?php echo $producto['requiere_receta'] ? 'Sí' : 'No'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nuevo producto -->
    <div id="modalProducto" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="card" style="margin: 2% auto; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;">
            <div class="card-header">
                <h2>Nuevo Producto</h2>
                <span onclick="document.getElementById('modalProducto').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Código Producto:</label>
                        <input type="text" name="codigo_producto" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="id_categoria" class="form-control" required>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select name="tipo" class="form-control" required>
                            <option value="PRODUCTO_TERMINADO">Producto Terminado</option>
                            <option value="MATERIA_PRIMA">Materia Prima</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock Mínimo:</label>
                        <input type="number" name="stock_minimo" class="form-control" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Compra:</label>
                        <input type="number" name="precio_compra" class="form-control" step="0.01" value="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Venta:</label>
                        <input type="number" name="precio_venta" class="form-control" step="0.01" value="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Mayorista:</label>
                        <input type="number" name="precio_mayorista" class="form-control" step="0.01" value="0.00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="requiere_receta" value="1"> Requiere Receta Médica
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit" name="crear_producto" class="btn btn-success">Guardar Producto</button>
                    <button type="button" onclick="document.getElementById('modalProducto').style.display='none'" class="btn btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.onclick = function(event) {
            var modal = document.getElementById('modalProducto');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>