<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="cabecera.css">
</head>
<body>
    <form action="procesar_recuperacion.php" method="post">
        <h1 class="animate__animated animate__backInLeft">Recuperar Contraseña</h1>

        <p>Ingrese su correo electrónico registrado:</p>
        <input type="email" name="correo" placeholder="Correo electrónico" required>

        <input type="submit" value="Enviar instrucciones">

        <p>
            <a href="inicio.php" class="recuperar">Volver al inicio de sesión</a>
        </p>
    </form>
</body>
</html>

