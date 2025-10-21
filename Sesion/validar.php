<?php
// Iniciar sesión al principio del script
session_start();
// Conexión a la base de datos (reemplaza con tus credenciales)
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
} catch (Exception $e) {
    error_log("Error de conexión: " . $e->getMessage());
    die("Error al conectar con la base de datos. Por favor, intente más tarde.");
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    // Validaciones básicas
    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        // Preparar la sentencia SQL
        $stmt = $conn->prepare("SELECT id, correo, contrasena, rol_id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            // Verificar la contraseña utilizando password_verify
            if (password_verify($contrasena, $usuario['contrasena'])) {
                // Regenerar ID de sesión para prevenir fixation attacks
                session_regenerate_id(true);
                
                // Establecer variables de sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol_id'];
                $_SESSION['ultimo_acceso'] = time();

                // Redirigir según el rol
                if ($usuario['rol_id'] == 1) { // Administrador
                    header('Location: admin.php');
                } else { // Usuario
                    header('Location: perfil.php');
                }
                exit();
            } else {
                $error = "Credenciales incorrectas.";
            }
        } else {
            $error = "Credenciales incorrectas.";
        }

        $stmt->close();
    }
}

$conn->close();
?>


