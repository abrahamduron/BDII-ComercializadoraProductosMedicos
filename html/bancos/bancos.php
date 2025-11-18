<?php 
include "../../conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bancos - Sistema Productos Médicos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<aside class="sidebar">
    <h2>Bancos</h2>
    <ul>
        <li><a href="bancos.php" class="activo">Bancos</a></li>
        <li><a href="cuentas_bancarias.php">Cuentas Bancarias</a></li>
        <li><a href="depositos.php">Depósitos</a></li>
        <li><a href="pagos_proveedores.php">Pagos a Proveedores</a></li>
         <li><a href="../index.php" class="volver">Volver</a></li>
    </ul>
</aside>

<main>
    <h1>Bancos</h1>
    <p>Administrar bancos de la empresa.</p>

    <h2>Agregar Banco</h2>
    <form method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br>
        <label>Dirección:</label><br>
        <input type="text" name="direccion"><br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono"><br><br>
        <input type="submit" value="Agregar Banco">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];

        $stmt = $conexion->prepare("INSERT INTO bancos (nombre, direccion, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $direccion, $telefono);

        if($stmt->execute()) {
            echo "<p class='exito'>Banco agregado correctamente.</p>";
        } else {
            echo "<p class='error'>Error al agregar el banco.</p>";
        }
    }

    // Mostrar bancos existentes
    $resultado = $conexion->query("SELECT * FROM bancos ORDER BY nombre");
    if($resultado->num_rows > 0){
        echo "<h2>Bancos Registrados</h2>";
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Nombre</th><th>Dirección</th><th>Teléfono</th></tr></thead><tbody>";
        while($row = $resultado->fetch_assoc()){
            echo "<tr>
                    <td>{$row['id_banco']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['direccion']}</td>
                    <td>{$row['telefono']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No hay bancos registrados.</p>";
    }
    ?>
</main>

<script src="../../js/main.js"></script>
</body>
</html>
