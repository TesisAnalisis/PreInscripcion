<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Notificación
$mensaje = "";

// Eliminar comentario individual
if(isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    if($conn->query("DELETE FROM comentarios WHERE id=$id")) {
        $mensaje = "Comentario eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar comentario.";
    }
}

// Eliminar todos los comentarios
if(isset($_POST['delete_all'])) {
    if($conn->query("TRUNCATE TABLE comentarios")) {
        $mensaje = "Todos los comentarios fueron eliminados.";
    } else {
        $mensaje = "Error al eliminar comentarios.";
    }
}

// Obtener todos los comentarios
$result = $conn->query("SELECT * FROM comentarios");
if(!$result) {
    die("Error al obtener comentarios: " . $conn->error);
}

// Total de comentarios
$totalComentarios = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Comentarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Administración de Comentarios</h2>

    <!-- Total de comentarios -->
    <div class="mb-3">
        <div class="alert alert-info">Total de comentarios: <?= $totalComentarios ?></div>
    </div>

    <!-- Botón eliminar todos -->
    <form method="post" class="mb-3">
        <button type="submit" name="delete_all" class="btn btn-danger" onclick="return confirm('¿Seguro quieres eliminar todos los comentarios?');">Eliminar Todos</button>
    </form>

    <!-- Tabla de comentarios -->
    <div class="table-responsive">
        <table id="comentariosTable" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Comentario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['correo']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?= $row['id'] ?>">Ver</button>

                        <!-- Modal para ver comentario completo -->
                        <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Comentario de <?= htmlspecialchars($row['nombre']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <p><?= nl2br(htmlspecialchars($row['comentario'])) ?></p>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este comentario?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#comentariosTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ comentarios",
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            }
        }
    });
});
</script>
</body>
</html>


