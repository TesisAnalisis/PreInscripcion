<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 2) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'SI') {
        // Eliminar el usuario
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        
        if ($stmt->execute()) {
            // Cerrar sesión y redirigir
            session_destroy();
            header('Location: login.php?mensaje=eliminado');
            exit();
        } else {
            $error = "Error al eliminar la inscripción.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Inscripción</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; }
        .header { background: #e74c3c; color: white; padding: 1rem; }
        .container { max-width: 500px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 0 10px; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Eliminar Inscripción</h1>
    </div>
    <div class="container">
        <div class="warning">
            <h3>⚠️ Advertencia</h3>
            <p>Esta acción es irreversible. Se eliminarán todos tus datos del sistema.</p>
        </div>
        
        <form method="POST">
            <p>Para confirmar, escribe <strong>SI</strong> en el siguiente campo:</p>
            <input type="text" name="confirmar" placeholder="Escribe SI aquí" required style="padding: 10px; width: 100px; text-align: center; font-size: 16px;">
            <br><br>
            <button type="submit" class="btn btn-danger">Eliminar Definitivamente</button>
            <a href="inicio.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
