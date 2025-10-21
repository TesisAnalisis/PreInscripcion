<?php
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

// Consulta para obtener todos los datos de usuarios
$sql_usuarios = "SELECT id, nombre, apellido, cedula_numero, correo, telefono, distrito, 
                        colegio_egresado, carrera, anio_egreso, condicion_medica
                 FROM usuarios WHERE rol_id = 2";

$result_usuarios = $conn->query($sql_usuarios);

// Verificar si la consulta fue exitosa
if ($result_usuarios === false) {
    die("Error en la consulta: " . $conn->error);
}

$datos_usuarios = array();
$estadisticas = array(
    'total_inscritos' => 0,
    'carreras' => array(),
    'distritos' => array(),
    'colegios' => array(),
    'anios_egreso' => array(),
    'condiciones_medicas' => array()
);

if ($result_usuarios->num_rows > 0) {
    while($row = $result_usuarios->fetch_assoc()) {
        $datos_usuarios[] = $row;
        
        // Calcular estadísticas
        $estadisticas['total_inscritos']++;
        
        // Carreras
        if (!isset($estadisticas['carreras'][$row['carrera']])) {
            $estadisticas['carreras'][$row['carrera']] = 0;
        }
        $estadisticas['carreras'][$row['carrera']]++;
        
        // Distritos
        if (!isset($estadisticas['distritos'][$row['distrito']])) {
            $estadisticas['distritos'][$row['distrito']] = 0;
        }
        $estadisticas['distritos'][$row['distrito']]++;
        
        // Colegios
        if (!isset($estadisticas['colegios'][$row['colegio_egresado']])) {
            $estadisticas['colegios'][$row['colegio_egresado']] = 0;
        }
        $estadisticas['colegios'][$row['colegio_egresado']]++;
        
        // Años de egreso
        if (!isset($estadisticas['anios_egreso'][$row['anio_egreso']])) {
            $estadisticas['anios_egreso'][$row['anio_egreso']] = 0;
        }
        $estadisticas['anios_egreso'][$row['anio_egreso']]++;
        
        // Condiciones médicas
        $condicion = $row['condicion_medica'] ? $row['condicion_medica'] : 'Ninguna';
        if (!isset($estadisticas['condiciones_medicas'][$condicion])) {
            $estadisticas['condiciones_medicas'][$condicion] = 0;
        }
        $estadisticas['condiciones_medicas'][$condicion]++;
    }
}

