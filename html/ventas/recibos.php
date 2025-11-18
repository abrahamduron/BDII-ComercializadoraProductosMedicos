<?php
include "../../conexion.php"; // Usamos $conexion definido en conexion.php

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = intval($_POST['id_cliente']);
    $fecha = $_POST['fecha'];
    $monto = floatval($_POST['monto']);

    $stmt = $conexion->prepare("INSERT INTO recibos (id_cliente, fecha, monto) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_cliente, $fecha, $monto);

    if ($stmt->execute()) {
        $mensaje = "Recibo registrado correctamente.";
    } else {
        $mensaje = "Error al registrar el recibo: " . $conexion->error;
    }
}

// Obtener lista de clientes
$clientes = $conexion->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos - Ventas</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Ventas</h2>
        <ul>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="facturas.php">Facturas</a></li>
            <li><a href="recibos.php" class="activo">Recibos</a></li>
            <li><a href="arqueos.php">Arqueos</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Registrar Recibo</h1>

        <?php if (!empty($mensaje)) echo "<p class='exito'>$mensaje</p>"; ?>

        <form method="POST" action="">
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" required>
                <option value="">Seleccione un cliente</option>
                <?php while ($row = $clientes->fetch_assoc()): ?>
                    <option value="<?= $row['id_cliente'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <label for="monto">Monto:</label>
            <input type="number" name="monto" id="monto" step="0.01" required>

            <button type="submit">Registrar Recibo</button>
        </form>

        <h2>Recibos Registrados</h2>
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
                <?php
                $result = $conexion->query("SELECT r.id_recibo, c.nombre, r.fecha, r.monto 
                                            FROM recibos r 
                                            JOIN clientes c ON r.id_cliente = c.id
                                            ORDER BY r.fecha DESC");
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $row['id_recibo'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['fecha'] ?></td>
                        <td><?= number_format($row['monto'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>


