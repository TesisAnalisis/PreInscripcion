<?php
session_start();

// Configuración de base de datos
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

if (!isset($_SESSION['usuario_id'])) {
    die("No ha iniciado sesión.");
}

$usuario_id = $_SESSION['usuario_id'];

// Verificamos que el formulario haya enviado el ID para eliminar y que coincida con el de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_eliminar = $_POST['id'] ?? 0;
    if ($id_eliminar != $usuario_id) {
        die("No tiene permiso para eliminar este usuario.");
    }

    // Eliminar usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        // Destruir sesión porque ya no existe el usuario
        session_destroy();

        // Mensaje y redirección
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8" />
            <title>Cuenta eliminada</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f8d7da;
                    display: flex;
                    height: 100vh;
                    align-items: center;
                    justify-content: center;
                    margin: 0;
                }
                .mensaje {
                    background: #721c24;
                    color: #f5c6cb;
                    padding: 30px 40px;
                    border-radius: 12px;
                    text-align: center;
                    max-width: 400px;
                    box-shadow: 0 4px 15px rgba(114,28,36,0.8);
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    color: white;
                    background-color: #c82333;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: bold;
                    transition: background-color 0.3s ease;
                }
                a:hover {
                    background-color: #7b121a;
                }
            </style>
        </head>
        <body>
            <div class="mensaje">
                <h2>Cuenta eliminada correctamente.</h2>
                <a href="inicio.php">Volver al inicio</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Error al eliminar la cuenta: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Acceso no válido.";
}

$conn->close();

