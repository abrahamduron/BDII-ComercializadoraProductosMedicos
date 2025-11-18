<?php
include "../../conexion.php";

// Manejo de filtro por fecha
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

// Consulta de arqueos
$query = "SELECT r.id_recibo, c.nombre AS cliente, r.fecha, r.monto
          FROM recibos r
          JOIN clientes c ON r.id_cliente = c.id";

if ($fecha_inicio && $fecha_fin) {
    $query .= " WHERE r.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$query .= " ORDER BY r.fecha DESC";

$result = $conexion->query($query);

// Calcular total
$total = 0;
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $total += $row['monto'];
    }
}

// Para mostrar de nuevo los resultados completos después del cálculo
$result = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueos - Ventas</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Ventas</h2>
        <ul>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="facturas.php">Facturas</a></li>
            <li><a href="recibos.php">Recibos</a></li>
            <li><a href="arqueos.php" class="activo">Arqueos</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Arqueo de Caja</h1>

        <form method="GET" action="">
            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">

            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">

            <button type="submit">Filtrar</button>
        </form>

        <h2>Movimientos Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Recibo</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_recibo'] ?></td>
                            <td><?= htmlspecialchars($row['cliente']) ?></td>
                            <td><?= $row['fecha'] ?></td>
                            <td><?= number_format($row['monto'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No hay movimientos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong><?= number_format($total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>
</html>
