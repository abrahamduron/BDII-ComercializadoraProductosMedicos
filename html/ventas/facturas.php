<?php
include "../../conexion.php"; // Asegúrate que la ruta es correcta
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas - Productos Médicos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

    <aside class="sidebar">
        <h2>Ventas</h2>
        <ul>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="facturas.php" class="activo">Facturas</a></li>
            <li><a href="recibos.php">Recibos</a></li>
            <li><a href="arqueos.php">Arqueos</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Facturas</h1>
        <p>Visualiza y registra facturas de ventas.</p>

        <!-- Formulario para agregar factura -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cliente = $_POST['id_cliente'];
            $fecha = $_POST['fecha'];
            $total = floatval($_POST['total']);

            $stmt = $conexion->prepare("INSERT INTO facturas (id_cliente, fecha, total) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $id_cliente, $fecha, $total);

            if ($stmt->execute()) {
                echo "<p class='exito'>Factura registrada correctamente.</p>";
            } else {
                echo "<p class='error'>Error al registrar factura: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
        ?>

        <form method="POST" class="formulario">
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" required>
                <option value="">Seleccione un cliente</option>
                <?php
                $clientes = $conexion->query("SELECT id_cliente, nombre FROM clientes ORDER BY nombre ASC");
                while($c = $clientes->fetch_assoc()) {
                    echo "<option value='".$c['id_cliente']."'>".$c['nombre']."</option>";
                }
                ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <label for="total">Total:</label>
            <input type="number" name="total" id="total" step="0.01" required>

            <button type="submit" class="boton-modulo">Registrar Factura</button>
        </form>

        <!-- Tabla de facturas -->
        <h2>Lista de Facturas</h2>
        <?php
        $resultado = $conexion->query("
            SELECT f.id_factura, c.nombre AS cliente, f.fecha, f.total 
            FROM facturas f
            INNER JOIN clientes c ON f.id_cliente = c.id_cliente
            ORDER BY f.fecha DESC
        ");

        if ($resultado->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th></tr></thead><tbody>";
            while($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['id_factura']."</td>";
                echo "<td>".$row['cliente']."</td>";
                echo "<td>".$row['fecha']."</td>";
                echo "<td>".$row['total']."</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='info'>No hay facturas registradas.</p>";
        }
        ?>
    </main>

</body>
</html>

