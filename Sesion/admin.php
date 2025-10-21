<?php
// Incluir el verificador de sesión
require_once 'session_check.php';

// Verificar que el usuario es administrador
if ($_SESSION['rol'] != 1) {
    header("Location: perfil.php");
    exit();
}
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Preinscripciones</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables para tablas avanzadas -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #2980b9;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7f9;
            color: #333;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Layout principal */
        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(to bottom, var(--secondary-color), var(--dark-color));
            width: var(--sidebar-width);
            transition: all var(--transition-speed) ease;
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            min-height: 80px;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 8px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu-item:hover, .sidebar-menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--primary-color);
        }

        .sidebar-menu-item i {
            margin-right: 15px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-menu-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Contenido principal */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all var(--transition-speed) ease;
            padding: 20px;
            min-width: 0; /* Para evitar problemas de desbordamiento */
        }

        /* Header */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            font-size: clamp(1.5rem, 4vw, 2rem); /* Título responsivo */
        }

        .user-menu {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .user-info {
            margin-right: 15px;
            text-align: right;
        }

        .user-name {
            font-weight: 500;
            color: var(--dark-color);
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        .user-role {
            font-size: clamp(0.75rem, 1.5vw, 0.85rem);
            color: #7f8c8d;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        /* Tarjetas de dashboard */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .bg-primary-light {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--primary-color);
        }

        .bg-success-light {
            background-color: rgba(39, 174, 96, 0.2);
            color: var(--success-color);
        }

        .bg-warning-light {
            background-color: rgba(243, 156, 18, 0.2);
            color: var(--warning-color);
        }

        .bg-danger-light {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }

        .card-value {
            font-size: clamp(1.5rem, 4vw, 1.8rem);
            font-weight: 700;
            margin: 5px 0;
            color: var(--dark-color);
        }

        .card-label {
            color: #7f8c8d;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
        }

        /* Contenedor de contenido */
        .content-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden; /* Evitar desbordamientos */
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
            gap: 15px;
        }

        .content-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            font-size: clamp(1.2rem, 3vw, 1.5rem);
        }

        /* Botones */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            white-space: nowrap;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Tablas */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Mejor scroll en iOS */
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 600px; /* Mínimo para evitar que se comprima demasiado */
        }

        thead th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid #eee;
            white-space: nowrap;
        }

        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: clamp(0.7rem, 2vw, 0.8rem);
        }

        /* Formularios */
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .form-control {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Footer */
        .app-footer {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            margin-top: auto;
        }

        /* Overlay para móviles */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Toggle para móviles */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Estados responsivos */
        @media (max-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar-header h2, .sidebar-menu-text {
                display: none;
            }
            
            .sidebar-header {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .sidebar-menu-item {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .sidebar-menu-item i {
                margin-right: 0;
                font-size: 1.4rem;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed-width);
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .main-header {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            
            .user-menu {
                margin-top: 10px;
                justify-content: center;
                width: 100%;
            }
            
            .user-info {
                margin-right: 0;
                text-align: center;
            }
            
            .content-container {
                padding: 15px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                width: 100%;
                max-width: 300px;
                transform: translateX(0);
            }
            
            .sidebar.active .sidebar-header h2,
            .sidebar.active .sidebar-menu-text {
                display: block;
            }
            
            .sidebar.active .sidebar-header {
                justify-content: flex-start;
            }
            
            .sidebar.active .sidebar-menu-item {
                justify-content: flex-start;
                padding: 12px 20px;
            }
            
            .sidebar.active .sidebar-menu-item i {
                margin-right: 15px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            .main-header {
                margin-top: 70px; /* Espacio para el botón de menú */
            }
            
            table {
                font-size: 0.9rem;
            }
            
            thead th, tbody td {
                padding: 8px 10px;
            }
        }

        @media (max-width: 400px) {
            .dashboard-card {
                padding: 15px;
            }
            
            .card-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .content-container {
                padding: 10px;
            }
            
            .btn-primary {
                padding: 6px 15px;
                font-size: 0.9rem;
            }
        }

        /* Utilidades para mejorar la responsividad */
        .text-truncate-mobile {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .text-truncate-mobile {
                max-width: 150px;
            }
        }

        .hidden-mobile {
            display: block;
        }

        @media (max-width: 768px) {
            .hidden-mobile {
                display: none;
            }
        }

        .visible-mobile {
            display: none;
        }

        @media (max-width: 768px) {
            .visible-mobile {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="menu-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">FCA</div>
                <h2>Panel de Administracion</h2>
            </div>
            
            <div class="sidebar-menu">
                <a href="admin.php?action=dashboard" class="sidebar-menu-item <?php echo (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
                <a href="admin.php?action=add" class="sidebar-menu-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'add') ? 'active' : ''; ?>">
                    <i class="fas fa-user-plus"></i>
                    <span class="sidebar-menu-text">Agregar Usuario</span>
                </a>
                <a href="admin.php?action=view" class="sidebar-menu-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'view') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-menu-text">Ver Usuarios</span>
                </a>
                <a href="admin.php?action=comments" class="sidebar-menu-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'comments') ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span class="sidebar-menu-text">Comentarios</span>
                </a>
                <a href="admin.php?action=report" class="sidebar-menu-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'report') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span class="sidebar-menu-text">Reportes</span>
                </a>
                <a href="logout.php" class="sidebar-menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-menu-text">Cerrar Sesión</span>
                </a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="main-content">
            <!-- Header -->
            <div class="main-header">
                <h1 class="page-title">
                    <?php
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'add': echo 'Agregar Usuario'; break;
                            case 'view': echo 'Ver Usuarios'; break;
                            case 'comments': echo 'Comentarios'; break;
                            case 'report': echo 'Generar Reportes'; break;
                            default: echo 'Dashboard';
                        }
                    } else {
                        echo 'Dashboard';
                    }
                    ?>
                </h1>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name">Admin User</div>
                        <div class="user-role">Administrador</div>
                    </div>
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <!-- Contenido dinámico -->
            <div class="content-container">
                <div class="content">
                    <?php 
                    if (isset($_GET['action'])) {
                        if ($_GET['action'] === 'add') {
                            include 'agregar_usuario.php';
                        } elseif ($_GET['action'] === 'view') {
                            include 'ver_usuarios.php';
                        } elseif ($_GET['action'] === 'comments') {
                            include 'ver_comentarios.php';
                        } elseif ($_GET['action'] === 'report') {
                            include 'reporte.php';
                        } else {
                            include 'admin_dashboard.php';
                        }
                    } else {
                        include 'admin_dashboard.php';
                    }
                    ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="app-footer">
                <p>&copy; 2025 Facultad de Ciencias Aplicadas - Todos los derechos reservados</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        // Toggle sidebar en móviles
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });
        
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        // Cerrar sidebar al hacer clic en un enlace en dispositivos móviles
        if (window.innerWidth <= 576) {
            const sidebarLinks = document.querySelectorAll('.sidebar-menu-item');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });
        }
        
        // Ajustar el diseño cuando cambia el tamaño de la ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 576) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Inicializar DataTables si existe una tabla
        $(document).ready(function() {
            if ($('#usersTable').length) {
                $('#usersTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
                    },
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 2, targets: -1 }
                    ]
                });
            }
        });
    </script>
</body>
</html>
