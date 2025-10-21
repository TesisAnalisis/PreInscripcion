<?php
session_start();
include 'conexion.php';

function mostrarMensaje($mensaje, $tipo = 'info') {
    $color = ($tipo === 'error') ? 'red' : 'green';
    echo "<p style='color: $color;'>$mensaje</p>";
}

if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];

// Verificar token válido
$sql = "SELECT * FROM recuperacion_password WHERE token = ? AND usado = 0 AND fecha_expiracion > NOW()";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();
$registro = $resultado->fetch_assoc();
$stmt->close();

if (!$registro) {
    die("El enlace ha expirado o es inválido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($password) || empty($password2)) {
        mostrarMensaje("Ambos campos de contraseña son obligatorios.", 'error');
    } elseif ($password !== $password2) {
        mostrarMensaje("Las contraseñas no coinciden.", 'error');
    } elseif (strlen($password) < 6) {
        mostrarMensaje("La contraseña debe tener al menos 6 caracteres.", 'error');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // Actualizar contraseña
            $sqlUpdate = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            if (!$stmtUpdate) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmtUpdate->bind_param("si", $hash, $registro['user_id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            // Marcar token como usado
            $sqlToken = "UPDATE recuperacion_password SET usado = 1 WHERE id = ?";
            $stmtToken = $conexion->prepare($sqlToken);
            if (!$stmtToken) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmtToken->bind_param("i", $registro['id']);
            $stmtToken->execute();
            $stmtToken->close();

            $conexion->commit();

            // Mostrar mensaje de éxito estilizado
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Contraseña restablecida</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        background: url('fondos/in.jpg') no-repeat center center fixed;
                        background-size: cover;
                        font-family: "Century Gothic", sans-serif;
                    }

                    .container-exito {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }

                    .card-exito {
                        background-color: rgba(0, 0, 0, 0.7);
                        padding: 40px 30px;
                        border-radius: 20px;
                        text-align: center;
                        color: #fff;
                        box-shadow: 0 0 15px rgba(0,0,0,0.6);
                        max-width: 400px;
                        width: 90%;
                        animation: fadeIn 1s ease-in-out;
                    }

                    .card-exito img {
                        width: 80px;
                        margin-bottom: 20px;
                    }

                    .card-exito h2 {
                        font-size: 26px;
                        color: #28a745;
                        margin-bottom: 10px;
                    }

                    .card-exito p {
                        font-size: 16px;
                        margin-bottom: 25px;
                    }

                    .btn-volver {
                        display: inline-block;
                        background-color: #007bff;
                        color: #fff;
                        padding: 12px 30px;
                        border: none;
                        border-radius: 8px;
                        text-decoration: none;
                        font-size: 16px;
                        transition: background-color 0.3s ease;
                    }

                    .btn-volver:hover {
                        background-color: #0056b3;
                    }
                    .logo-contraste {
                         width: 100px; /* o el tamaño que necesites */
                         height: auto;
                         background-color: #fff; /* fondo blanco para contraste */
                         padding: 10px;
                         border-radius: 12px;
                         box-shadow: 0 0 10px rgba(0, 0, 0, 0.8); /* sombra oscura para resaltar */
                         display: inline-block;
                       }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: scale(0.95); }
                        to { opacity: 1; transform: scale(1); }
                    }
                </style>
            </head>
            <body>
                <div class="container-exito">
                    <div class="card-exito">
                        <img src="fondos/logo.webp" alt="Éxito" class="logo-contraste">
                        <h2>¡Contraseña restablecida!</h2>
                        <p>Tu nueva contraseña fue guardada correctamente. Ahora podés iniciar sesión.</p>
                        <a href="inicio.php" class="btn-volver">Ir al Inicio</a>
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;

        } catch (Exception $e) {
            $conexion->rollback();
            mostrarMensaje("Error al actualizar la contraseña. Intenta nuevamente.", 'error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="cabecera.css" />
</head>
<body>
    <form method="post" action="">
        <h1>Restablecer Contraseña</h1>

        <p>Ingrese la nueva contraseña:</p>
        <input type="password" name="password" placeholder="Nueva contraseña" required minlength="6" />

        <p>Confirme la nueva contraseña:</p>
        <input type="password" name="password2" placeholder="Confirmar contraseña" required minlength="6" />

        <input type="submit" value="Restablecer contraseña" />
    </form>
</body>
</html>

