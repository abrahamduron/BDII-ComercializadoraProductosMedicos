<?php
include "../../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Existencias en Bodega</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<aside class="sidebar">
    <h2>Consultas</h2>
    <ul>
        <li><a href="consultas.php">Inicio</a></li>
        <li><a href="existencias_bodega.php" class="activo">Existencias en Bodega</a></li>
        <li><a href="movimientos_dia.php">Movimientos del Día</a></li>
        <li><a href="ordenes_pendientes.php">Órdenes Pendientes</a></li>
        <li><a href="saldo_cuentas.php">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php">Saldo Proveedores</a></li>
         <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Existencias en Bodega</h1>
    <table>
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Nombre</th>
                <th>Stock Actual</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conexion->query("SELECT id_producto, nombre, cantidad, precio FROM inventario ORDER BY nombre");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_producto']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['cantidad']}</td>
                        <td>{$row['precio']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>







