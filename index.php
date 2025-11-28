<?php
session_start();
require_once 'config/database.php';
$db = new Database();

// Obtener estadísticas para el dashboard
$clientes = $db->executeProcedure("sp_Cliente_Leer");
$proveedores = $db->executeProcedure("sp_Proveedor_Leer");
$productos = $db->executeProcedure("sp_Producto_Leer");
$stockBajo = $db->executeProcedure("sp_Producto_StockBajo");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comercializadora de Productos Médicos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">Comercializadora Médica</div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="modules/clientes/">Clientes</a></li>
                <li><a href="modules/proveedores/">Proveedores</a></li>
                <li><a href="modules/productos/">Productos</a></li>
                <li><a href="modules/compras/">Compras</a></li>
                <li><a href="modules/ventas/">Ventas</a></li>
                <li><a href="modules/inventario/">Inventario</a></li>
                <li><a href="modules/reportes/">Reportes</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Dashboard - Sistema de Gestión</h1>
            </div>
            
            <div class="grid">
                <div class="dashboard-card">
                    <h3>Total Clientes</h3>
                    <div class="number"><?php echo count($clientes); ?></div>
                </div>
                <div class="dashboard-card">
                    <h3>Total Proveedores</h3>
                    <div class="number"><?php echo count($proveedores); ?></div>
                </div>
                <div class="dashboard-card">
                    <h3>Total Productos</h3>
                    <div class="number"><?php echo count($productos); ?></div>
                </div>
                <div class="dashboard-card">
                    <h3>Productos con Stock Bajo</h3>
                    <div class="number" style="color: #e74c3c;"><?php echo count($stockBajo); ?></div>
                </div>
            </div>

            <?php if (!empty($stockBajo)): ?>
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2>Productos con Stock Bajo</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stockBajo as $producto): ?>
                        <tr>
                            <td><?php echo $producto['codigo_producto']; ?></td>
                            <td><?php echo $producto['nombre']; ?></td>
                            <td style="color: #e74c3c; font-weight: bold;"><?php echo $producto['cantidad']; ?></td>
                            <td><?php echo $producto['stock_minimo']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>