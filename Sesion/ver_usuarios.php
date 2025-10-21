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

// Mostrar mensajes de sesión
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Procesar acciones (eliminar, etc.)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Eliminar usuario
        $sql_delete = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el usuario: " . $stmt->error;
        }
        $stmt->close();
        
        // Redirigir para evitar reenvío del formulario
        header("Location: admin.php?action=view");
        exit();
    }
}

// Obtener facultades disponibles
$facultades = [];
$sql_facultades = "SELECT DISTINCT carrera FROM usuarios WHERE carrera IS NOT NULL AND carrera != '' ORDER BY carrera";
$result_facultades = $conn->query($sql_facultades);
if ($result_facultades && $result_facultades->num_rows > 0) {
    while($row = $result_facultades->fetch_assoc()) {
        $facultades[] = $row['carrera'];
    }
}

// Obtener estadísticas por facultad
$stats = [];
$sql_stats = "SELECT carrera, COUNT(*) as cantidad FROM usuarios WHERE carrera IS NOT NULL AND carrera != '' GROUP BY carrera";
$result_stats = $conn->query($sql_stats);
if ($result_stats && $result_stats->num_rows > 0) {
    while($row = $result_stats->fetch_assoc()) {
        $stats[$row['carrera']] = $row['cantidad'];
    }
}

// Obtener total de usuarios
$total_usuarios = 0;
$sql_total = "SELECT COUNT(*) as total FROM usuarios";
$result_total = $conn->query($sql_total);
if ($result_total && $result_total->num_rows > 0) {
    $row = $result_total->fetch_assoc();
    $total_usuarios = $row['total'];
}

// Filtrar por facultad si se ha seleccionado una
$facultad_filtro = "";
if (isset($_GET['facultad']) && !empty($_GET['facultad'])) {
    $facultad_filtro = $conn->real_escape_string($_GET['facultad']);
}

// Construir la consulta SQL
$sql = "SELECT id, nombre, apellido, correo, telefono, direccion, distrito, carrera, rol_id 
        FROM usuarios";
        
