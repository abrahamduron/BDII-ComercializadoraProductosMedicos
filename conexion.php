<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "productos_medicos";

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>

