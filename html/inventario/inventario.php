<?php include "../../conexion.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<aside class="sidebar">
    <h2>Inventario</h2>
    <ul>
        <li><a href="inventario.php" class="activo">Inventario</a></li>
        <li><a href="agregar_producto.php">Agregar Producto</a></li>
        <li><a href="proveedores.php">Proveedores</a></li>
        <li><a href="orden_compra.php">Orden de Compra</a></li>
        <li><a href="devoluciones.php">Devoluciones</a></li>
        <li><a href="ingreso_rechazo.php">Ingresos / Rechazos</a></li>
        <li><a href="elaboracion.php">Elaboración</a></li>
        <li><a href="mostrar_inventario.php">Mostrar Inventario</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Inventario General</h1>

<?php
$sql = "SELECT * FROM inventario ORDER BY nombre ASC";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>";

    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['id_producto']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['descripcion']}</td>
                <td>{$fila['cantidad']}</td>
                <td>\${$fila['precio']}</td>
              </tr>";
    }

    echo "</tbody></table>";

} else {
    echo "<p>No hay productos registrados.</p>";
}
?>

</main>

</body>
</html>










