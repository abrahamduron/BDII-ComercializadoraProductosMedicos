<?php
include "../../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Saldo Cuentas Bancarias</title>
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
        <li><a href="saldo_cuentas.php" class="activo">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php">Saldo Proveedores</a></li>
        <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Saldo Cuentas Bancarias</h1>
    <table>
        <thead>
            <tr>
                <th>ID Cuenta</th>
                <th>Banco</th>
                <th>Cuenta</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conexion->query("SELECT c.id_cuenta, b.nombre AS banco, c.numero_cuenta, c.saldo
                                     FROM cuentas_bancarias c
                                     JOIN bancos b ON c.id_banco = b.id_banco
                                     ORDER BY b.nombre");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_cuenta']}</td>
                        <td>{$row['banco']}</td>
                        <td>{$row['numero_cuenta']}</td>
                        <td>{$row['saldo']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>








