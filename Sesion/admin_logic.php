<?php
// admin_logic.php

// Configura la conexión (igual que en otros archivos)
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Error de conexión a base de datos: " . $conn->connect_error . "</div>");
}

// Consultas para estadísticas básicas
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'] ?? 0;
$totalComentarios = $conn->query("SELECT COUNT(*) AS total FROM comentarios")->fetch_assoc()['total'] ?? 0;

$conn->close();
?>

<style>
    .dashboard-container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: 0 auto;
    }
    .dashboard-header {
        font-size: 2rem;
        font-weight: 700;
        color: #2980b9;
        margin-bottom: 25px;
        text-align: center;
        letter-spacing: 1.5px;
    }
    .stats-grid {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    .stat-box {
        flex: 1 1 250px;
        background: #eaf3fc;
        border-radius: 15px;
        padding: 20px 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(41, 128, 185, 0.2);
        transition: transform 0.3s ease;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(41, 128, 185, 0.4);
    }
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: #2980b9;
        margin-bottom: 10px;
    }
    .stat-label {
        font-size: 1.25rem;
        color: #34495e;
        font-weight: 600;
    }
    .welcome-text {
        font-size: 1.1rem;
        color: #555;
        text-align: center;
        margin-bottom: 25px;
        line-height: 1.5;
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">Panel de Administración</div>
    <p class="welcome-text">
        Bienvenido al sistema de administración de preinscripciones.<br>
        Usa el menú lateral para navegar y gestionar usuarios, comentarios, y reportes.
    </p>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-number"><?php echo $totalUsuarios; ?></div>
            <div class="stat-label">Usuarios Registrados</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo $totalComentarios; ?></div>
            <div class="stat-label">Comentarios Recibidos</div>
        </div>
    </div>
</div>


