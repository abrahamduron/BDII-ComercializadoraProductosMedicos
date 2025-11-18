<?php
include "../../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultas - Sistema</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<aside class="sidebar">
    <h2>Consultas</h2>
    <ul>
        <li><a href="consultas.php" class="activo">Consultas</a></li>
        <li><a href="existencias_bodega.php">Existencias en Bodega</a></li>
        <li><a href="movimientos_dia.php">Movimientos del Día</a></li>
        <li><a href="ordenes_pendientes.php">Órdenes Pendientes</a></li>
        <li><a href="saldo_cuentas.php">Saldo Cuentas Bancarias</a></li>
        <li><a href="saldo_proveedores.php">Saldo Proveedores</a></li>
         <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Consultas</h1>
    <div class="panel-enlaces">
        <a href="existencias_bodega.php" class="boton-modulo">Existencias en Bodega</a>
        <a href="movimientos_dia.php" class="boton-modulo">Movimientos del Día</a>
        <a href="ordenes_pendientes.php" class="boton-modulo">Órdenes Pendientes</a>
        <a href="saldo_cuentas.php" class="boton-modulo">Saldo Cuentas Bancarias</a>
        <a href="saldo_proveedores.php" class="boton-modulo">Saldo Proveedores</a>
    </div>
</main>
</body>
</html>

