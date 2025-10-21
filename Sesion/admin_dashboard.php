<?php
// Configuración de la base de datos
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

// Consultas para obtener estadísticas
$total_usuarios = 0;
$preinscripciones_completadas = 0;
$usuarios_por_genero = ['Masculino' => 0, 'Femenino' => 0, 'Otro' => 0];
$usuarios_por_nacionalidad = [];
$usuarios_por_edad = [
    '18-20' => 0,
    '21-25' => 0,
    '26-30' => 0,
    '31+' => 0
];

// Obtener total de usuarios
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_usuarios = $row['total'];
}

// Obtener preinscripciones completadas (todos los documentos subidos)
$sql = "SELECT COUNT(*) as completadas FROM usuarios 
        WHERE foto_anverso_cedula IS NOT NULL 
        AND foto_reverso_cedula IS NOT NULL 
        AND foto_anverso_certificado IS NOT NULL 
        AND foto_reverso_certificado IS NOT NULL 
        AND antecedente_policial IS NOT NULL 
        AND cert_medic IS NOT NULL 
        AND cert_nacim IS NOT NULL
        AND foto_carnet IS NOT NULL";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $preinscripciones_completadas = $row['completadas'];
}

// Obtener distribución por género
$sql = "SELECT sexo, COUNT(*) as cantidad FROM usuarios GROUP BY sexo";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $genero = $row['sexo'] ?: 'No especificado';
        $usuarios_por_genero[$genero] = $row['cantidad'];
    }
}

// Obtener distribución por nacionalidad (top 5)
$sql = "SELECT nacionalidad, COUNT(*) as cantidad FROM usuarios 
        GROUP BY nacionalidad ORDER BY cantidad DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios_por_nacionalidad[$row['nacionalidad']] = $row['cantidad'];
    }
}

// Obtener distribución por grupos de edad
$sql = "SELECT fecha_nacimiento FROM usuarios WHERE fecha_nacimiento IS NOT NULL";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fecha_nacimiento = new DateTime($row['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;
        
        if ($edad >= 18 && $edad <= 20) {
            $usuarios_por_edad['18-20']++;
        } elseif ($edad >= 21 && $edad <= 25) {
            $usuarios_por_edad['21-25']++;
        } elseif ($edad >= 26 && $edad <= 30) {
            $usuarios_por_edad['26-30']++;
        } elseif ($edad > 30) {
            $usuarios_por_edad['31+']++;
        }
    }
}

// Obtener distribución por carrera
$carreras_data = [];
$sql = "SELECT carrera, COUNT(*) as cantidad FROM usuarios GROUP BY carrera";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $carreras_data[$row['carrera']] = $row['cantidad'];
    }
}

// Obtener distribución por distrito
$distritos_data = [];
$sql = "SELECT distrito, COUNT(*) as cantidad FROM usuarios GROUP BY distrito ORDER BY cantidad DESC LIMIT 10";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $distritos_data[$row['distrito']] = $row['cantidad'];
    }
}

// Obtener tasa de finalización (porcentaje de usuarios que completaron el proceso)
$tasa_finalizacion = $total_usuarios > 0 ? round(($preinscripciones_completadas / $total_usuarios) * 100, 2) : 0;

// Obtener último comentario recibido
$ultimo_comentario = [];
$sql = "SELECT nombre, correo, comentario, fecha_creacion FROM comentarios ORDER BY fecha_creacion DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $ultimo_comentario = $result->fetch_assoc();
}

// Obtener cantidad de administradores y alumnos
$administradores = 0;
$alumnos = 0;
$sql = "SELECT rol_id, COUNT(*) as cantidad FROM usuarios GROUP BY rol_id";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['rol_id'] == 1) {
            $administradores = $row['cantidad'];
        } elseif ($row['rol_id'] == 2) {
            $alumnos = $row['cantidad'];
        }
    }
}

