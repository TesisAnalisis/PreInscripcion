<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $conexion->real_escape_string($_POST['correo']);

    $sql = "SELECT id FROM usuarios WHERE correo = '$correo'";
    $resultado = $conexion->query($sql);

    if ($resultado && $usuario = $resultado->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $user_id = $usuario['id'];

        $sqlInsert = "INSERT INTO recuperacion_password (user_id, token, fecha_expiracion) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sqlInsert);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("iss", $user_id, $token, $expiracion);
        $stmt->execute();
        $stmt->close();

        // Simular envío de correo
        $url = "http://localhost/PreInscripcion/PreInscripcion/Sesion/restablecer_contrasena.php?token=$token";

        echo "<p>Si el correo está registrado, se enviaron las instrucciones para recuperar la contraseña.</p>";
        echo "<p><strong>Modo prueba:</strong> Este sería el enlace generado:</p>";
        echo "<p><a href='$url'>$url</a></p>";
    } else {
        echo "<p>Si el correo está registrado, se enviaron las instrucciones para recuperar la contraseña.</p>";
    }
}
?>