// Obtener datos para filtros
$carreras = array_keys($estadisticas['carreras']);
$distritos = array_keys($estadisticas['distritos']);
$colegios = array_keys($estadisticas['colegios']);
$anios_egreso = array_keys($estadisticas['anios_egreso']);
sort($anios_egreso);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Preinscripción</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f7f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            background-color: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--secondary-color);
        }
        
        .stat-card p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .btn-report {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .btn-report:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .btn-download {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-download:hover {
            background-color: #c0392b;
            color: white;
        }
        
        .filter-section {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .dataTables_wrapper {
            padding: 0 10px;
        }
        
        /* Tabs personalizadas */
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 10px 15px;
        }
        
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid var(--primary-color);
            color: var(--primary-color);
            background-color: transparent;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .stat-card h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 992px) {
            .chart-container {
                height: 250px;
            }
            
            .stat-card {
                padding: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .stat-card h2 {
                font-size: 1.8rem;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .header p {
                font-size: 1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .card-header .btn {
                align-self: flex-end;
            }
            
            .filter-section .col-md-3 {
                margin-bottom: 15px;
            }
            
            .filter-section .text-end {
                text-align: left !important;
            }
            
            .filter-section .ms-2 {
                margin-left: 0 !important;
                margin-top: 10px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            #tabla-datos {
                font-size: 0.85rem;
            }
            
            #tabla-datos td, #tabla-datos th {
                padding: 0.5rem;
            }
            
            .chart-container {
                height: 220px;
            }
            
            .nav-tabs .nav-link {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .header {
                padding: 15px;
            }
            
            .stat-card {
                padding: 15px 10px;
            }
            
            .stat-card i {
                font-size: 2rem;
            }
            
            .stat-card h2 {
                font-size: 1.6rem;
            }
            
            .filter-section {
                padding: 10px;
            }
            
            .card-header {
                padding: 10px 15px;
            }
            
            .btn-report, .btn-download {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
            
            .chart-container {
                height: 200px;
            }
            
            /* Ajustes para botones en la tabla en dispositivos muy pequeños */
            #tabla-datos .btn {
                font-size: 0.8rem;
                padding: 4px 8px;
            }
            
            /* Ocultar columnas menos importantes en móviles */
            #tabla-datos td:nth-child(4), 
            #tabla-datos th:nth-child(4),
            #tabla-datos td:nth-child(5), 
            #tabla-datos th:nth-child(5) {
                display: none;
            }
            
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 400px) {
            .header h1 {
                font-size: 1.5rem;
            }
            
            .stat-card {
                padding: 10px 5px;
            }
            
            .stat-card i {
                font-size: 1.5rem;
            }
            
            .stat-card h2 {
                font-size: 1.4rem;
            }
            
            /* Ocultar más columnas en pantallas muy pequeñas */
            #tabla-datos td:nth-child(6), 
            #tabla-datos th:nth-child(6) {
                display: none;
            }
            
            .btn-group .btn {
                font-size: 0.75rem;
            }
        }
        
        /* Ajustes para modo landscape en móviles */
        @media (max-height: 500px) and (orientation: landscape) {
            .header {
                padding: 10px;
                margin-bottom: 15px;
            }
            
            .stat-card {
                padding: 10px 5px;
            }
            
            .chart-container {
                height: 180px;
            }
        }
        
        /* Mejoras para tablets */
        @media (min-width: 769px) and (max-width: 1024px) {
            .stat-card h2 {
                font-size: 2rem;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .filter-section .col-md-3 {
                margin-bottom: 15px;
            }
        }
        
        /* Animaciones para gráficos */
        .chart-card {
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* Mejoras visuales para las tarjetas de estadísticas */
        .stat-card-1 i { color: var(--primary-color); }
        .stat-card-2 i { color: var(--success-color); }
        .stat-card-3 i { color: var(--warning-color); }
        .stat-card-4 i { color: var(--accent-color); }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header text-center">
            <h1><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h1>
            <p class="lead">Sistema de Preinscripción - Datos en tiempo real</p>
        </div>

        <!-- Estadísticas generales -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card stat-card-1">
                    <i class="fas fa-users"></i>
                    <h2 id="total-inscritos"><?php echo $estadisticas['total_inscritos']; ?></h2>
                    <p>Total Inscritos</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card stat-card-2">
                    <i class="fas fa-graduation-cap"></i>
                    <h2 id="total-carreras"><?php echo count($estadisticas['carreras']); ?></h2>
                    <p>Carreras Solicitadas</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card stat-card-3">
                    <i class="fas fa-heartbeat"></i>
                    <h2 id="total-condiciones"><?php echo count($estadisticas['condiciones_medicas']); ?></h2>
                    <p>Tipos de Condiciones</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card stat-card-4">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2 id="total-distritos"><?php echo count($estadisticas['distritos']); ?></h2>
                    <p>Distritos</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Carrera</label>
                    <select class="form-select" id="filtro-carrera">
                        <option value="">Todas las carreras</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera; ?>"><?php echo $carrera; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Distrito</label>
                    <select class="form-select" id="filtro-distrito">
                        <option value="">Todos los distritos</option>
                        <?php foreach ($distritos as $distrito): ?>
                            <option value="<?php echo $distrito; ?>"><?php echo $distrito; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Colegio</label>
                    <select class="form-select" id="filtro-colegio">
                        <option value="">Todos los colegios</option>
                        <?php foreach ($colegios as $colegio): ?>
                            <option value="<?php echo $colegio; ?>"><?php echo $colegio; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Año de Egreso</label>
                    <select class="form-select" id="filtro-anio">
                        <option value="">Todos los años</option>
                        <?php foreach ($anios_egreso as $anio): ?>
                            <option value="<?php echo $anio; ?>"><?php echo $anio; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 text-end mt-2">
                    <button class="btn btn-report" id="aplicar-filtros">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                    <button class="btn btn-download ms-2" id="descargar-pdf">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs para gráficos -->
        <ul class="nav nav-tabs mb-3" id="chartsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="carreras-tab" data-bs-toggle="tab" data-bs-target="#carreras" type="button" role="tab">Por Carreras</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="distritos-tab" data-bs-toggle="tab" data-bs-target="#distritos" type="button" role="tab">Por Distritos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="colegios-tab" data-bs-toggle="tab" data-bs-target="#colegios" type="button" role="tab">Por Colegios</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="medicas-tab" data-bs-toggle="tab" data-bs-target="#medicas" type="button" role="tab">Condiciones Médicas</button>
            </li>
        </ul>

        <div class="tab-content" id="chartsTabContent">
            <!-- Tab de Carreras -->
            <div class="tab-pane fade show active" id="carreras" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Inscritos por Carrera</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartCarreras', 'inscritos-por-carrera')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartCarreras"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Distribución por Carreras</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartCarrerasPorcentaje', 'distribucion-carreras-porcentaje')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartCarrerasPorcentaje"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab de Distritos -->
            <div class="tab-pane fade" id="distritos" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Distribución por Distritos</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartDistritos', 'distribucion-distritos')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartDistritos"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Top 5 Distritos</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartDistritosTop', 'top-5-distritos')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartDistritosTop"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab de Colegios -->
            <div class="tab-pane fade" id="colegios" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Distribución por Colegios (Top 10)</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartColegios', 'distribucion-colegios')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartColegios"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Distribución por Año de Egreso</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartAnios', 'distribucion-anios-egreso')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartAnios"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab de Condiciones Médicas -->
            <div class="tab-pane fade" id="medicas" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Distribución de Condiciones Médicas</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartCondicionesMedicas', 'condiciones-medicas')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartCondicionesMedicas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-header">
                                <span>Porcentaje de Condiciones Médicas</span>
                                <button class="btn btn-sm btn-report" onclick="descargarGrafico('chartCondicionesPorcentaje', 'condiciones-medicas-porcentaje')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartCondicionesPorcentaje"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de datos -->
        <div class="card">
            <div class="card-header">
                <span>Datos de Preinscripciones</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla-datos" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula</th>
                                <th>Carrera</th>
                                <th>Distrito</th>
                                <th>Colegio</th>
                                <th>Año Egreso</th>
                                <th>Condición Médica</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos_usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></td>
                                <td><?php echo $usuario['cedula_numero']; ?></td>
                                <td><?php echo $usuario['carrera']; ?></td>
                                <td><?php echo $usuario['distrito']; ?></td>
                                <td><?php echo $usuario['colegio_egresado']; ?></td>
                                <td><?php echo $usuario['anio_egreso']; ?></td>
                                <td><?php echo $usuario['condicion_medica'] ? $usuario['condicion_medica'] : 'Ninguna'; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-report" onclick="descargarFicha('<?php echo $usuario['cedula_numero']; ?>')">
                                            <i class="fas fa-download me-1"></i> Ficha
                                        </button>
                                        <button class="btn btn-sm btn-download" onclick="verDetalles(<?php echo $usuario['id']; ?>)">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <!-- SheetJS para exportar a Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Variables globales
        let chartCarreras, chartCarrerasPorcentaje, chartDistritos, chartDistritosTop, chartColegios, chartAnios, chartCondicionesMedicas, chartCondicionesPorcentaje;
        let tablaDatos;
        let datosUsuarios = <?php echo json_encode($datos_usuarios); ?>;
        let estadisticas = <?php echo json_encode($estadisticas); ?>;

        // Cuando el documento está listo
        $(document).ready(function() {
            // Inicializar gráficos
            inicializarGraficos();
            
            // Inicializar tabla
            inicializarTabla();
            
            // Configurar evento para el botón de aplicar filtros
            $('#aplicar-filtros').click(function() {
                aplicarFiltros();
            });
            
            // Configurar evento para el botón de exportar PDF
            $('#descargar-pdf').click(function() {
                generarPDF();
            });
            
            // Configurar evento para el botón de exportar datos
            $('#exportar-datos').click(function() {
                exportarDatosExcel();
            });
        });

        // Función para inicializar los gráficos
        function inicializarGraficos() {
            // Gráfico de carreras (barras)
            const ctxCarreras = document.getElementById('chartCarreras').getContext('2d');
            chartCarreras = new Chart(ctxCarreras, {
                type: 'bar',
                data: {
                    labels: Object.keys(estadisticas.carreras),
                    datasets: [{
                        label: 'Inscritos por Carrera',
                        data: Object.values(estadisticas.carreras),
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Inscritos por Carrera'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Gráfico de carreras (porcentaje)
            const ctxCarrerasPorcentaje = document.getElementById('chartCarrerasPorcentaje').getContext('2d');
            const totalInscritos = estadisticas.total_inscritos;
            const porcentajesCarreras = Object.values(estadisticas.carreras).map(val => ((val / totalInscritos) * 100).toFixed(1));
            
            chartCarrerasPorcentaje = new Chart(ctxCarrerasPorcentaje, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(estadisticas.carreras),
                    datasets: [{
                        data: Object.values(estadisticas.carreras),
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#2ecc71', '#f39c12', 
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
                        },
                        title: {
                            display: true,
                            text: 'Distribución por Carreras (%)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const percentage = ((value / totalInscritos) * 100).toFixed(1);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfico de distritos
            const ctxDistritos = document.getElementById('chartDistritos').getContext('2d');
            chartDistritos = new Chart(ctxDistritos, {
                type: 'bar',
                data: {
                    labels: Object.keys(estadisticas.distritos),
                    datasets: [{
                        label: 'Cantidad de Estudiantes',
                        data: Object.values(estadisticas.distritos),
                        backgroundColor: '#2ecc71'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribución por Distritos'
                        }
                    }
                }
            });
            
            // Gráfico de Top 5 distritos
            const distritosData = Object.entries(estadisticas.distritos)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);
                
            const ctxDistritosTop = document.getElementById('chartDistritosTop').getContext('2d');
            chartDistritosTop = new Chart(ctxDistritosTop, {
                type: 'pie',
                data: {
                    labels: distritosData.map(item => item[0]),
                    datasets: [{
                        data: distritosData.map(item => item[1]),
                        backgroundColor: [
                            '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#e74c3c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Distritos con más Inscritos'
                        }
                    }
                }
            });
            
            // Gráfico de colegios (solo los 10 principales)
            const colegiosData = Object.entries(estadisticas.colegios)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);
                
            const ctxColegios = document.getElementById('chartColegios').getContext('2d');
            chartColegios = new Chart(ctxColegios, {
                type: 'doughnut',
                data: {
                    labels: colegiosData.map(item => item[0]),
                    datasets: [{
                        data: colegiosData.map(item => item[1]),
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
                            '#9b59b6', '#1abc9c', '#d35400', '#34495e',
                            '#16a085', '#27ae60'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Distribución por Colegios (Top 10)'
                        }
                    }
                }
            });
            
            // Gráfico de años de egreso
            const aniosData = Object.entries(estadisticas.anios_egreso)
                .sort((a, b) => a[0] - b[0]);
                
            const ctxAnios = document.getElementById('chartAnios').getContext('2d');
            chartAnios = new Chart(ctxAnios, {
                type: 'line',
                data: {
                    labels: aniosData.map(item => item[0]),
                    datasets: [{
                        label: 'Estudiantes por Año',
                        data: aniosData.map(item => item[1]),
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: '#3498db',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribución por Año de Egreso'
                        }
                    }
                }
            });
            
            // Gráfico de condiciones médicas
            const ctxCondicionesMedicas = document.getElementById('chartCondicionesMedicas').getContext('2d');
            chartCondicionesMedicas = new Chart(ctxCondicionesMedicas, {
                type: 'bar',
                data: {
                    labels: Object.keys(estadisticas.condiciones_medicas),
                    datasets: [{
                        label: 'Cantidad',
                        data: Object.values(estadisticas.condiciones_medicas),
                        backgroundColor: '#9b59b6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribución de Condiciones Médicas'
                        }
                    }
                }
            });
            
            // Gráfico de porcentaje de condiciones médicas
            const ctxCondicionesPorcentaje = document.getElementById('chartCondicionesPorcentaje').getContext('2d');
            chartCondicionesPorcentaje = new Chart(ctxCondicionesPorcentaje, {
                type: 'pie',
                data: {
                    labels: Object.keys(estadisticas.condiciones_medicas),
                    datasets: [{
                        data: Object.values(estadisticas.condiciones_medicas),
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
                            '#9b59b6', '#1abc9c', '#d35400', '#34495e'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Porcentaje de Condiciones Médicas'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const percentage = ((value / totalInscritos) * 100).toFixed(1);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Función para inicializar la tabla de datos
        function inicializarTabla() {
            tablaDatos = $('#tabla-datos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true
            });
        }

        // Función para aplicar filtros
        function aplicarFiltros() {
            const carrera = $('#filtro-carrera').val();
            const distrito = $('#filtro-distrito').val();
            const colegio = $('#filtro-colegio').val();
            const anio = $('#filtro-anio').val();
            
            // Aplicar filtros a la tabla
            tablaDatos.column(2).search(carrera).draw();
            tablaDatos.column(3).search(distrito).draw();
            tablaDatos.column(4).search(colegio).draw();
            tablaDatos.column(5).search(anio).draw();
            
            // Actualizar estadísticas
            const filteredData = tablaDatos.rows({ filter: 'applied' }).data();
            $('#total-inscritos').text(filteredData.length);
        }

        // Función para descargar un gráfico como imagen
        function descargarGrafico(canvasId, nombreArchivo) {
            const canvas = document.getElementById(canvasId);
            const url = canvas.toDataURL('image/png');
            
            const link = document.createElement('a');
            link.download = `${nombreArchivo}.png`;
            link.href = url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Función para descargar ficha individual
        function descargarFicha(cedula) {
            // Hacer una petición al servidor para generar el PDF
            window.open(`generar_ficha.php?cedula=${cedula}`, '_blank');
        }

        // Función para ver detalles del usuario
        function verDetalles(id) {
            // Redirigir a una página de detalles o mostrar un modal
            window.open(`detalles_usuario.php?id=${id}`, '_blank');
        }

        // Función para generar PDF del reporte general
        function generarPDF() {
            // Hacer una petición al servidor para generar el PDF
            window.open('generar_reporte.php', '_blank');
        }
    </script>
</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
