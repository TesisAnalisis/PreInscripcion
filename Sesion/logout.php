<?php
session_start();

// Evitar que el navegador guarde caché de esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Eliminar todas las variables de sesión
$_SESSION = array();
session_unset();

// Destruir la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir completamente la sesión
session_destroy();

// Redirigir al inicio de sesión
header("Location: http://localhost/PreInscripcion/PreInscripcion/");
exit();
?>

