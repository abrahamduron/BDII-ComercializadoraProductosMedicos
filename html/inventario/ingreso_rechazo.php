<?php
include '../../conexion.php';

$mensaje = "";

// Obtener lista de productos para el select (COLUMNAS REALES)
$productos = $conexion->query("SELECT id_producto, nombre FROM inventario ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_producto = intval($_POST['id_producto']);
    $tipo = $_POST['tipo'];
    $cantidad = intval($_POST['cantidad']);

    if ($id_producto <= 0 || $cantidad <= 0) {
        $mensaje = "Datos inválidos. Intente nuevamente.";
    } else {

        // Registrar movimiento
        $sql = "INSERT INTO ingreso_rechazo (id_producto, tipo, cantidad)
                VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isi", $id_producto, $tipo, $cantidad);

        if ($stmt->execute()) {

            // Actualizar inventario según movimiento
            if ($tipo === "ingreso") {
                $conexion->query("UPDATE inventario SET stock = stock + $cantidad WHERE id_producto = $id_producto");
            } else {
                $conexion->query("UPDATE inventario SET stock = stock - $cantidad WHERE id_producto = $id_producto");
            }

            $mensaje = "Movimiento registrado correctamente.";
        } else {
            $mensaje = "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresos / Rechazos</title>
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
        <li><a href="ingreso_rechazo.php" class="activo">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Registro de Ingresos y Rechazos</h1>
    <p>Permite registrar ingresos adicionales o rechazos de productos dañados.</p>

    <?php if ($mensaje != ""): ?>
        <p class="exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario">

        <label>Producto:</label>
        <select name="id_producto" required>
            <option value="">Seleccione un producto</option>
            <?php while ($p = $productos->fetch_assoc()): ?>
                <option value="<?php echo $p['id_producto']; ?>">
                    <?php echo $p['nombre_producto']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Tipo de movimiento:</label>
        <select name="tipo" required>
            <option value="ingreso">Ingreso</option>
            <option value="rechazo">Rechazo</option>
        </select>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="1" required>

        <button type="submit" class="boton-modulo">Registrar Movimiento</button>

    </form>

</main>

</body>
</html>








