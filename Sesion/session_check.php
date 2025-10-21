<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión
    header("Location: inicio.php");
    exit();
}

// Verificar el tiempo de inactividad (30 minutos)
$inactividad = 1800; // 30 minutos en segundos
if (isset($_SESSION['timeout'])) {
    $session_life = time() - $_SESSION['timeout'];
    if ($session_life > $inactividad) {
        session_unset();
        session_destroy();
        header("Location: inicio.php?msg=timeout");
        exit();
    }
}

// Actualizar el tiempo de la última actividad
$_SESSION['timeout'] = time();

// Prevenir caching de páginas privadas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
