<?php
include '../../conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_producto = intval($_POST['id_producto']);
    $id_proveedor = intval($_POST['id_proveedor']);
    $cantidad = intval($_POST['cantidad']);
    $motivo = $_POST['motivo'];

    // Insertar devolución
    $sql = "INSERT INTO devoluciones (id_producto, id_proveedor, cantidad, motivo)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiis", $id_producto, $id_proveedor, $cantidad, $motivo);

    if ($stmt->execute()) {

        // Actualizar inventario RESTANDO la cantidad devuelta
        $conexion->query("UPDATE inventario SET stock = stock - $cantidad WHERE id = $id_producto");

        $mensaje = "Devolución registrada exitosamente.";
    } else {
        $mensaje = "Error al registrar devolución: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devoluciones</title>
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
        <li><a href="devoluciones.php" class="activo">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<main>
    <h1>Devoluciones al Proveedor</h1>
    <p>Registra devoluciones por productos defectuosos, caducados o mal surtidos.</p>

    <?php if ($mensaje != ''): ?>
        <p class="exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario">

        <label>Producto:</label>
        <select name="id_producto" required>
            <option value="">-- Selecciona Producto --</option>
            <?php
            $prod = $conexion->query("SELECT id, nombre, stock FROM inventario ORDER BY nombre ASC");
            while ($p = $prod->fetch_assoc()):
            ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['nombre'] ?> (Stock: <?= $p['stock'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Proveedor:</label>
        <select name="id_proveedor" required>
            <option value="">-- Selecciona Proveedor --</option>
            <?php
            $prov = $conexion->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
            while ($pr = $prov->fetch_assoc()):
            ?>
                <option value="<?= $pr['id'] ?>"><?= $pr['nombre'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Cantidad a devolver:</label>
        <input type="number" name="cantidad" min="1" required>

        <label>Motivo:</label>
        <textarea name="motivo" required></textarea>

        <button type="submit" class="boton-modulo">Registrar Devolución</button>
    </form>

</main>

</body>
</html>





