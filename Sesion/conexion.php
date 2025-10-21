<?php
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// echo "Conexión exitosa"; // ❌ Esta línea debe eliminarse o comentarse
?>

