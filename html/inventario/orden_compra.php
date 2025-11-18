<?php
include '../../conexion.php';

$mensaje = "";

// Obtener lista de proveedores
$proveedores = $conexion->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");

// Obtener lista de productos
$productos = $conexion->query("SELECT id_producto, nombre FROM inventario ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proveedor = intval($_POST['id_proveedor']);
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);
    $precio_unitario = floatval($_POST['precio_unitario']);

    if ($id_proveedor <= 0 || $id_producto <= 0 || $cantidad <= 0 || $precio_unitario <= 0) {
        $mensaje = "Datos inválidos, verifique e intente nuevamente.";
    } else {
        // Insertar orden de compra
        $sql = "INSERT INTO orden_compra (id_proveedor, id_producto, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiid", $id_proveedor, $id_producto, $cantidad, $precio_unitario);

        if ($stmt->execute()) {
            // Actualizar inventario automáticamente
            $conexion->query("UPDATE inventario SET stock = stock + $cantidad WHERE id_producto = $id_producto");
            $mensaje = "Orden de compra registrada y stock actualizado correctamente.";
        } else {
            $mensaje = "Error al registrar orden: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<aside class="sidebar">
    <h2>Inventario</h2>
    <ul>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="agregar_producto.php">Agregar Producto</a></li>
        <li><a href="proveedores.php">Proveedores</a></li>
        <li><a href="orden_compra.php" class="activo">Orden de Compra</a></li>
        <li><a href="devoluciones.php">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Registro de Orden de Compra</h1>
    <p>Registra nuevas órdenes de compra y actualiza automáticamente el inventario.</p>

    <?php if ($mensaje != ""): ?>
        <p class="exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <label>Proveedor:</label>
        <select name="id_proveedor" required>
            <option value="">Seleccione un proveedor</option>
            <?php while ($p = $proveedores->fetch_assoc()): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo $p['nombre']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Producto:</label>
        <select name="id_producto" required>
            <option value="">Seleccione un producto</option>
            <?php while ($prod = $productos->fetch_assoc()): ?>
                <option value="<?php echo $prod['id_producto']; ?>"><?php echo $prod['nombre_producto']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="1" required>

        <label>Precio Unitario:</label>
        <input type="number" step="0.01" name="precio_unitario" min="0.01" required>

        <button type="submit" class="boton-modulo">Registrar Orden</button>
    </form>

</main>

</body>
</html>





