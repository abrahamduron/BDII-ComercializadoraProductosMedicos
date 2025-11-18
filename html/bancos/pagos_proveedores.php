<?php
include "../../conexion.php"; // Ajusta la ruta según tu estructura

$mensaje = "";

// Registrar pago a proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proveedor = $_POST['id_proveedor'];
    $id_cuenta = $_POST['id_cuenta'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];

    // Verificar que la cuenta tenga saldo suficiente
    $res_saldo = $conexion->query("SELECT saldo FROM cuentas_bancarias WHERE id_cuenta = $id_cuenta");
    $saldo = $res_saldo->fetch_assoc()['saldo'];

    if ($saldo >= $monto) {
        // Insertar el pago
        $stmt = $conexion->prepare("INSERT INTO pagos_proveedores (id_proveedor, id_cuenta, monto, fecha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $id_proveedor, $id_cuenta, $monto, $fecha);

        if ($stmt->execute()) {
            // Actualizar saldo de la cuenta
            $conexion->query("UPDATE cuentas_bancarias SET saldo = saldo - $monto WHERE id_cuenta = $id_cuenta");
            $mensaje = "Pago registrado correctamente.";
        } else {
            $mensaje = "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        $mensaje = "Error: saldo insuficiente en la cuenta.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos a Proveedores - Bancos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Bancos</h2>
        <ul>
            <li><a href="bancos.php">Bancos</a></li>
            <li><a href="cuentas_bancarias.php">Cuentas Bancarias</a></li>
            <li><a href="depositos.php">Depósitos</a></li>
            <li><a href="pagos_proveedores.php" class="activo">Pagos a Proveedores</a></li>
             <li><a href="../index.php" class="volver">Volver</a></li>
        </ul>
    </aside>

    <main>
        <h1>Registrar Pago a Proveedor</h1>

        <?php if (!empty($mensaje)) echo "<p class='exito'>$mensaje</p>"; ?>

        <form method="POST">
            <label for="id_proveedor">Proveedor:</label>
            <select name="id_proveedor" required>
                <option value="">Seleccione un proveedor</option>
                <?php
                $res_proveedores = $conexion->query("SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre");
                while($prov = $res_proveedores->fetch_assoc()) {
                    echo "<option value='{$prov['id_proveedor']}'>{$prov['nombre']}</option>";
                }
                ?>
            </select><br><br>

            <label for="id_cuenta">Cuenta Bancaria:</label>
            <select name="id_cuenta" required>
                <option value="">Seleccione una cuenta</option>
                <?php
                $res_cuentas = $conexion->query("SELECT c.id_cuenta, b.nombre AS banco, c.numero_cuenta, c.saldo
                                                FROM cuentas_bancarias c
                                                JOIN bancos b ON c.id_banco = b.id_banco
                                                ORDER BY b.nombre, c.numero_cuenta");
                while($cuenta = $res_cuentas->fetch_assoc()) {
                    echo "<option value='{$cuenta['id_cuenta']}'>Banco: {$cuenta['banco']} - Cuenta: {$cuenta['numero_cuenta']} (Saldo: {$cuenta['saldo']})</option>";
                }
                ?>
            </select><br><br>

            <label for="monto">Monto:</label>
            <input type="number" step="0.01" name="monto" required><br><br>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required><br><br>

            <button type="submit">Registrar Pago</button>
        </form>

        <h2>Historial de Pagos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Cuenta</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT p.id_pago, prov.nombre AS proveedor, b.nombre AS banco, c.numero_cuenta, p.monto, p.fecha
                        FROM pagos_proveedores p
                        JOIN proveedores prov ON p.id_proveedor = prov.id_proveedor
                        JOIN cuentas_bancarias c ON p.id_cuenta = c.id_cuenta
                        JOIN bancos b ON c.id_banco = b.id_banco
                        ORDER BY p.fecha DESC";
                $res = $conexion->query($sql);
                while($fila = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id_pago']}</td>
                            <td>{$fila['proveedor']}</td>
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






