<?php
include "../../conexion.php";
$fecha = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos del Día</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<aside class="sidebar">
    <h2>Consultas</h2>
    <ul>
        <li><a href="consultas.php">Inicio</a></li>
        <li><a href="existencias_bodega.php">Existencias en Bodega</a></li>
        <li><a href="movimientos_dia.php" class="activo">Movimientos del Día</a></li>
        <li><a href="ordenes_pendientes.php">Órdenes Pendientes</a></li>
        <li><a href="saldo_cuentas.php">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php">Saldo Proveedores</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Movimientos del Día (<?php echo $fecha; ?>)</h1>
    <table>
        <thead>
            <tr>
                <th>ID Movimiento</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conexion->query("SELECT m.id_movimiento, i.nombre AS producto, m.tipo, m.cantidad, m.fecha
                                     FROM movimientos_dia m
                                     JOIN inventario i ON m.id_producto = i.id_producto
                                     WHERE m.fecha = '$fecha'
                                     ORDER BY m.id_movimiento DESC");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_movimiento']}</td>
                        <td>{$row['producto']}</td>
                        <td>{$row['tipo']}</td>
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









