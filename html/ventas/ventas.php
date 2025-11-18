<?php
include "../../conexion.php"; // Ruta correcta a tu conexión

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Productos Médicos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

    <!-- SIDEBAR / MENÚ -->
    <aside class="sidebar">
        <h2>Ventas</h2>
        <ul>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="ventas.php" class="activo">Ventas</a></li>
            <li><a href="facturas.php">Facturas</a></li>
            <li><a href="recibos.php">Recibos</a></li>
            <li><a href="arqueos.php">Arqueos</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Registro de Ventas</h1>
        <p>Agrega y visualiza todas las ventas realizadas.</p>

        <!-- Formulario para agregar venta -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cliente = $_POST['id_cliente'];
            $fecha = $_POST['fecha'];
            $total = $_POST['total'];

            $stmt = $conexion->prepare("INSERT INTO ventas (id_cliente, fecha, total) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $id_cliente, $fecha, $total);

            if ($stmt->execute()) {
                echo "<p class='exito'>Venta registrada correctamente.</p>";
            } else {
                echo "<p class='error'>Error al registrar la venta: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
        ?>

        <form method="POST" class="formulario">
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" required>
                <option value="">Selecciona un cliente</option>
                <?php
                $clientes = $conexion->query("SELECT id_cliente, nombre FROM clientes");
                while($cliente = $clientes->fetch_assoc()) {
                    echo "<option value='" . $cliente['id_cliente'] . "'>" . $cliente['nombre'] . "</option>";
                }
                ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <label for="total">Total:</label>
            <input type="number" step="0.01" name="total" id="total" required>

            <button type="submit" class="boton-modulo">Agregar Venta</button>
        </form>

        <!-- Tabla de ventas -->
        <h2>Lista de Ventas</h2>
        <?php
        $resultado = $conexion->query("SELECT v.id_venta, c.nombre AS cliente, v.fecha, v.total 
                                       FROM ventas v 
                                       INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                                       ORDER BY v.fecha DESC");
        if ($resultado->num_rows > 0) {
            echo "<table class='tabla-inventario'>";
            echo "<thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th></tr></thead><tbody>";
            while($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_venta'] . "</td>";
                echo "<td>" . $row['cliente'] . "</td>";
                echo "<td>" . $row['fecha'] . "</td>";
                echo "<td>$" . number_format($row['total'],2) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='info'>No hay ventas registradas.</p>";
        }
        ?>
    </main>

</body>
</html>





