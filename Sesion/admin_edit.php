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
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Inicializar variables
$user_data = null;
$error = '';
$success = '';
$image_fields = [
    'foto_anverso_cedula', 'foto_reverso_cedula', 'foto_anverso_certificado',
    'foto_reverso_certificado', 'antecedente_policial', 'cert_medic',
    'cert_nacim', 'foto_carnet'
];

// Función para procesar archivos
function processFile($file, $existing_data = null) {
    if (!isset($file['tmp_name']) || !$file['tmp_name'] || !file_exists($file['tmp_name'])) {
        return $existing_data; // Mantener el valor existente si no se sube nuevo archivo
    }
    
    // Validar tipo de archivo
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return false; // Tipo de archivo no permitido
    }
    
    // Validar tamaño (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false; // Archivo demasiado grande
    }
    
    // Leer el contenido del archivo
    $fileContent = file_get_contents($file['tmp_name']);
    return $fileContent;
}

// Procesar acciones (editar, eliminar, etc.)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $user_id = intval($_GET['id']);
        
        // Obtener datos del usuario para editar
        $sql_user = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql_user);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        
        if ($user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
        } else {
            $error = "Usuario no encontrado.";
        }
        $stmt->close();
        
        // Procesar el formulario de edición si se envió
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recoger datos del formulario
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $cedula_numero = $_POST['cedula_numero'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $distrito = $_POST['distrito'] ?? '';
            $nacionalidad = $_POST['nacionalidad'] ?? '';
            $sexo = $_POST['sexo'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
            $anio_egreso = $_POST['anio_egreso'] ?? '';
            $colegio_egresado = $_POST['colegio_egresado'] ?? '';
            $carrera = $_POST['carrera'] ?? '';
            $condicion_medica = $_POST['condicion_medica'] ?? '';
            $condicion_especifica = $_POST['condicion_especifica'] ?? '';
            $como_se_entero = $_POST['referencia'] ?? '';
            
            // Procesar archivos (mantener existentes si no se suben nuevos)
            $file_updates = [];
            $file_params = [];
            $file_types = "";
            
            foreach ($image_fields as $field) {
                $processed_file = processFile($_FILES[$field] ?? null, $user_data[$field] ?? null);
                
                if ($processed_file === false) {
                    $error = "Error en el archivo $field: tipo no permitido o demasiado grande.";
                    break;
                }
                
                if ($processed_file !== $user_data[$field]) {
                    $file_updates[] = "$field = ?";
                    $file_params[] = $processed_file;
                    $file_types .= "b";
                }
            }
            
            if (empty($error)) {
                // Construir consulta de actualización
                $sql_update = "UPDATE usuarios SET 
                    nombre = ?, 
                    apellido = ?, 
                    cedula_numero = ?, 
                    correo = ?, 
                    telefono = ?, 
                    telefono_emergencia = ?, 
                    direccion = ?, 
                    distrito = ?, 
                    nacionalidad = ?, 
                    sexo = ?, 
                    fecha_nacimiento = ?, 
                    anio_egreso = ?, 
                    colegio_egresado = ?, 
                    carrera = ?, 
                    condicion_medica = ?, 
                    condicion_especifica = ?, 
                    como_se_entero = ?";
                
                // Agregar campos de archivo si es necesario
                if (!empty($file_updates)) {
                    $sql_update .= ", " . implode(", ", $file_updates);
                }
                
                $sql_update .= " WHERE id = ?";
                
                // Preparar parámetros
                $params = [
                    $nombre, $apellido, $cedula_numero, $correo, $telefono, 
                    $telefono_emergencia, $direccion, $distrito, $nacionalidad, 
                    $sexo, $fecha_nacimiento, $anio_egreso, $colegio_egresado, 
                    $carrera, $condicion_medica, $condicion_especifica, 
                    $como_se_entero
                ];
                
                // Tipos de parámetros
                $types = "sssssssssssssssss";
                
                // Agregar parámetros de archivo
                if (!empty($file_params)) {
                    $params = array_merge($params, $file_params);
                    $types .= $file_types;
                }
                
                // Agregar ID al final
                $params[] = $user_id;
                $types .= "i";
                
                // Ejecutar actualización
                $stmt = $conn->prepare($sql_update);
                
                if ($stmt) {
                    $stmt->bind_param($types, ...$params);
                    
                    // Para campos BLOB, necesitamos usar send_long_data
                    $blob_index = 17; // Después de los 17 parámetros regulares
                    foreach ($image_fields as $field) {
                        if (isset($_FILES[$field]['tmp_name']) && $_FILES[$field]['tmp_name']) {
                            $stmt->send_long_data($blob_index, $file_params[$blob_index - 17]);
                            $blob_index++;
                        }
                    }
                    
                    if ($stmt->execute()) {
                        $success = "Usuario actualizado correctamente.";
                        
                        // Actualizar los datos del usuario en la variable
                        $user_data = array_merge($user_data, [
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'cedula_numero' => $cedula_numero,
                            'correo' => $correo,
                            'telefono' => $telefono,
                            'telefono_emergencia' => $telefono_emergencia,
                            'direccion' => $direccion,
                            'distrito' => $distrito,
                            'nacionalidad' => $nacionalidad,
                            'sexo' => $sexo,
                            'fecha_nacimiento' => $fecha_nacimiento,
                            'anio_egreso' => $anio_egreso,
                            'colegio_egresado' => $colegio_egresado,
                            'carrera' => $carrera,
                            'condicion_medica' => $condicion_medica,
                            'condicion_especifica' => $condicion_especifica,
                            'como_se_entero' => $como_se_entero
                        ]);
                        
                        // Actualizar también los campos de imagen si se cambiaron
                        foreach ($image_fields as $field) {
                            if (isset($_FILES[$field]['tmp_name']) && $_FILES[$field]['tmp_name']) {
                                $user_data[$field] = processFile($_FILES[$field]);
                            }
                        }
                    } else {
                        $error = "Error al actualizar el usuario: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Error al preparar la consulta: " . $conn->error;
                }
            }
        }
    }
}

