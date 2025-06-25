<?php
$conexion = new mysqli("localhost", "root", "", "centro");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
