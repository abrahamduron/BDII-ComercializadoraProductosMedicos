<?php
// compras/detalle_orden.php
session_start();
require_once '../../config/database.php';
$db = new Database();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_orden = $_GET['id'];

// Obtener detalles de la orden
$orden = $db->executeQuery("
    SELECT oc.*, p.nombre as proveedor_nombre 
    FROM ordenes_compra oc 
    INNER JOIN proveedores p ON oc.id_proveedor = p.id 
    WHERE oc.id = ?
", [$id_orden]);

$detalles = $db->executeQuery("
    SELECT doc.*, i.nombre as producto_nombre, i.codigo_producto
    FROM detalle_orden_compra doc 
    INNER JOIN inventario i ON doc.id_producto = i.id_producto 
    WHERE doc.id_orden_compra = ?
", [$id_orden]);

if (empty($orden)) {
    header('Location: index.php');
    exit;
}

$orden = $orden[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden de Compra</title>
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
                <li><a href="#" class="active">Compras</a></li>
                <li><a href="../ventas/">Ventas</a></li>
                <li><a href="../inventario/">Inventario</a></li>
                <li><a href="../reportes/">Reportes</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Detalle de Orden de Compra: <?php echo htmlspecialchars($orden['numero_orden']); ?></h1>
                <a href="index.php" class="btn btn-primary">Volver</a>
            </div>

            <div class="card" style="margin-bottom: 1rem;">
                <div class="card-header">
                    <h3>Información General</h3>
                </div>
                <div style="padding: 1.5rem;">
                    <div class="form-row">
                        <div class="form-group">
                            <strong>Proveedor:</strong> <?php echo htmlspecialchars($orden['proveedor_nombre']); ?>
                        </div>
                        <div class="form-group">
                            <strong>Fecha Orden:</strong> <?php echo date('d/m/Y', strtotime($orden['fecha_orden'])); ?>
                        </div>
                        <div class="form-group">
                            <strong>Fecha Esperada:</strong> <?php echo date('d/m/Y', strtotime($orden['fecha_esperada_entrega'])); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <strong>Estado:</strong> 
                            <span class="badge badge-<?php 
                                switch($orden['estado']) {
                                    case 'APROBADA': echo 'success'; break;
                                    case 'PENDIENTE': echo 'warning'; break;
                                    case 'RECIBIDA': echo 'info'; break;
                                    case 'CANCELADA': echo 'danger'; break;
                                    default: echo 'secondary';
                                }
                            ?>">
                                <?php echo $orden['estado']; ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <strong>Subtotal:</strong> L. <?php echo number_format($orden['subtotal'], 2); ?>
                        </div>
                        <div class="form-group">
                            <strong>IVA:</strong> L. <?php echo number_format($orden['iva'], 2); ?>
                        </div>
                        <div class="form-group">
                            <strong>Total:</strong> L. <?php echo number_format($orden['total'], 2); ?>
                        </div>
                    </div>
                    <?php if (!empty($orden['observaciones'])): ?>
                        <div class="form-group">
                            <strong>Observaciones:</strong> <?php echo htmlspecialchars($orden['observaciones']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Productos de la Orden</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad Solicitada</th>
                            <th>Cantidad Recibida</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detalles)): ?>
                            <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['codigo_producto']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                <td><?php echo $detalle['cantidad_solicitada']; ?></td>
                                <td><?php echo $detalle['cantidad_recibida']; ?></td>
                                <td>L. <?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                <td>L. <?php echo number_format($detalle['subtotal'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No hay productos en esta orden</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>