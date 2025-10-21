<?php
// Replace with your database connection details
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$comentario = $_POST['comentario'];

// Prepare SQL statement (prevent SQL injection)
$sql = "INSERT INTO comentarios (nombre, correo, comentario) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nombre, $email, $comentario);

// Execute the statement
if ($stmt->execute()) {
    echo "Comentario enviado con éxito.";
} else {
    echo "Error: " . $stmt->error;
}
// Redirigir al inicio de sesión después de 3 segundos
header("refresh:3;url=http://localhost/PreInscripcion/PreInscripcion/");
$stmt->close();
$conn->close();
?>