if (!empty($facultad_filtro)) {
    $sql .= " WHERE carrera = '$facultad_filtro'";
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

// Obtener datos de un usuario específico para ver detalles o editar
$user_data = null;
if (isset($_GET['action']) && ($_GET['action'] == 'view' || $_GET['action'] == 'edit') && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $sql_user = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Usuarios por Facultad</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
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
    }

    body {
      background-color: #f5f7f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      padding: 20px;
    }

    .content-container {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
    }

    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    .content-title {
      font-weight: 600;
      color: var(--dark-color);
      margin: 0;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      border-radius: 6px;
      padding: 8px 20px;
      font-weight: 500;
    }

    .btn-primary:hover {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 0.875rem;
    }

    .badge-admin {
      background-color: var(--primary-color);
      color: white;
    }

    .badge-user {
      background-color: var(--success-color);
      color: white;
    }

    .table-container {
      overflow-x: auto;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    thead th {
      background-color: #f8f9fa;
      padding: 12px 15px;
      text-align: left;
      font-weight: 600;
      color: var(--dark-color);
      border-bottom: 2px solid #eee;
    }

    tbody td {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
      vertical-align: middle;
    }

    tbody tr:hover {
      background-color: #f8f9fa;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .btn-view {
      background-color: #3498db;
      color: white;
    }

    .btn-edit {
      background-color: #f39c12;
      color: white;
    }

    .btn-delete {
      background-color: #e74c3c;
      color: white;
    }

    .search-container {
      display: flex;
      margin-bottom: 20px;
      gap: 10px;
      flex-wrap: wrap;
    }

    .search-input {
      flex: 1;
      border-radius: 6px;
      border: 1px solid #ddd;
      padding: 8px 15px;
      min-width: 200px;
    }

    .filter-select {
      border-radius: 6px;
      border: 1px solid #ddd;
      padding: 8px 15px;
      min-width: 250px;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1rem;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #7f8c8d;
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #bdc3c7;
    }

    .faculty-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .faculty-1 { background-color: #e8f4fc; color: #3498db; }
    .faculty-2 { background-color: #fce8e8; color: #e74c3c; }
    .faculty-3 { background-color: #f0fce8; color: #27ae60; }
    .faculty-4 { background-color: #f8e8fc; color: #9b59b6; }

    /* Modal styles */
    .modal-content {
      border-radius: 10px;
      border: none;
    }
    
    .modal-header {
      background-color: var(--primary-color);
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    
    .user-detail-item {
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }
    
    .user-detail-label {
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 5px;
    }
    
    .user-detail-value {
      color: #555;
    }
    
    .document-badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 4px;
      margin-right: 5px;
      margin-bottom: 5px;
      font-size: 0.8rem;
    }
    
    .document-present {
      background-color: #d4edda;
      color: #155724;
    }
    
    .document-missing {
      background-color: #f8d7da;
      color: #721c24;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .search-container {
        flex-direction: column;
      }

      .action-buttons {
        flex-wrap: wrap;
      }

      .table-container {
        border: 1px solid #eee;
      }

      thead {
        display: none;
      }

      tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 10px;
      }

      tbody td {
        display: block;
        text-align: right;
        padding: 10px;
        border-bottom: 1px solid #eee;
      }

      tbody td:before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        color: var(--dark-color);
      }

      .action-buttons {
        justify-content: center;
      }
    }

    .info-box {
      background-color: #e8f4fc;
      border-left: 4px solid var(--primary-color);
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    .stats-box {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      flex: 1;
      min-width: 180px;
      text-align: center;
    }

    .stat-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .stat-label {
      font-size: 0.9rem;
      color: #7f8c8d;
    }
    
    .filter-active {
      background-color: #d4edda;
      border-color: #c3e6cb;
    }
    
    .dataTables_wrapper {
      margin-top: 20px;
    }
    
    .dataTables_filter input {
      border-radius: 6px;
      border: 1px solid #ddd;
      padding: 5px 10px;
    }
    
    .dataTables_length select {
      border-radius: 6px;
      border: 1px solid #ddd;
      padding: 5px 10px;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <!-- Mostrar mensajes de éxito o error -->
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($success_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($error_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="content-container">
      <div class="content-header">
        <h2 class="content-title"><i class="fas fa-users me-2"></i>Gestión de Usuarios por Facultad</h2>
        <div>
          <a href="admin.php?action=add" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
          </a>
        </div>
      </div>

      <div class="info-box">
        <h5><i class="fas fa-filter me-2"></i>Sistema de Filtrado por Facultad</h5>
        <p class="mb-0">Selecciona una facultad para filtrar los usuarios que se inscribieron en esa facultad.</p>
      </div>

      <!-- Estadísticas rápidas -->
      <div class="stats-box">
        <div class="stat-card">
          <div class="stat-value"><?php echo $total_usuarios; ?></div>
          <div class="stat-label">Total Usuarios</div>
        </div>
        <?php 
        $color_classes = ['faculty-1', 'faculty-2', 'faculty-3', 'faculty-4'];
        $i = 0;
        foreach ($stats as $facultad => $cantidad): 
          $color_class = $color_classes[$i % count($color_classes)];
          $i++;
        ?>
        <div class="stat-card">
          <div class="stat-value"><?php echo $cantidad; ?></div>
          <div class="stat-label"><?php echo htmlspecialchars($facultad); ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <form method="GET" action="">
        <input type="hidden" name="action" value="view">
        <div class="search-container">
          <select name="facultad" id="facultyFilter" class="filter-select">
            <option value="">Todas las facultades</option>
            <?php foreach ($facultades as $facultad): ?>
            <option value="<?php echo htmlspecialchars($facultad); ?>" 
                    <?php echo ($facultad_filtro == $facultad) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($facultad); ?>
            </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary">Filtrar</button>
          <?php if (!empty($facultad_filtro)): ?>
          <a href="admin.php?action=view" class="btn btn-secondary">Limpiar Filtro</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="table-container">
        <table id="usersTable" class="table table-striped">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Información de Contacto</th>
              <th>Facultad</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && $result->num_rows > 0) {
              $color_classes = ['faculty-1', 'faculty-2', 'faculty-3', 'faculty-4'];
              $faculty_colors = [];
              $i = 0;
              
              while($row = $result->fetch_assoc()) {
                $initial = strtoupper(substr($row['nombre'], 0, 1));
                $fullName = htmlspecialchars($row['nombre'] . ' ' . $row['apellido']);
                $email = htmlspecialchars($row['correo']);
                $phone = htmlspecialchars($row['telefono']);
                $address = htmlspecialchars($row['direccion']);
                $district = htmlspecialchars($row['distrito']);
                $faculty = htmlspecialchars($row['carrera']);
                $role = $row['rol_id'] == 1 ? 'Administrador' : 'Usuario';
                $roleBadge = $row['rol_id'] == 1 ? 'badge-admin' : 'badge-user';
                
                // Asignar color consistente por facultad
                if (!isset($faculty_colors[$faculty])) {
                  $faculty_colors[$faculty] = $color_classes[$i % count($color_classes)];
                  $i++;
                }
                $facultyColorClass = $faculty_colors[$faculty];
                
                echo "<tr>";
                echo "<td data-label='Usuario'>
                        <div class='d-flex align-items-center'>
                          <div class='user-avatar me-3'>$initial</div>
                          <div>
                            <div class='fw-bold'>$fullName</div>
                            <small class='text-muted'>ID: " . $row['id'] . "</small>
                          </div>
                        </div>
                      </td>";
                echo "<td data-label='Contacto'>
                        <div>$email</div>
                        <small class='text-muted'>$phone</small>
                      </td>";
                echo "<td data-label='Facultad'>
                        <span class='faculty-badge $facultyColorClass'>$faculty</span>
                      </td>";
                echo "<td data-label='Rol'><span class='badge $roleBadge'>$role</span></td>";
                echo "<td data-label='Acciones'>
                        <div class='action-buttons'>
                          <button type='button' class='btn btn-view btn-sm view-user' data-user-id='" . $row['id'] . "' title='Ver detalles'>
                            <i class='fas fa-eye'></i>
                          </button>
                          <a href='admin_edit.php?action=edit&id=" . $row['id'] . "' class='btn btn-edit btn-sm' title='Editar'>
                            <i class='fas fa-edit'></i>
                          </a>
                          <button type='button' class='btn btn-delete btn-sm delete-user' data-user-id='" . $row['id'] . "' data-user-name='$fullName' title='Eliminar'>
                            <i class='fas fa-trash'></i>
                          </button>
                        </div>
                      </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5'>
                      <div class='empty-state'>
                        <i class='fas fa-users'></i>
                        <h4>No hay usuarios registrados</h4>
                        <p>" . (empty($facultad_filtro) ? 
                          "Comienza agregando nuevos usuarios al sistema" : 
                          "No hay usuarios en la facultad seleccionada") . "</p>
                        <a href='admin.php?action=add' class='btn btn-primary mt-2'>
                          <i class='fas fa-user-plus me-1'></i> Agregar Usuario
                        </a>
                      </div>
                    </td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal para ver detalles del usuario -->
  <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userDetailsModalLabel">Detalles del Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="userDetailsContent">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para confirmar eliminación -->
  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar Eliminación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar al usuario: <strong id="deleteUserName"></strong>?</p>
          <p class="text-danger">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <form id="deleteForm" method="POST" action="delete_user.php">
            <input type="hidden" name="user_id" id="userIdToDelete">
            <button type="submit" class="btn btn-danger">Eliminar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
  
  <script>

  $(document).ready(function() {
     $.fn.dataTable.ext.errMode = 'none';
    // Inicializar DataTables solo si aún no está inicializado
    if (!$.fn.DataTable.isDataTable('#usersTable')) {
      $('#usersTable').DataTable({
        language: {
          "decimal": "",
          "emptyTable": "No hay datos disponibles en la tabla",
          "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
          "infoEmpty": "Mostrando 0 a 0 de 0 registros",
          "infoFiltered": "(filtrado de _MAX_ registros totales)",
          "infoPostFix": "",
          "thousands": ",",
          "lengthMenu": "Mostrar _MENU_ registros",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "zeroRecords": "No se encontraron registros coincidentes",
          "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
          },
          "aria": {
            "sortAscending": ": activar para ordenar columna ascendente",
            "sortDescending": ": activar para ordenar columna descendente"
          }
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      });
    }

    // Ajustar para dispositivos móviles
    function adjustForMobile() {
      if ($(window).width() < 768) {
        $('.user-avatar').css({
          'width': '35px',
          'height': '35px',
          'font-size': '0.9rem'
        });
      } else {
        $('.user-avatar').css({
          'width': '40px',
          'height': '40px',
          'font-size': '1rem'
        });
      }
    }

    adjustForMobile();
    $(window).resize(adjustForMobile);

    // Manejar clic en botón de ver detalles
    $('.view-user').on('click', function() {
      const userId = $(this).data('user-id');
      
      // Mostrar modal de carga
      $('#userDetailsContent').html(`
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="mt-2">Cargando detalles del usuario...</p>
        </div>
      `);
      
      $('#userDetailsModal').modal('show');
      
      // Cargar detalles del usuario mediante AJAX
      $.ajax({
        url: 'get_user_details.php',
        method: 'GET',
        data: { id: userId },
        success: function(response) {
          $('#userDetailsContent').html(response);
        },
        error: function() {
          $('#userDetailsContent').html(`
            <div class="alert alert-danger">
              Error al cargar los detalles del usuario. Por favor, intente nuevamente.
            </div>
          `);
        }
      });
    });

    // Manejar clic en botón de eliminar
    $('.delete-user').on('click', function() {
      const userId = $(this).data('user-id');
      const userName = $(this).data('user-name');
      
      $('#deleteUserName').text(userName);
      $('#userIdToDelete').val(userId);
      $('#deleteConfirmModal').modal('show');
    });
  });
  </script>

</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
