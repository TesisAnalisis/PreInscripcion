<?php
// delete_user.php
session_start();

// ============================
// Configuración de la conexión
// ============================
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si es una solicitud POST y tiene el ID del usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Preparar la consulta de eliminación
    $sql_delete = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el usuario: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error al preparar la consulta: " . $conn->error;
    }
    
    // Redirigir de vuelta a la página principal
    header("Location: admin.php?action=view");
    exit();
} else {
    // Si no es una solicitud válida, redirigir
    $_SESSION['error_message'] = "Solicitud inválida.";
    header("Location: admin.php?action=view");
    exit();
}

$conn->close();
?>
