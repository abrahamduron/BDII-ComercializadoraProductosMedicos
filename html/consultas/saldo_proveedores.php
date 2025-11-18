<?php
include "../../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Saldo Proveedores</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<aside class="sidebar">
    <h2>Consultas</h2>
    <ul>
        <li><a href="consultas.php">Inicio</a></li>
        <li><a href="existencias_bodega.php">Existencias en Bodega</a></li>
        <li><a href="movimientos_dia.php">Movimientos del Día</a></li>
        <li><a href="ordenes_pendientes.php">Órdenes Pendientes</a></li>
        <li><a href="saldo_cuentas.php">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php" class="activo">Saldo Proveedores</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Saldo Proveedores</h1>
    <table>
        <thead>
            <tr>
                <th>ID Proveedor</th>
                <th>Proveedor</th>
                <th>Total Pendiente</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conexion->query("SELECT p.id, p.nombre, IFNULL(SUM(o.cantidad * i.precio),0) AS total_pendiente
                                     FROM proveedores p
                                     LEFT JOIN ordenes_compra o ON p.id = o.proveedor_id AND o.estado='pendiente'
                                     LEFT JOIN inventario i ON o.producto_id = i.id_producto
                                     GROUP BY p.id
                                     ORDER BY p.nombre");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_proveedor']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['total_pendiente']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>