// Obtener facultades disponibles para el dropdown
$facultades = [];
$sql_facultades = "SELECT DISTINCT carrera FROM usuarios WHERE carrera IS NOT NULL AND carrera != '' ORDER BY carrera";
$result_facultades = $conn->query($sql_facultades);
if ($result_facultades && $result_facultades->num_rows > 0) {
    while($row = $result_facultades->fetch_assoc()) {
        $facultades[] = $row['carrera'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario - Sistema de Preinscripción</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2c3e50;
      --accent-color: #2980b9;
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
    
    .form-section {
      margin-bottom: 25px;
      padding: 20px;
      border-radius: 8px;
      background-color: #f8f9fa;
      border-left: 4px solid var(--primary-color);
    }
    
    .form-section h5 {
      color: var(--primary-color);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    
    .form-section h5 i {
      margin-right: 10px;
    }
    
    .required-field::after {
      content: " *";
      color: #e74c3c;
    }
    
    .image-preview {
      max-width: 100%;
      max-height: 200px;
      margin-top: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px;
    }
    
    .file-info {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <!-- Mostrar mensajes de éxito o error -->
    <?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $success; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo $error; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="content-container">
      <div class="content-header">
        <h2 class="content-title">
          <i class="fas fa-user-edit me-2"></i>
          <?php echo isset($user_data) ? "Editar Usuario" : "Usuario No Encontrado"; ?>
        </h2>
        <div>
          <a href="admin.php?action=view" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a la lista
          </a>
        </div>
      </div>

      <?php if (isset($user_data)): ?>
      <form method="POST" action="update_user.php" enctype="multipart/form-data">
        <!-- Campo oculto con el ID del usuario -->
        <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">
        <!-- Información Personal -->
        <div class="form-section">
          <h5><i class="fas fa-user"></i> Información Personal</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label required-field">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" 
                     value="<?php echo htmlspecialchars($user_data['nombre'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="apellido" class="form-label required-field">Apellido</label>
              <input type="text" class="form-control" id="apellido" name="apellido" 
                     value="<?php echo htmlspecialchars($user_data['apellido'] ?? ''); ?>" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="cedula_numero" class="form-label">Número de Cédula</label>
              <input type="text" class="form-control" id="cedula_numero" name="cedula_numero" 
                     value="<?php echo htmlspecialchars($user_data['cedula_numero'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label for="correo" class="form-label required-field">Correo Electrónico</label>
              <input type="email" class="form-control" id="correo" name="correo" 
                     value="<?php echo htmlspecialchars($user_data['correo'] ?? ''); ?>" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="nacionalidad" class="form-label">Nacionalidad</label>
              <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" 
                     value="<?php echo htmlspecialchars($user_data['nacionalidad'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label for="sexo" class="form-label">Género</label>
              <select class="form-select" id="sexo" name="sexo">
                <option value="">Seleccionar...</option>
                <option value="Masculino" <?php echo (isset($user_data['sexo']) && $user_data['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                <option value="Femenino" <?php echo (isset($user_data['sexo']) && $user_data['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo (isset($user_data['sexo']) && $user_data['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
              <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                     value="<?php echo htmlspecialchars($user_data['fecha_nacimiento'] ?? ''); ?>">
            </div>
          </div>
        </div>

        <!-- Información de Contacto -->
        <div class="form-section">
          <h5><i class="fas fa-address-book"></i> Información de Contacto</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="telefono" class="form-label required-field">Teléfono</label>
              <input type="tel" class="form-control" id="telefono" name="telefono" 
                     value="<?php echo htmlspecialchars($user_data['telefono'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="telefono_emergencia" class="form-label">Teléfono de Emergencia</label>
              <input type="tel" class="form-control" id="telefono_emergencia" name="telefono_emergencia" 
                     value="<?php echo htmlspecialchars($user_data['telefono_emergencia'] ?? ''); ?>">
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" name="direccion" 
                     value="<?php echo htmlspecialchars($user_data['direccion'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label for="distrito" class="form-label">Distrito</label>
              <input type="text" class="form-control" id="distrito" name="distrito" 
                     value="<?php echo htmlspecialchars($user_data['distrito'] ?? ''); ?>">
            </div>
          </div>
        </div>

        <!-- Información Académica -->
        <div class="form-section">
          <h5><i class="fas fa-graduation-cap"></i> Información Académica</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="colegio_egresado" class="form-label">Colegio de Egreso</label>
              <input type="text" class="form-control" id="colegio_egresado" name="colegio_egresado" 
                     value="<?php echo htmlspecialchars($user_data['colegio_egresado'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label for="anio_egreso" class="form-label">Año de Egreso</label>
              <input type="number" class="form-control" id="anio_egreso" name="anio_egreso" 
                     value="<?php echo htmlspecialchars($user_data['anio_egreso'] ?? ''); ?>" min="1900" max="2099">
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="carrera" class="form-label required-field">Facultad/Carrera</label>
              <select class="form-select" id="carrera" name="carrera" required>
                <option value="">Seleccionar facultad...</option>
                <?php foreach ($facultades as $facultad): ?>
                <option value="<?php echo htmlspecialchars($facultad); ?>" 
                  <?php echo (isset($user_data['carrera']) && $user_data['carrera'] == $facultad) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($facultad); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="referencia" class="form-label">¿Cómo se enteró de nosotros?</label>
              <select class="form-select" id="referencia" name="referencia">
                <option value="">Seleccionar...</option>
                <option value="Redes Sociales" <?php echo (isset($user_data['como_se_entero']) && $user_data['como_se_entero'] == 'Redes Sociales') ? 'selected' : ''; ?>>Redes Sociales</option>
                <option value="Amigos/Familia" <?php echo (isset($user_data['como_se_entero']) && $user_data['como_se_entero'] == 'Amigos/Familia') ? 'selected' : ''; ?>>Amigos/Familia</option>
                <option value="Internet" <?php echo (isset($user_data['como_se_entero']) && $user_data['como_se_entero'] == 'Internet') ? 'selected' : ''; ?>>Internet</option>
                <option value="Prensa" <?php echo (isset($user_data['como_se_entero']) && $user_data['como_se_entero'] == 'Prensa') ? 'selected' : ''; ?>>Prensa</option>
                <option value="Otro" <?php echo (isset($user_data['como_se_entero']) && $user_data['como_se_entero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Información Médica -->
        <div class="form-section">
          <h5><i class="fas fa-heartbeat"></i> Información Médica</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="condicion_medica" class="form-label">Condición Médica</label>
              <select class="form-select" id="condicion_medica" name="condicion_medica">
                <option value="">Seleccionar...</option>
                <option value="Ninguna" <?php echo (isset($user_data['condicion_medica']) && $user_data['condicion_medica'] == 'Ninguna') ? 'selected' : ''; ?>>Ninguna</option>
                <option value="Discapacidad Visual" <?php echo (isset($user_data['condicion_medica']) && $user_data['condicion_medica'] == 'Discapacidad Visual') ? 'selected' : ''; ?>>Discapacidad Visual</option>
                <option value="Discapacidad Auditiva" <?php echo (isset($user_data['condicion_medica']) && $user_data['condicion_medica'] == 'Discapacidad Auditiva') ? 'selected' : ''; ?>>Discapacidad Auditiva</option>
                <option value="Discapacidad Motriz" <?php echo (isset($user_data['condicion_medica']) && $user_data['condicion_medica'] == 'Discapacidad Motriz') ? 'selected' : ''; ?>>Discapacidad Motriz</option>
                <option value="Otra" <?php echo (isset($user_data['condicion_medica']) && $user_data['condicion_medica'] == 'Otra') ? 'selected' : ''; ?>>Otra</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="condicion_especifica" class="form-label">Condición Específica (si aplica)</label>
              <input type="text" class="form-control" id="condicion_especifica" name="condicion_especifica" 
                     value="<?php echo htmlspecialchars($user_data['condicion_especifica'] ?? ''); ?>">
            </div>
          </div>
        </div>

        <!-- Documentos e Imágenes -->
        <div class="form-section">
          <h5><i class="fas fa-file-image"></i> Documentos e Imágenes</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="foto_anverso_cedula" class="form-label">Foto Anverso Cédula</label>
              <input type="file" class="form-control" id="foto_anverso_cedula" name="foto_anverso_cedula" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['foto_anverso_cedula'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="foto_anverso_cedula">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
              <label for="foto_reverso_cedula" class="form-label">Foto Reverso Cédula</label>
              <input type="file" class="form-control" id="foto_reverso_cedula" name="foto_reverso_cedula" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['foto_reverso_cedula'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="foto_reverso_cedula">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="foto_anverso_certificado" class="form-label">Foto Anverso Certificado</label>
              <input type="file" class="form-control" id="foto_anverso_certificado" name="foto_anverso_certificado" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['foto_anverso_certificado'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="foto_anverso_certificado">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
              <label for="foto_reverso_certificado" class="form-label">Foto Reverso Certificado</label>
              <input type="file" class="form-control" id="foto_reverso_certificado" name="foto_reverso_certificado" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['foto_reverso_certificado'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="foto_reverso_certificado">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="antecedente_policial" class="form-label">Antecedente Policial</label>
              <input type="file" class="form-control" id="antecedente_policial" name="antecedente_policial" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['antecedente_policial'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="antecedente_policial">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
            <div class="col-md-4 mb-3">
              <label for="cert_medic" class="form-label">Certificado Médico</label>
              <input type="file" class="form-control" id="cert_medic" name="cert_medic" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['cert_medic'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="cert_medic">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
            <div class="col-md-4 mb-3">
              <label for="cert_nacim" class="form-label">Certificado de Nacimiento</label>
              <input type="file" class="form-control" id="cert_nacim" name="cert_nacim" accept="image/*,.pdf">
              <div class="file-info">JPEG, PNG, GIF o PDF (Máx. 5MB)</div>
              <?php if (!empty($user_data['cert_nacim'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="cert_nacim">Ver documento</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="foto_carnet" class="form-label">Foto Carnet</label>
              <input type="file" class="form-control" id="foto_carnet" name="foto_carnet" accept="image/*">
              <div class="file-info">JPEG, PNG o GIF (Máx. 5MB)</div>
              <?php if (!empty($user_data['foto_carnet'])): ?>
              <div class="mt-2">
                <span class="badge bg-success">Archivo existente cargado</span>
                <a href="#" class="ms-2 view-image" data-field="foto_carnet">Ver imagen</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between mt-4">
          <a href="admin.php?action=view" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar Cambios
          </button>
        </div>
      </form>
      <?php else: ?>
      <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo $error ?: "El usuario solicitado no existe o no se pudo cargar."; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal para visualizar imágenes -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="imageModalLabel">Visualizar Documento/Imagen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalImage" src="" class="img-fluid" alt="Documento">
          <iframe id="modalPdf" src="" style="width: 100%; height: 500px; display: none;"></iframe>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <a id="downloadLink" href="#" class="btn btn-primary" download>Descargar</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
  $(document).ready(function() {
    // Validación básica del formulario
    $('form').on('submit', function(e) {
      let valid = true;
      
      // Validar campos requeridos
      $('[required]').each(function() {
        if ($(this).val() === '') {
          $(this).addClass('is-invalid');
          valid = false;
        } else {
          $(this).removeClass('is-invalid');
        }
      });
      
      // Validar formato de email
      const email = $('#correo');
      if (email.val() !== '' && !isValidEmail(email.val())) {
        email.addClass('is-invalid');
        valid = false;
      }
      
      // Validar archivos
      $('input[type="file"]').each(function() {
        if (this.files.length > 0) {
          const file = this.files[0];
          const maxSize = 5 * 1024 * 1024; // 5MB
          
          if (file.size > maxSize) {
            $(this).addClass('is-invalid');
            valid = false;
          } else {
            $(this).removeClass('is-invalid');
          }
        }
      });
      
      if (!valid) {
        e.preventDefault();
        alert('Por favor, complete todos los campos requeridos correctamente y verifique el tamaño de los archivos (máximo 5MB).');
      }
    });
    
    function isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }
    
    // Visualizar imágenes/documentos
    $('.view-image').on('click', function(e) {
      e.preventDefault();
      const field = $(this).data('field');
      
      // Hacer una solicitud para obtener la imagen
      $.ajax({
        url: 'view_file.php',
        type: 'POST',
        data: {
          id: <?php echo $user_data['id'] ?? 0; ?>,
          field: field
        },
        xhrFields: {
          responseType: 'blob'
        },
        success: function(data) {
          const url = URL.createObjectURL(data);
          const modal = new bootstrap.Modal(document.getElementById('imageModal'));
          
          // Determinar si es PDF o imagen
          if (field === 'antecedente_policial' || field === 'cert_medic' || field === 'cert_nacim' || 
              field.endsWith('_cedula') || field.endsWith('_certificado')) {
            // Es probablemente un PDF
            $('#modalImage').hide();
            $('#modalPdf').attr('src', url).show();
          } else {
            // Es una imagen
            $('#modalPdf').hide();
            $('#modalImage').attr('src', url).show();
          }
          
          $('#downloadLink').attr('href', url).attr('download', field + '.<?php echo $user_data['id'] ?? 0; ?>');
          modal.show();
        },
        error: function() {
          alert('Error al cargar el archivo.');
        }
      });
    });
  });
  </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
