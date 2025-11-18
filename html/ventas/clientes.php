<?php
include "../../conexion.php"; // Asegúrate de que la ruta sea correcta
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Productos Médicos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

    <aside class="sidebar">
        <h2>Ventas</h2>
        <ul>
            <li><a href="clientes.php" class="activo">Clientes</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="facturas.php">Facturas</a></li>
            <li><a href="recibos.php">Recibos</a></li>
            <li><a href="arqueos.php">Arqueos</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Clientes</h1>
        <p>Agrega, edita y visualiza los clientes.</p>

        <!-- Formulario para agregar cliente -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];

            $stmt = $conexion->prepare("INSERT INTO clientes (nombre, telefono, correo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $telefono, $correo);

            if ($stmt->execute()) {
                echo "<p class='exito'>Cliente agregado correctamente.</p>";
            } else {
                echo "<p class='error'>Error al agregar cliente: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
        ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono">

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo">

            <button type="submit" class="boton-modulo">Agregar Cliente</button>
        </form>

        <!-- Tabla de clientes -->
        <h2>Lista de Clientes</h2>
        <?php
        $resultado = $conexion->query("SELECT * FROM clientes ORDER BY nombre ASC");
        if ($resultado->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th></tr></thead><tbody>";
            while($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_cliente'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['telefono'] . "</td>";
                echo "<td>" . $row['correo'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='info'>No hay clientes registrados.</p>";
        }
        ?>
    </main>

</body>
</html>





