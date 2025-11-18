<?php
include '../../conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $stock = intval($_POST['stock']);
    $precio = floatval($_POST['precio']);

    $sql = "INSERT INTO inventario (nombre, descripcion, cantidad, precio)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssid", $nombre, $categoria, $stock, $precio);

    if ($stmt->execute()) {
        $mensaje = "Producto agregado correctamente.";
    } else {
        $mensaje = "Error al guardar: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2>Inventario</h2>
    <ul>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="agregar_producto.php" class="activo">Agregar Producto</a></li>
        <li><a href="proveedores.php">Proveedores</a></li>
        <li><a href="orden_compra.php">Orden de Compra</a></li>
        <li><a href="devoluciones.php">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<main>
    <h1>Agregar Producto</h1>
    <p>Registra un nuevo producto en el inventario.</p>

    <?php if ($mensaje != ''): ?>
        <p class="exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario">

        <label>Nombre del Producto:</label>
        <input type="text" name="nombre" required>

        <label>Categoría:</label>
        <input type="text" name="categoria" required>

        <label>Cantidad en Stock:</label>
        <input type="number" name="stock" required min="0">

        <label>Precio:</label>
        <input type="number" step="0.01" name="precio" required min="0">

        <button type="submit" class="boton-modulo">Guardar Producto</button>
    </form>
</main>

</body>
</html>






