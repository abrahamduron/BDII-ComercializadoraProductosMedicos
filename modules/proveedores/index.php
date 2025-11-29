<?php
// proveedores/index.php
session_start();
require_once '../../config/database.php';
$db = new Database();

$mensaje = '';
$proveedor_editar = null;

// Procesar formularios
if ($_POST) {
    if (isset($_POST['crear_proveedor'])) {
        $result = $db->executeProcedureSingle("sp_Proveedor_Crear", [
            $_POST['codigo_proveedor'],
            $_POST['nombre'],
            $_POST['direccion'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['correo'] ?? '',
            $_POST['limite_credito']
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error: " . $result['error'];
        } elseif (isset($result['id_proveedor'])) {
            $mensaje = "Proveedor creado exitosamente con ID: " . $result['id_proveedor'];
        } else {
            $mensaje = "Proveedor creado exitosamente";
        }
    }
    
    if (isset($_POST['editar_proveedor'])) {
        $result = $db->executeProcedureNoResult("sp_Proveedor_Actualizar", [
            $_POST['id_proveedor'],
            $_POST['codigo_proveedor'],
            $_POST['nombre'],
            $_POST['direccion'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['correo'] ?? '',
            $_POST['limite_credito']
        ]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al actualizar: " . $result['error'];
        } else {
            $mensaje = "Proveedor actualizado exitosamente";
        }
    }
    
    if (isset($_POST['eliminar_proveedor'])) {
        $result = $db->executeProcedureNoResult("sp_Proveedor_Eliminar", [$_POST['id_proveedor']]);
        
        if (isset($result['error'])) {
            $mensaje = "Error al eliminar: " . $result['error'];
        } else {
            $mensaje = "Proveedor eliminado exitosamente";
        }
    }
}

// Obtener proveedor para editar
if (isset($_GET['editar'])) {
    $proveedores = $db->executeProcedure("sp_Proveedor_Leer", [$_GET['editar']]);
    if (!empty($proveedores) && !isset($proveedores['error'])) {
        $proveedor_editar = $proveedores[0];
    }
}

$proveedores = $db->executeProcedure("sp_Proveedor_Leer");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">Comercializadora Médica</div>
            <ul class="nav-menu">
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../clientes/">Clientes</a></li>
                <li><a href="#" class="active">Proveedores</a></li>
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
                <h1>Gestión de Proveedores</h1>
                <button onclick="mostrarModal('modalProveedor')" class="btn btn-primary">
                    Nuevo Proveedor
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Límite Crédito</th>
                        <th>Saldo Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($proveedores['error'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #e74c3c;">
                                Error: <?php echo $proveedores['error']; ?>
                            </td>
                        </tr>
                    <?php elseif (!empty($proveedores)): ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($proveedor['codigo_proveedor']); ?></td>
                            <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($proveedor['correo']); ?></td>
                            <td>L. <?php echo number_format($proveedor['limite_credito'], 2); ?></td>
                            <td>L. <?php echo number_format($proveedor['saldo_actual'], 2); ?></td>
                            <td>
                                <a href="?editar=<?php echo $proveedor['id']; ?>" class="btn btn-primary">Editar</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['id']; ?>">
                                    <button type="submit" name="eliminar_proveedor" class="btn btn-danger" 
                                            onclick="return confirm('¿Está seguro de eliminar este proveedor?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No hay proveedores registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para nuevo/editar proveedor -->
    <div id="modalProveedor" class="modal" style="display: <?php echo $proveedor_editar ? 'block' : 'none'; ?>;">
        <div class="card" style="margin: 5% auto; width: 90%; max-width: 600px;">
            <div class="card-header">
                <h2><?php echo $proveedor_editar ? 'Editar Proveedor' : 'Nuevo Proveedor'; ?></h2>
                <span onclick="cerrarModal('modalProveedor')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <form method="POST" id="formProveedor">
                <?php if ($proveedor_editar): ?>
                    <input type="hidden" name="id_proveedor" value="<?php echo $proveedor_editar['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Código Proveedor: *</label>
                    <input type="text" name="codigo_proveedor" class="form-control" 
                           value="<?php echo $proveedor_editar ? htmlspecialchars($proveedor_editar['codigo_proveedor']) : ''; ?>" 
                           required pattern="[A-Za-z0-9-]+" title="Solo letras, números y guiones">
                </div>
                <div class="form-group">
                    <label>Nombre: *</label>
                    <input type="text" name="nombre" class="form-control" 
                           value="<?php echo $proveedor_editar ? htmlspecialchars($proveedor_editar['nombre']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="form-control" 
                           value="<?php echo $proveedor_editar ? htmlspecialchars($proveedor_editar['direccion']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" name="telefono" class="form-control" 
                           value="<?php echo $proveedor_editar ? htmlspecialchars($proveedor_editar['telefono']) : ''; ?>"
                           pattern="[0-9+\- ]+" title="Solo números, espacios, guiones y el signo +">
                </div>
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" class="form-control" 
                           value="<?php echo $proveedor_editar ? htmlspecialchars($proveedor_editar['correo']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Límite de Crédito: *</label>
                    <input type="number" name="limite_credito" class="form-control" step="0.01" min="0"
                           value="<?php echo $proveedor_editar ? $proveedor_editar['limite_credito'] : '0.00'; ?>" required>
                </div>
                <div class="form-group">
                    <small style="color: #666;">* Campos obligatorios</small>
                </div>
                <div class="form-group">
                    <?php if ($proveedor_editar): ?>
                        <button type="submit" name="editar_proveedor" class="btn btn-success">Actualizar Proveedor</button>
                    <?php else: ?>
                        <button type="submit" name="crear_proveedor" class="btn btn-success">Guardar Proveedor</button>
                    <?php endif; ?>
                    <button type="button" onclick="cerrarModal('modalProveedor')" class="btn btn-danger">Cancelar</button>
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