<?php
include "../../conexion.php"; // Ajusta la ruta según tu estructura

// Agregar cuenta bancaria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_banco = $_POST['id_banco'];
    $numero_cuenta = $_POST['numero_cuenta'];
    $tipo_cuenta = $_POST['tipo_cuenta'];
    $saldo = floatval($_POST['saldo']);

    $stmt = $conexion->prepare("INSERT INTO cuentas_bancarias (id_banco, numero_cuenta, tipo_cuenta, saldo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $id_banco, $numero_cuenta, $tipo_cuenta, $saldo);

    if ($stmt->execute()) {
        $mensaje = "Cuenta bancaria agregada correctamente.";
    } else {
        $mensaje = "Error al agregar la cuenta: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuentas Bancarias - Bancos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Bancos</h2>
        <ul>
            <li><a href="bancos.php">Bancos</a></li>
            <li><a href="cuentas_bancarias.php" class="activo">Cuentas Bancarias</a></li>
            <li><a href="depositos.php">Depósitos</a></li>
            <li><a href="pagos_proveedores.php">Pagos a Proveedores</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Cuentas Bancarias</h1>

        <?php if (!empty($mensaje)) echo "<p class='exito'>$mensaje</p>"; ?>

        <h2>Agregar Nueva Cuenta</h2>
        <form method="POST">
            <label for="id_banco">Banco:</label>
            <select name="id_banco" required>
                <option value="">Seleccione un banco</option>
                <?php
                $res_bancos = $conexion->query("SELECT * FROM bancos ORDER BY nombre");
                while($banco = $res_bancos->fetch_assoc()) {
                    echo "<option value='{$banco['id_banco']}'>{$banco['nombre']}</option>";
                }
                ?>
            </select><br><br>

            <label for="numero_cuenta">Número de Cuenta:</label>
            <input type="text" name="numero_cuenta" required><br><br>

            <label for="tipo_cuenta">Tipo de Cuenta:</label>
            <input type="text" name="tipo_cuenta"><br><br>

            <label for="saldo">Saldo Inicial:</label>
            <input type="number" step="0.01" name="saldo" value="0"><br><br>

            <button type="submit">Agregar Cuenta</button>
        </form>

        <h2>Lista de Cuentas Bancarias</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Banco</th>
                    <th>Número de Cuenta</th>
                    <th>Tipo</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT c.id_cuenta, b.nombre AS banco, c.numero_cuenta, c.tipo_cuenta, c.saldo
                        FROM cuentas_bancarias c
                        JOIN bancos b ON c.id_banco = b.id_banco
                        ORDER BY b.nombre, c.numero_cuenta";
                $res = $conexion->query($sql);
                while($fila = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id_cuenta']}</td>
                            <td>{$fila['banco']}</td>
                            <td>{$fila['numero_cuenta']}</td>
                            <td>{$fila['tipo_cuenta']}</td>
                            <td>{$fila['saldo']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>




