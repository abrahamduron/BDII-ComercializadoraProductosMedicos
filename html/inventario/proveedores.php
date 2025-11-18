<?php
include '../../conexion.php';

// Manejar el registro de un nuevo proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    $stmt = $conexion->prepare("INSERT INTO proveedores (nombre, telefono, correo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $telefono, $correo);
    $stmt->execute();
}

// Obtener todos los proveedores
$resultado = $conexion->query("SELECT id, nombre, telefono, correo FROM proveedores ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores - Inventario</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<aside class="sidebar">
    <h2>Inventario</h2>
    <ul>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="agregar_producto.php">Agregar Producto</a></li>
        <li><a href="proveedores.php" class="activo">Proveedores</a></li>
        <li><a href="orden_compra.php">Orden de Compra</a></li>
        <li><a href="devoluciones.php">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Proveedores</h1>
    <p>Gestiona los proveedores de tus productos.</p>

    <h2>Agregar Nuevo Proveedor</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="text" id="telefono" name="telefono"><br><br>

        <label for="correo">Correo Electrónico:</label><br>
        <input type="email" id="correo" name="correo"><br><br>

        <button type="submit">Registrar Proveedor</button>
    </form>

    <h2>Lista de Proveedores</h2>
    <?php if($resultado->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id']; ?></td>
                        <td><?php echo $fila['nombre']; ?></td>
                        <td><?php echo $fila['telefono']; ?></td>
                        <td><?php echo $fila['correo']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay proveedores registrados.</p>
    <?php endif; ?>

</main>

</body>
</html>






