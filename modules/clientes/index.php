<?php
session_start();
require_once '../../config/database.php';
$db = new Database();

$mensaje = '';
$cliente_editar = null;

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_cliente'])) {
        $result = $db->executeProcedureSingle("sp_Cliente_Crear", [
            $_POST['codigo_cliente'],
            $_POST['nombre'],
            $_POST['direccion'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['correo'] ?? '',
            $_POST['tipo_cliente'],
            $_POST['limite_credito']
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error: " . $result['error'];
        } elseif (isset($result['id_cliente'])) {
            $mensaje = "Cliente creado exitosamente con ID: " . $result['id_cliente'];
        } else {
            $mensaje = "Cliente creado exitosamente";
        }
    }
    
    if (isset($_POST['editar_cliente'])) {
        $result = $db->executeProcedureNoResult("sp_Cliente_Actualizar", [
            $_POST['id_cliente'],
            $_POST['codigo_cliente'],
            $_POST['nombre'],
            $_POST['direccion'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['correo'] ?? '',
            $_POST['tipo_cliente'],
            $_POST['limite_credito']
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al actualizar: " . $result['error'];
        } else {
            $mensaje = "Cliente actualizado exitosamente";
        }
    }
    
    if (isset($_POST['eliminar_cliente'])) {
        $result = $db->executeProcedureNoResult("sp_Cliente_Eliminar", [$_POST['id_cliente']]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al eliminar: " . $result['error'];
        } else {
            $mensaje = "Cliente eliminado exitosamente";
        }
    }
}

// Obtener cliente para editar
if (isset($_GET['editar'])) {
    $clientes = $db->executeProcedure("sp_Cliente_Leer", [$_GET['editar']]);
    if (!empty($clientes) && !isset($clientes['error'])) {
        $cliente_editar = $clientes[0];
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
        <?php if ($mensaje): ?>
            <div class="alert <?php echo strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h1>Gestión de Clientes</h1>
                <button onclick="mostrarModal('modalCliente')" class="btn btn-primary">
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
                    <?php if (isset($clientes['error'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #e74c3c;">
                                Error: <?php echo $clientes['error']; ?>
                            </td>
                        </tr>
                    <?php elseif (!empty($clientes)): ?>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['codigo_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['tipo_cliente']); ?></td>
                            <td>L. <?php echo number_format($cliente['limite_credito'], 2); ?></td>
                            <td>L. <?php echo number_format($cliente['saldo_actual'], 2); ?></td>
                            <td>
                                <a href="?editar=<?php echo $cliente['id']; ?>" class="btn btn-primary">Editar</a>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No hay clientes registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nuevo/editar cliente -->
    <div id="modalCliente" class="modal" style="display: <?php echo $cliente_editar ? 'block' : 'none'; ?>;">
        <div class="card" style="margin: 5% auto; width: 90%; max-width: 600px;">
            <div class="card-header">
                <h2><?php echo $cliente_editar ? 'Editar Cliente' : 'Nuevo Cliente'; ?></h2>
                <span onclick="cerrarModal('modalCliente')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST" id="formCliente">
                <?php if ($cliente_editar): ?>
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente_editar['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Código Cliente: *</label>
                    <input type="text" name="codigo_cliente" class="form-control" 
                           value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['codigo_cliente']) : ''; ?>" 
                           required pattern="[A-Za-z0-9-]+" title="Solo letras, números y guiones">
                </div>
                <div class="form-group">
                    <label>Nombre: *</label>
                    <input type="text" name="nombre" class="form-control" 
                           value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['nombre']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="form-control" 
                           value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['direccion']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" name="telefono" class="form-control" 
                           value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['telefono']) : ''; ?>"
                           pattern="[0-9+\- ]+" title="Solo números, espacios, guiones y el signo +">
                </div>
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" class="form-control" 
                           value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['correo']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Tipo Cliente: *</label>
                    <select name="tipo_cliente" class="form-control" required>
                        <option value="DETALLE" <?php echo ($cliente_editar && $cliente_editar['tipo_cliente'] == 'DETALLE') ? 'selected' : ''; ?>>Detalle</option>
                        <option value="MAYORISTA" <?php echo ($cliente_editar && $cliente_editar['tipo_cliente'] == 'MAYORISTA') ? 'selected' : ''; ?>>Mayorista</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Límite de Crédito: *</label>
                    <input type="number" name="limite_credito" class="form-control" step="0.01" min="0"
                           value="<?php echo $cliente_editar ? $cliente_editar['limite_credito'] : '0.00'; ?>" required>
                </div>
                <div class="form-group">
                    <small style="color: #666;">* Campos obligatorios</small>
                </div>
                <div class="form-group">
                    <?php if ($cliente_editar): ?>
                        <button type="submit" name="editar_cliente" class="btn btn-success">Actualizar Cliente</button>
                    <?php else: ?>
                        <button type="submit" name="crear_cliente" class="btn btn-success">Guardar Cliente</button>
                    <?php endif; ?>
                    <button type="button" onclick="cerrarModal('modalCliente')" class="btn btn-danger">Cancelar</button>
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