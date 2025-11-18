<?php
include "../../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes Pendientes</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<aside class="sidebar">
    <h2>Consultas</h2>
    <ul>
        <li><a href="consultas.php">Inicio</a></li>
        <li><a href="existencias_bodega.php">Existencias en Bodega</a></li>
        <li><a href="movimientos_dia.php">Movimientos del Día</a></li>
        <li><a href="ordenes_pendientes.php" class="activo">Órdenes Pendientes</a></li>
        <li><a href="saldo_cuentas.php">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php">Saldo Proveedores</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Órdenes Pendientes</h1>
    <table>
        <thead>
            <tr>
                <th>ID Orden</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Fecha Orden</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conexion->query("SELECT o.id, p.nombre AS proveedor, i.nombre AS producto, o.cantidad, o.fecha
                                     FROM ordenes_compra o
                                     JOIN proveedores p ON o.proveedor_id = p.id
                                     JOIN inventario i ON o.producto_id = i.id_producto
                                     WHERE o.estado = 'pendiente'
                                     ORDER BY o.fecha ASC");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_orden']}</td>
                        <td>{$row['proveedor']}</td>
                        <td>{$row['producto']}</td>
                        <td>{$row['cantidad']}</td>
                        <td>{$row['fecha']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>






