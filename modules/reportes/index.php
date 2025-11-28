<?php
session_start();
require_once '../../config/database.php';
$db = new Database();

// Obtener datos para reportes
$saldoProveedores = $db->executeProcedure("sp_Consulta_SaldoProveedores");
$existenciasBodega = $db->executeProcedure("sp_Consulta_ExistenciasBodega");
$movimientosDia = $db->executeProcedure("sp_Consulta_MovimientosDia");
$saldoCuentas = $db->executeProcedure("sp_Consulta_SaldoCuentasBancarias");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes del Sistema</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">Comercializadora Médica</div>
            <ul class="nav-menu">
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../clientes/">Clientes</a></li>
                <li><a href="../proveedores/">Proveedores</a></li>
                <li><a href="../productos/">Productos</a></li>
                <li><a href="../compras/">Compras</a></li>
                <li><a href="../ventas/">Ventas</a></li>
                <li><a href="../inventario/">Inventario</a></li>
                <li><a href="#" class="active">Reportes</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Reportes del Sistema</h1>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Saldos de Proveedores</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Límite Crédito</th>
                            <th>Saldo Actual</th>
                            <th>Órdenes Pendientes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saldoProveedores as $proveedor): ?>
                        <tr>
                            <td><?php echo $proveedor['nombre']; ?></td>
                            <td>L. <?php echo number_format($proveedor['limite_credito'], 2); ?></td>
                            <td style="color: <?php echo $proveedor['saldo_actual'] > 0 ? '#e74c3c' : '#27ae60'; ?>">
                                L. <?php echo number_format($proveedor['saldo_actual'], 2); ?>
                            </td>
                            <td><?php echo $proveedor['ordenes_pendientes']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Existencias por Bodega</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bodega</th>
                            <th>Producto</th>
                            <th>Disponible</th>
                            <th>Reservado</th>
                            <th>Lote</th>
                            <th>Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($existenciasBodega as $existencia): ?>
                        <tr>
                            <td><?php echo $existencia['bodega']; ?></td>
                            <td><?php echo $existencia['producto']; ?></td>
                            <td><?php echo $existencia['cantidad_disponible']; ?></td>
                            <td><?php echo $existencia['cantidad_reservada']; ?></td>
                            <td><?php echo $existencia['lote']; ?></td>
                            <td><?php echo $existencia['fecha_vencimiento']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Movimientos del Día</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Bodega</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientosDia as $movimiento): ?>
                        <tr>
                            <td><?php echo $movimiento['fecha_movimiento']; ?></td>
                            <td><?php echo $movimiento['tipo_movimiento']; ?></td>
                            <td><?php echo $movimiento['producto']; ?></td>
                            <td><?php echo $movimiento['bodega']; ?></td>
                            <td style="color: <?php echo $movimiento['cantidad'] > 0 ? '#27ae60' : '#e74c3c'; ?>">
                                <?php echo $movimiento['cantidad']; ?>
                            </td>
                            <td><?php echo $movimiento['usuario']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Saldos de Cuentas Bancarias</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cuenta</th>
                            <th>Banco</th>
                            <th>Tipo</th>
                            <th>Saldo</th>
                            <th>Depósitos Hoy</th>
                            <th>Pagos Hoy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saldoCuentas as $cuenta): ?>
                        <tr>
                            <td><?php echo $cuenta['numero_cuenta']; ?></td>
                            <td><?php echo $cuenta['nombre_banco']; ?></td>
                            <td><?php echo $cuenta['tipo_cuenta']; ?></td>
                            <td>L. <?php echo number_format($cuenta['saldo'], 2); ?></td>
                            <td>L. <?php echo number_format($cuenta['depositos_hoy'] ?? 0, 2); ?></td>
                            <td>L. <?php echo number_format($cuenta['pagos_hoy'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>