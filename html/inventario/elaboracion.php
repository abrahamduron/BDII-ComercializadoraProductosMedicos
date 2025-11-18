<?php
include '../../conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $cantidad = intval($_POST['cantidad']);

    // Registrar elaboración
    $sql = "INSERT INTO productos_elaborados (nombre, descripcion, cantidad)
            VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $cantidad);

    if ($stmt->execute()) {

        // ¿Existe ya en inventario?
        $buscar = $conexion->prepare("SELECT id FROM inventario WHERE nombre = ?");
        $buscar->bind_param("s", $nombre);
        $buscar->execute();
        $buscar->store_result();

        if ($buscar->num_rows > 0) {
            // Si existe, sumamos stock
            $buscar->bind_result($id_encontrado);
            $buscar->fetch();

            $conexion->query("UPDATE inventario SET stock = stock + $cantidad WHERE id = $id_encontrado");

        } else {
            // Si NO existe, lo creamos con stock inicial
            $insertInv = $conexion->prepare("
                INSERT INTO inventario (nombre, categoria, stock, precio)
                VALUES (?, 'Elaborado', ?, 0)
            ");
            $insertInv->bind_param("si", $nombre, $cantidad);
            $insertInv->execute();
            $insertInv->close();
        }

        $mensaje = "Producto elaborado registrado y agregado al inventario.";
    } else {
        $mensaje = "Error al registrar elaboración: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Elaboración de Productos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2>Inventario</h2>
    <ul>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="agregar_producto.php">Agregar Producto</a></li>
        <li><a href="proveedores.php">Proveedores</a></li>
        <li><a href="orden_compra.php">Orden de Compra</a></li>
        <li><a href="devoluciones.php">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php" class="activo">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<main>
    <h1>Elaboración de Productos</h1>
    <p>Registra los productos fabricados internamente y actualiza el inventario automáticamente.</p>

    <?php if ($mensaje != ''): ?>
        <p class="exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario">

        <label>Nombre del producto elaborado:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>

        <label>Cantidad elaborada:</label>
        <input type="number" name="cantidad" min="1" required>

        <button type="submit" class="boton-modulo">Registrar Elaboración</button>

    </form>
</main>

</body>
</html>




