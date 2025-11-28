<?php
session_start();
require_once '../../config/database.php';
$db = new Database();

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_cliente'])) {
        $result = $db->executeProcedure("sp_Cliente_Crear", [
            $_POST['codigo_cliente'],
            $_POST['nombre'],
            $_POST['direccion'],
            $_POST['telefono'],
            $_POST['correo'],
            $_POST['tipo_cliente'],
            $_POST['limite_credito']
        ]);
        $mensaje = empty($result['error']) ? "Cliente creado exitosamente" : $result['error'];
    }
    
    if (isset($_POST['eliminar_cliente'])) {
        $result = $db->executeProcedure("sp_Cliente_Eliminar", [$_POST['id_cliente']]);
        $mensaje = "Cliente eliminado exitosamente";
    }
}

$clientes = $db->executeProcedure("sp_Cliente_Leer");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">Comercializadora Médica</div>
            <ul class="nav-menu">
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="#" class="active">Clientes</a></li>
                <li><a href="../proveedores/">Proveedores</a></li>
                <li><a href="../productos/">Productos</a></li>
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
                <h1>Gestión de Clientes</h1>
                <button onclick="document.getElementById('modalCliente').style.display='block'" class="btn btn-primary">
                    Nuevo Cliente
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Límite Crédito</th>
                        <th>Saldo Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['codigo_cliente']; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $cliente['telefono']; ?></td>
                        <td><?php echo $cliente['tipo_cliente']; ?></td>
                        <td>L. <?php echo number_format($cliente['limite_credito'], 2); ?></td>
                        <td>L. <?php echo number_format($cliente['saldo_actual'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id']; ?>">
                                <button type="submit" name="eliminar_cliente" class="btn btn-danger" 
                                        onclick="return confirm('¿Está seguro de eliminar este cliente?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nuevo cliente -->
    <div id="modalCliente" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="card" style="margin: 5% auto; width: 90%; max-width: 600px;">
            <div class="card-header">
                <h2>Nuevo Cliente</h2>
                <span onclick="document.getElementById('modalCliente').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Código Cliente:</label>
                    <input type="text" name="codigo_cliente" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" class="form-control">
                </div>
                <div class="form-group">
                    <label>Tipo Cliente:</label>
                    <select name="tipo_cliente" class="form-control" required>
                        <option value="DETALLE">Detalle</option>
                        <option value="MAYORISTA">Mayorista</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Límite de Crédito:</label>
                    <input type="number" name="limite_credito" class="form-control" step="0.01" value="0.00">
                </div>
                <div class="form-group">
                    <button type="submit" name="crear_cliente" class="btn btn-success">Guardar Cliente</button>
                    <button type="button" onclick="document.getElementById('modalCliente').style.display='none'" class="btn btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            var modal = document.getElementById('modalCliente');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>