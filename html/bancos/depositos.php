<?php
include "../../conexion.php"; // Ajusta la ruta según tu estructura

$mensaje = "";

// Registrar depósito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cuenta = $_POST['id_cuenta'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];

    // Insertar el depósito
    $stmt = $conexion->prepare("INSERT INTO depositos (id_cuenta, monto, fecha) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $id_cuenta, $monto, $fecha);

    if ($stmt->execute()) {
        // Actualizar el saldo de la cuenta
        $conexion->query("UPDATE cuentas_bancarias SET saldo = saldo + $monto WHERE id_cuenta = $id_cuenta");
        $mensaje = "Depósito registrado correctamente.";
    } else {
        $mensaje = "Error al registrar el depósito: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Depósitos - Bancos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Bancos</h2>
        <ul>
            <li><a href="bancos.php">Bancos</a></li>
            <li><a href="cuentas_bancarias.php">Cuentas Bancarias</a></li>
            <li><a href="depositos.php" class="activo">Depósitos</a></li>
            <li><a href="pagos_proveedores.php">Pagos a Proveedores</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Registrar Depósito</h1>

        <?php if (!empty($mensaje)) echo "<p class='exito'>$mensaje</p>"; ?>

        <form method="POST">
            <label for="id_cuenta">Cuenta Bancaria:</label>
            <select name="id_cuenta" required>
                <option value="">Seleccione una cuenta</option>
                <?php
                $res_cuentas = $conexion->query("SELECT c.id_cuenta, b.nombre AS banco, c.numero_cuenta
                                                FROM cuentas_bancarias c
                                                JOIN bancos b ON c.id_banco = b.id_banco
                                                ORDER BY b.nombre, c.numero_cuenta");
                while($cuenta = $res_cuentas->fetch_assoc()) {
                    echo "<option value='{$cuenta['id_cuenta']}'>Banco: {$cuenta['banco']} - Cuenta: {$cuenta['numero_cuenta']}</option>";
                }
                ?>
            </select><br><br>

            <label for="monto">Monto:</label>
            <input type="number" step="0.01" name="monto" required><br><br>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required><br><br>

            <button type="submit">Registrar Depósito</button>
        </form>

        <h2>Historial de Depósitos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cuenta</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT d.id_deposito, b.nombre AS banco, c.numero_cuenta, d.monto, d.fecha
                        FROM depositos d
                        JOIN cuentas_bancarias c ON d.id_cuenta = c.id_cuenta
                        JOIN bancos b ON c.id_banco = b.id_banco
                        ORDER BY d.fecha DESC";
                $res = $conexion->query($sql);
                while($fila = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id_deposito']}</td>
                            <td>{$fila['banco']} - {$fila['numero_cuenta']}</td>
                            <td>{$fila['monto']}</td>
                            <td>{$fila['fecha']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>





