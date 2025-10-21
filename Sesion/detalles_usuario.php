<?php
// ============================
// Detalles de Usuario - Versión Simplificada
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

// Obtener ID desde parámetro GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta para obtener datos del usuario
    $sql = "SELECT id, nombre, apellido, cedula_numero, correo, telefono, distrito, 
                   colegio_egresado, carrera, anio_egreso, condicion_medica
            FROM usuarios 
            WHERE id = ? AND rol_id = 2";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        die("No se encontró ningún usuario con el ID proporcionado.");
    }
    
    $stmt->close();
} else {
    die("No se proporcionó ID de usuario.");
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Alumno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background-color: #3498db;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        
        .card-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
        }
        
        .info-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            width: 150px;
        }
        
        .btn-back {
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h4>Detalles del Alumno</h4>
        </div>

        <!-- Información Personal -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Nombre completo:</span>
                    <?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Cédula:</span>
                    <?php echo $usuario['cedula_numero']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    <?php echo $usuario['telefono']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Correo electrónico:</span>
                    <?php echo $usuario['correo']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Distrito:</span>
                    <?php echo $usuario['distrito']; ?>
                </div>
            </div>
        </div>

        <!-- Información Académica -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información Académica</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Colegio de egreso:</span>
                    <?php echo $usuario['colegio_egresado']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Año de egreso:</span>
                    <?php echo $usuario['anio_egreso']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Carrera de interés:</span>
                    <?php echo $usuario['carrera']; ?>
                </div>
            </div>
        </div>

        <!-- Condición Médica (solo si existe) -->
        <?php if (!empty($usuario['condicion_medica'])): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Condición Médica</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <?php echo $usuario['condicion_medica']; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <div class="text-center">
    <a href="admin.php" class="btn btn-back">Volver Atrás</a>
</div>   

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