// Obtener últimos usuarios registrados
$ultimos_usuarios = [];
$sql = "SELECT nombre, apellido, carrera, fecha_registro, rol_id FROM usuarios ORDER BY fecha_registro DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $ultimos_usuarios[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Preinscripción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --success: #2ecc71;
            --info: #17a2b8;
            --warning: #f39c12;
            --danger: #e74c3c;
            --dark: #34495e;
            --light: #f8f9fa;
        }
        
        body {
            background-color: #f5f7f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), #2980b9);
            color: white;
            padding: 20px 0;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .bg-primary-light { background-color: rgba(52, 152, 219, 0.15); color: var(--primary); }
        .bg-success-light { background-color: rgba(46, 204, 113, 0.15); color: var(--success); }
        .bg-info-light { background-color: rgba(23, 162, 184, 0.15); color: var(--info); }
        .bg-warning-light { background-color: rgba(243, 156, 18, 0.15); color: var(--warning); }
        .bg-danger-light { background-color: rgba(231, 76, 60, 0.15); color: var(--danger); }
        .bg-dark-light { background-color: rgba(52, 73, 94, 0.15); color: var(--dark); }
        
        .card-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .card-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .content-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .content-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .comment-card {
            background: #f8f9fa;
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .comment-author {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .comment-date {
            font-size: 12px;
            color: #6c757d;
        }
        
        .comment-text {
            margin-top: 10px;
            color: #343a40;
        }
        
        .user-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-admin {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--danger);
        }
        
        .badge-student {
            background-color: rgba(46, 204, 113, 0.15);
            color: var(--success);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
                    <p class="mb-0">Sistema de Preinscripción - Estadísticas y Reportes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="dashboard-card">
                <div class="card-icon bg-primary-light">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-value"><?php echo $total_usuarios; ?></div>
                <div class="card-label">Total de Postulantes</div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon bg-success-light">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-value"><?php echo $preinscripciones_completadas; ?></div>
                <div class="card-label">Preinscripciones Completas</div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon bg-info-light">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="card-value"><?php echo $tasa_finalizacion; ?>%</div>
                <div class="card-label">Tasa de Finalización</div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon bg-warning-light">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-value"><?php echo count($carreras_data); ?></div>
                <div class="card-label">Carreras Solicitadas</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <!-- Gráfico de distribución por carrera -->
                    <div class="col-md-6 mb-4">
                        <div class="content-container">
                            <div class="content-header">
                                <h3 class="content-title">Distribución por Carrera</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="carreraChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de distribución por género -->
                    <div class="col-md-6 mb-4">
                        <div class="content-container">
                            <div class="content-header">
                                <h3 class="content-title">Distribución por Género</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="generoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Gráfico de distribución por distrito -->
                    <div class="col-md-6 mb-4">
                        <div class="content-container">
                            <div class="content-header">
                                <h3 class="content-title">Top 10 Distritos</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="distritoChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de distribución por edad -->
                    <div class="col-md-6 mb-4">
                        <div class="content-container">
                            <div class="content-header">
                                <h3 class="content-title">Distribución por Edad</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="edadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Último comentario -->
                <div class="content-container mb-4">
                    <div class="content-header">
                        <h3 class="content-title">Último Comentario</h3>
                        <a href="ver_comentarios.php" class="btn btn-sm btn-primary">Ver todos</a>
                    </div>
                    <?php if (!empty($ultimo_comentario)): ?>
                    <div class="comment-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="comment-author"><?php echo htmlspecialchars($ultimo_comentario['nombre']); ?></span>
                            <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($ultimo_comentario['fecha_creacion'])); ?></span>
                        </div>
                        <p class="comment-text"><?php echo htmlspecialchars($ultimo_comentario['comentario']); ?></p>
                        <div class="text-end">
                            <small class="text-muted"><?php echo htmlspecialchars($ultimo_comentario['correo']); ?></small>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted py-3">No hay comentarios aún</p>
                    <?php endif; ?>
                </div>

                <!-- Distribución de roles -->
                <div class="content-container mb-4">
                    <div class="content-header">
                        <h3 class="content-title">Distribución de Roles</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="rolesChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Administradores</span>
                            <span class="fw-bold"><?php echo $administradores; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Alumnos</span>
                            <span class="fw-bold"><?php echo $alumnos; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos usuarios registrados -->
        <div class="content-container">
            <div class="content-header">
                <h3 class="content-title">Últimos Usuarios Registrados</h3>
                <a href="admin.php?action=view" class="btn btn-primary">Ver Todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Carrera</th>
                            <th>Tipo</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimos_usuarios)): ?>
                            <?php foreach ($ultimos_usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['carrera']); ?></td>
                                    <td>
                                        <?php if ($usuario['rol_id'] == 1): ?>
                                            <span class="user-badge badge-admin">Administrador</span>
                                        <?php else: ?>
                                            <span class="user-badge badge-student">Alumno</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">No hay usuarios registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de distribución por carrera
        const carreraCtx = document.getElementById('carreraChart').getContext('2d');
        const carreraChart = new Chart(carreraCtx, {
            type: 'pie',
            data: {
                labels: [<?php echo implode(',', array_map(function($v) { return "'" . addslashes($v) . "'"; }, array_keys($carreras_data))); ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_values($carreras_data)); ?>],
                    backgroundColor: [
                        '#3498db', '#2ecc71', '#e74c3c', '#f39c12', 
                        '#9b59b6', '#1abc9c', '#d35400', '#34495e',
                        '#16a085', '#27ae60', '#8e44ad', '#f1c40f'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Gráfico de distribución por género
        const generoCtx = document.getElementById('generoChart').getContext('2d');
        const generoChart = new Chart(generoCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php echo implode(',', array_map(function($v) { return "'" . addslashes($v) . "'"; }, array_keys($usuarios_por_genero))); ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_values($usuarios_por_genero)); ?>],
                    backgroundColor: [
                        '#3498db', '#e74c3c', '#2ecc71'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Gráfico de distribución por distrito
        const distritoCtx = document.getElementById('distritoChart').getContext('2d');
        const distritoChart = new Chart(distritoCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode(',', array_map(function($v) { return "'" . addslashes($v) . "'"; }, array_keys($distritos_data))); ?>],
                datasets: [{
                    label: 'Número de Postulantes',
                    data: [<?php echo implode(',', array_values($distritos_data)); ?>],
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de distribución por edad
        const edadCtx = document.getElementById('edadChart').getContext('2d');
        const edadChart = new Chart(edadCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode(',', array_map(function($v) { return "'" . addslashes($v) . "'"; }, array_keys($usuarios_por_edad))); ?>],
                datasets: [{
                    label: 'Postulantes',
                    data: [<?php echo implode(',', array_values($usuarios_por_edad)); ?>],
                    backgroundColor: '#2ecc71'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de distribución de roles
        const rolesCtx = document.getElementById('rolesChart').getContext('2d');
        const rolesChart = new Chart(rolesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Administradores', 'Alumnos'],
                datasets: [{
                    data: [<?php echo $administradores; ?>, <?php echo $alumnos; ?>],
                    backgroundColor: [
                        '#e74c3c',
                        '#2ecc71'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
