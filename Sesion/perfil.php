<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 2) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Procesar mensajes de éxito/error
$mensaje = '';
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'actualizado':
            $mensaje = '<div class="alert success">Datos actualizados correctamente.</div>';
            break;
        case 'error':
            $mensaje = '<div class="alert error">Error al actualizar los datos.</div>';
            break;
        case 'eliminado':
            $mensaje = '<div class="alert success">Inscripción eliminada correctamente.</div>';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Panel de Usuario</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .panel {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .welcome {
            text-align: center;
            margin-bottom: 2rem;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .action-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #219a52;
        }
        .user-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logout {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout:hover {
            background: #7f8c8d;
        }
        /* Footer */
        .app-footer {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Panel de Usuario</h1>
        <div>
            <span>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></span>
            <form action="logout.php" method="post" style="display: inline-block; margin-left: 1rem;">
                <button type="submit" class="logout">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        <?php echo $mensaje; ?>
        
        <div class="panel">
            <div class="welcome">
                <h2>Bienvenido a tu Panel de Control</h2>
                <p>Desde aquí puedes gestionar tu información personal y académica.</p>
            </div>

            <div class="user-info">
                <div class="info-item">
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?>
                </div>
                <div class="info-item">
                    <strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['apellido']); ?>
                </div>
                <div class="info-item">
                    <strong>Cédula:</strong> <?php echo htmlspecialchars($usuario['cedula_numero']); ?>
                </div>
                <div class="info-item">
                    <strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?>
                </div>
                <div class="info-item">
                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?>
                </div>
                <div class="info-item">
                    <strong>Carrera:</strong> <?php echo htmlspecialchars($usuario['carrera']); ?>
                </div>
            </div>

            <div class="actions">
                <div class="action-card">
                    <h3>📋 Ver Mis Datos Completos</h3>
                    <p>Consulta toda tu información de inscripción</p>
                    <a href="ver_datos.php" class="btn">Ver Datos</a>
                </div>

                <div class="action-card">
                    <h3>✏️ Editar Mis Datos</h3>
                    <p>Actualiza tu información personal y de contacto</p>
                    <a href="editar_datos.php" class="btn btn-success">Editar Datos</a>
                </div>

                <div class="action-card">
                    <h3>🗑️ Eliminar Inscripción</h3>
                    <p>Elimina tu registro del sistema (acción irreversible)</p>
                    <a href="eliminar_inscripcion.php" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar tu inscripción? Esta acción no se puede deshacer.')">Eliminar</a>
                </div>
            </div>
        </div>
         <!-- Footer -->
            <div class="app-footer">
                <p>&copy; 2025 Facultad de Ciencias Aplicadas - Todos los derechos reservados</p>
            </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
