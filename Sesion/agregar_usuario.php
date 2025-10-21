<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

// Procesar el formulario cuando se envía
$mensaje = "";
$tipo_mensaje = ""; // success, danger, warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    // Recoger datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
    $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
    $cedula_numero = $conn->real_escape_string($_POST['cedula_numero'] ?? '');
    $correo = $conn->real_escape_string($_POST['correo'] ?? '');
    $telefono = $conn->real_escape_string($_POST['telefono'] ?? '');
    $telefono_emergencia = $conn->real_escape_string($_POST['telefono_emergencia'] ?? '');
    $direccion = $conn->real_escape_string($_POST['direccion'] ?? '');
    $distrito = $conn->real_escape_string($_POST['distrito'] ?? '');
    $nacionalidad = $conn->real_escape_string($_POST['nacionalidad'] ?? '');
    $sexo = $conn->real_escape_string($_POST['sexo'] ?? '');
    $fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento'] ?? '');
    $anio_egreso = $conn->real_escape_string($_POST['anio_egreso'] ?? '');
    $colegio_egresado = $conn->real_escape_string($_POST['colegio_egresado'] ?? '');
    $carrera = $conn->real_escape_string($_POST['carrera'] ?? '');
    $condicion_medica = $conn->real_escape_string($_POST['condicion_medica'] ?? '');
    $condicion_especifica = $conn->real_escape_string($_POST['condicion_especifica'] ?? '');
    $como_se_entero = $conn->real_escape_string($_POST['referencia'] ?? '');
    $rol_id = $conn->real_escape_string($_POST['rol_id'] ?? '2'); // Por defecto usuario (2)
    $contrasena = password_hash($_POST['contrasena'] ?? '', PASSWORD_DEFAULT);
    
    // Verificar si el correo o cédula ya existen
    $sql_verificar = "SELECT id FROM usuarios WHERE correo = '$correo' OR cedula_numero = '$cedula_numero'";
    $result = $conn->query($sql_verificar);
    
    if ($result->num_rows > 0) {
        $mensaje = "Error: Ya existe un usuario con ese correo electrónico o número de cédula.";
        $tipo_mensaje = "danger";
    } else {
        // Procesar archivos (guardar como datos binarios)
        function processFile($file) {
            if (!isset($file['tmp_name']) || !$file['tmp_name'] || !file_exists($file['tmp_name'])) {
                return null;
            }
            
            // Leer el contenido del archivo
            $fileContent = file_get_contents($file['tmp_name']);
            return $fileContent;
        }
        
        $foto_anverso_cedula = processFile($_FILES['foto_anverso_cedula'] ?? null);
        $foto_reverso_cedula = processFile($_FILES['foto_reverso_cedula'] ?? null);
        $foto_anverso_certificado = processFile($_FILES['foto_anverso_certificado'] ?? null);
        $foto_reverso_certificado = processFile($_FILES['foto_reverso_certificado'] ?? null);
        $antecedente_policial = processFile($_FILES['antecedente_policial'] ?? null);
        $cert_medic = processFile($_FILES['cert_medic'] ?? null);
        $cert_nacim = processFile($_FILES['cert_nacim'] ?? null);
        $foto_carnet = processFile($_FILES['foto_carnet'] ?? null);
        
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, apellido, cedula_numero, correo, telefono, telefono_emergencia, 
                direccion, distrito, nacionalidad, sexo, fecha_nacimiento, anio_egreso, colegio_egresado, 
                carrera, condicion_medica, condicion_especifica, como_se_entero, contrasena, rol_id,
                foto_anverso_cedula, foto_reverso_cedula, foto_anverso_certificado, foto_reverso_certificado,
                antecedente_policial, cert_medic, cert_nacim, foto_carnet) 
                VALUES ('$nombre', '$apellido', '$cedula_numero', '$correo', '$telefono', '$telefono_emergencia', 
                '$direccion', '$distrito', '$nacionalidad', '$sexo', '$fecha_nacimiento', '$anio_egreso', 
                '$colegio_egresado', '$carrera', '$condicion_medica', '$condicion_especifica', 
                '$como_se_entero', '$contrasena', '$rol_id',
                '" . ($foto_anverso_cedula ? $conn->real_escape_string($foto_anverso_cedula) : NULL) . "',
                '" . ($foto_reverso_cedula ? $conn->real_escape_string($foto_reverso_cedula) : NULL) . "',
                '" . ($foto_anverso_certificado ? $conn->real_escape_string($foto_anverso_certificado) : NULL) . "',
                '" . ($foto_reverso_certificado ? $conn->real_escape_string($foto_reverso_certificado) : NULL) . "',
                '" . ($antecedente_policial ? $conn->real_escape_string($antecedente_policial) : NULL) . "',
                '" . ($cert_medic ? $conn->real_escape_string($cert_medic) : NULL) . "',
                '" . ($cert_nacim ? $conn->real_escape_string($cert_nacim) : NULL) . "',
                '" . ($foto_carnet ? $conn->real_escape_string($foto_carnet) : NULL) . "')";
        
        if ($conn->query($sql)) {
            $mensaje = "Usuario registrado exitosamente.";
            $tipo_mensaje = "success";
            
            // Limpiar campos después de registro exitoso
            $_POST = array();
        } else {
            $mensaje = "Error al registrar el usuario: " . $conn->error;
            $tipo_mensaje = "danger";
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .container {
      background: rgba(255, 255, 255, 0.97);
      padding: 30px 40px;
      border-radius: 20px;
      max-width: 900px;
      width: 100%;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      margin: 20px auto;
    }
    
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .header h1 {
      color: #2c3e50;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    /* Alertas */
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    /* Accordion styling */
    .form-section {
      border: 1px solid #dde1e7;
      border-radius: 12px;
      margin-bottom: 25px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgb(0 0 0 / 0.05);
      transition: box-shadow 0.3s ease;
    }
    
    .form-section:hover {
      box-shadow: 0 4px 18px rgb(0 0 0 / 0.15);
    }
    
    .card-header {
      background-color: #f7f9fc;
      padding: 15px 25px;
      font-weight: 600;
      font-size: 1.1rem;
      color: #34495e;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      user-select: none;
    }
    
    .card-header i {
      color: #2980b9;
      font-size: 1.2rem;
      width: 24px;
    }
    
    .card-body {
      padding: 25px 30px;
      background-color: white;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    label {
      font-weight: 600;
      color: #34495e;
      margin-bottom: 8px;
      display: block;
    }
    
    input.form-control,
    select.form-control,
    input[type="file"] {
      border-radius: 8px;
      border: 1.5px solid #ccc;
      padding: 10px 14px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
      width: 100%;
    }
    
    input.form-control:focus,
    select.form-control:focus {
      border-color: #2980b9;
      box-shadow: 0 0 6px rgba(41, 128, 185, 0.3);
      outline: none;
    }
    
    .row {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -10px;
    }
    
    .col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
      padding: 0 10px;
      box-sizing: border-box;
    }
    
    small.text-muted {
      font-size: 0.8rem;
      color: #7f8c8d;
    }
    
    button.btn-primary {
      width: 100%;
      padding: 14px;
      font-size: 1.2rem;
      font-weight: 700;
      background: #2980b9;
      border: none;
      border-radius: 12px;
      transition: background-color 0.3s ease;
      color: white;
      cursor: pointer;
    }
    
    button.btn-primary:hover {
      background: #1c5980;
    }
    
    .password-toggle {
      margin-top: 8px;
      display: flex;
      align-items: center;
      font-weight: 600;
      color: #34495e;
      cursor: pointer;
      user-select: none;
    }
    
    .password-toggle input {
      margin-right: 8px;
      cursor: pointer;
    }
    
    .btn-group {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    
    .btn-secondary {
      padding: 10px 20px;
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .file-preview {
      margin-top: 8px;
      font-size: 0.9rem;
      color: #6c757d;
    }
    
    .declaraciones {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin: 20px 0;
    }
    
    .declaraciones .form-group {
      margin-bottom: 15px;
    }
    
    .declaraciones label {
      font-weight: normal;
      display: flex;
      align-items: center;
    }
    
    .declaraciones input[type="checkbox"] {
      margin-right: 10px;
    }
    
    @media (max-width: 768px) {
      .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
      }
      
      .container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Agregar Nuevo Usuario</h1>
    </div>
    
    <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
      <?php echo $mensaje; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="accordion" id="accordionExample">

        <!-- Datos Personales -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('personalData')">
            <i class="fas fa-user"></i>
            Datos Personales
          </div>
          <div id="personalData" class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nombre">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" 
                         value="<?php echo $_POST['nombre'] ?? ''; ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="apellido">Apellido:</label>
                  <input type="text" class="form-control" id="apellido" name="apellido" 
                         value="<?php echo $_POST['apellido'] ?? ''; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="cedula_numero">Número de Cédula:</label>
                  <input type="text" class="form-control" id="cedula_numero" name="cedula_numero" 
                         value="<?php echo $_POST['cedula_numero'] ?? ''; ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="correo">Correo Electrónico:</label>
                  <input type="email" class="form-control" id="correo" name="correo" 
                         value="<?php echo $_POST['correo'] ?? ''; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="telefono">Teléfono:</label>
                  <input type="tel" class="form-control" id="telefono" name="telefono" 
                         value="<?php echo $_POST['telefono'] ?? ''; ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="telefono_emergencia">Teléfono de Emergencia:</label>
                  <input type="tel" class="form-control" id="telefono_emergencia" name="telefono_emergencia" 
                         value="<?php echo $_POST['telefono_emergencia'] ?? ''; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="direccion">Dirección:</label>
              <input type="text" class="form-control" id="direccion" name="direccion" 
                     value="<?php echo $_POST['direccion'] ?? ''; ?>" required>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="distrito">Distrito de Procedencia:</label>
                  <select class="form-control" id="distrito" name="distrito" required>
                    <option value="">Seleccione su distrito</option>
                    <option value="Alberdi" <?php echo (($_POST['distrito'] ?? '') == 'Alberdi') ? 'selected' : ''; ?>>Alberdi</option>
                    <option value="Cerrito" <?php echo (($_POST['distrito'] ?? '') == 'Cerrito') ? 'selected' : ''; ?>>Cerrito</option>
                    <option value="Desmochados" <?php echo (($_POST['distrito'] ?? '') == 'Desmochados') ? 'selected' : ''; ?>>Desmochados</option>
                    <option value="General José de Eduvigis Díaz" <?php echo (($_POST['distrito'] ?? '') == 'General José de Eduvigis Díaz') ? 'selected' : ''; ?>>General José de Eduvigis Díaz</option>
                    <option value="Guazú Cuá" <?php echo (($_POST['distrito'] ?? '') == 'Guazú Cuá') ? 'selected' : ''; ?>>Guazú Cuá</option>
                    <option value="Humaitá" <?php echo (($_POST['distrito'] ?? '') == 'Humaitá') ? 'selected' : ''; ?>>Humaitá</option>
                    <option value="Isla Umbú" <?php echo (($_POST['distrito'] ?? '') == 'Isla Umbú') ? 'selected' : ''; ?>>Isla Umbú</option>
                    <option value="Laureles" <?php echo (($_POST['distrito'] ?? '') == 'Laureles') ? 'selected' : ''; ?>>Laureles</option>
                    <option value="Mayor José de Jesús Martínez" <?php echo (($_POST['distrito'] ?? '') == 'Mayor José de Jesús Martínez') ? 'selected' : ''; ?>>Mayor José de Jesús Martínez</option>
                    <option value="Paso de Patria" <?php echo (($_POST['distrito'] ?? '') == 'Paso de Patria') ? 'selected' : ''; ?>>Paso de Patria</option>
                    <option value="Pilar" <?php echo (($_POST['distrito'] ?? '') == 'Pilar') ? 'selected' : ''; ?>>Pilar</option>
                    <option value="San Juan Bautista del Ñeembucú" <?php echo (($_POST['distrito'] ?? '') == 'San Juan Bautista del Ñeembucú') ? 'selected' : ''; ?>>San Juan Bautista del Ñeembucú</option>
                    <option value="Tacuaras" <?php echo (($_POST['distrito'] ?? '') == 'Tacuaras') ? 'selected' : ''; ?>>Tacuaras</option>
                    <option value="Villa Franca" <?php echo (($_POST['distrito'] ?? '') == 'Villa Franca') ? 'selected' : ''; ?>>Villa Franca</option>
                    <option value="Villa Oliva" <?php echo (($_POST['distrito'] ?? '') == 'Villa Oliva') ? 'selected' : ''; ?>>Villa Oliva</option>
                    <option value="Villalbín" <?php echo (($_POST['distrito'] ?? '') == 'Villalbín') ? 'selected' : ''; ?>>Villalbín</option>
                    <option value="Otros" <?php echo (($_POST['distrito'] ?? '') == 'Otros') ? 'selected' : ''; ?>>Otros</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nacionalidad">Nacionalidad:</label>
                  <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" 
                         value="<?php echo $_POST['nacionalidad'] ?? ''; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="sexo">Sexo:</label>
                  <select class="form-control" id="sexo" name="sexo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Masculino" <?php echo (($_POST['sexo'] ?? '') == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Femenino" <?php echo (($_POST['sexo'] ?? '') == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                    <option value="Prefiero Omitir" <?php echo (($_POST['sexo'] ?? '') == 'Prefiero Omitir') ? 'selected' : ''; ?>>Prefiero Omitir</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                  <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                         value="<?php echo $_POST['fecha_nacimiento'] ?? ''; ?>" required>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Datos Académicos -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('academicData')">
            <i class="fas fa-graduation-cap"></i>
            Datos Académicos
          </div>
          <div id="academicData" class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="anio_egreso">Año de Egreso:</label>
                  <select class="form-control" id="anio_egreso" name="anio_egreso" required>
                    <option value="">Seleccione el año</option>
                    <option value="anteriores">Años anteriores a 2010</option>
                    <?php
                      for ($i = 2010; $i <= 2030; $i++) {
                        $selected = (($_POST['anio_egreso'] ?? '') == $i) ? 'selected' : '';
                        echo "<option value=\"$i\" $selected>$i</option>";
                      }
                    ?>
                    <option value="posteriores" <?php echo (($_POST['anio_egreso'] ?? '') == 'posteriores') ? 'selected' : ''; ?>>Años posteriores a 2030</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="colegio_egresado">Institución de Procedencia:</label>
                  <select class="form-control" id="colegio_egresado" name="colegio_egresado" required>
                    <option value="">Seleccione su colegio</option>
                    <option value="Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)') ? 'selected' : ''; ?>>Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)</option>
                    <option value="COLEGIO TECNICO JUAN XXIII (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO TECNICO JUAN XXIII (Pilar)') ? 'selected' : ''; ?>>COLEGIO TECNICO JUAN XXIII (Pilar)</option>
                    <option value="COL. NAC. SAN LORENZO (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAN LORENZO (Pilar)') ? 'selected' : ''; ?>>COL. NAC. SAN LORENZO (Pilar)</option>
                    <option value="CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)') ? 'selected' : ''; ?>>CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)</option>
                    <option value="COL. NAC. 6º COMPAÑIA MEDINA (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. 6º COMPAÑIA MEDINA (Pilar)') ? 'selected' : ''; ?>>COL. NAC. 6º COMPAÑIA MEDINA (Pilar)</option>
                    <option value="COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)') ? 'selected' : ''; ?>>COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)</option>
                    <option value="COL. NAC. PILAR (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. PILAR (Pilar)') ? 'selected' : ''; ?>>COL. NAC. PILAR (Pilar)</option>
                    <option value="COLEGIO SAN FRANCISCO DE ASIS (Pilar)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO SAN FRANCISCO DE ASIS (Pilar)') ? 'selected' : ''; ?>>COLEGIO SAN FRANCISCO DE ASIS (Pilar)</option>
                    <option value="COL. NAC. DE LOMAS (Alberdi)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. DE LOMAS (Alberdi)') ? 'selected' : ''; ?>>COL. NAC. DE LOMAS (Alberdi)</option>
                    <option value="COL. NAC. JUAN BAUTISTA ALBERDI (Alberdi)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. JUAN BAUTISTA ALберди (Alberdi)') ? 'selected' : ''; ?>>COL. NAC. JUAN BAUTISTA ALBERDI (Alberdi)</option>
                    <option value="COLEGIO NACIONAL CERRITO (Cerrito)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL CERRITO (Cerrito)') ? 'selected' : ''; ?>>COLEGIO NACIONAL CERRITO (Cerrito)</option>
                    <option value="COL. NAC. TACURUTY (Cerrito)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. TACURUTY (Cerrito)') ? 'selected' : ''; ?>>COL. NAC. TACURUTY (Cerrito)</option>
                    <option value="COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)') ? 'selected' : ''; ?>>COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)</option>
                    <option value="COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)') ? 'selected' : ''; ?>>COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)</option>
                    <option value="COL. NAC. LOMA GUAZU (General Diaz)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. LOMA GUAZU (General Diaz)') ? 'selected' : ''; ?>>COL. NAC. LOMA GUAZU (General Diaz)</option>
                    <option value="COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)') ? 'selected' : ''; ?>>COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)</option>
                    <option value="COL. NAC. SAN CARLOS (Humaita)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAN CARLOS (Humaita)') ? 'selected' : ''; ?>>COL. NAC. SAN CARLOS (Humaita)</option>
                    <option value="COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)') ? 'selected' : ''; ?>>COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)</option>
                    <option value="COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)') ? 'selected' : ''; ?>>COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)</option>
                    <option value="COLEGIO NACIONAL DE ISLERIA (Isla Umbu)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL DE ISLERIA (Isla Umbu)') ? 'selected' : ''; ?>>COLEGIO NACIONAL DE ISLERIA (Isla Umbu)</option>
                    <option value="COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)') ? 'selected' : ''; ?>>COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)</option>
                    <option value="COLEGIO NACIONAL APIPE (Los Laureles)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL APIPE (Los Laureles)') ? 'selected' : ''; ?>>COLEGIO NACIONAL APIPE (Los Laureles)</option>
                    <option value="COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)') ? 'selected' : ''; ?>>COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)</option>
                    <option value="COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)') ? 'selected' : ''; ?>>COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)</option>
                    <option value="COLEGIO NACIONAL YATAITY (Mayor Martinez)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL YATAITY (Mayor Martinez)') ? 'selected' : ''; ?>>COLEGIO NACIONAL YATAITY (Mayor Martinez)</option>
                    <option value="COL. NAC. SAN PATRICIO (Paso de Patria)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAN PATRICIO (Paso de Patria)') ? 'selected' : ''; ?>>COL. NAC. SAN PATRICIO (Paso de Patria)</option>
                    <option value="COL. NAC. SAN JUAN BAUTista de Ñeembucú" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAN JUAN BAUTISTA DE ÑEEMBUCU (San Juan B. de Ñeembucú)') ? 'selected' : ''; ?>>COL. NAC. SAN JUAN BAUTISTA DE ÑEEMBUCU (San Juan B. de Ñeembucú)</option>
                    <option value="COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)') ? 'selected' : ''; ?>>COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)</option>
                    <option value="COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)') ? 'selected' : ''; ?>>COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)</option>
                    <option value="COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)') ? 'selected' : ''; ?>>COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)</option>
                    <option value="COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)') ? 'selected' : ''; ?>>COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)</option>
                    <option value="COL. NAC. COLONIA MBURICA (Tacuaras)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. COLONIA MBURICA (Tacuaras)') ? 'selected' : ''; ?>>COL. NAC. COLONIA MBURICA (Tacuaras)</option>
                    <option value="COL. NAC. VILLA FRANCA (Villa Franca)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. VILLA FRANCA (Villa Franca)') ? 'selected' : ''; ?>>COL. NAC. VILLA FRANCA (Villa Franca)</option>
                    <option value="COL. NAC. LILIAN SOLALINDE (Villa Oliva)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. LILIAN SOLALINDE (Villa Oliva)') ? 'selected' : ''; ?>>COL. NAC. LILIAN SOLALINDE (Villa Oliva)</option>
                    <option value="COLEGIO NACIONAL DE ZANJITA (Villa Oliva)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL DE ZANJITA (Villa Oliva)') ? 'selected' : ''; ?>>COLEGIO NACIONAL DE ZANJITA (Villa Oliva)</option>
                    <option value="COL. NAC. RIO PARAGUAY (Villa Oliva)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. RIO PARAGUAY (Villa Oliva)') ? 'selected' : ''; ?>>COL. NAC. RIO PARAGUAY (Villa Oliva)</option>
                    <option value="COL. NAC. SAN FRANCISCO (Villalbin)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COL. NAC. SAN FRANCISCO (Villalbin)') ? 'selected' : ''; ?>>COL. NAC. SAN FRANCISCO (Villalbin)</option>
                    <option value="COLEGIO NACIONAL SAN RAMON (Villalbin)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL SAN RAMON (Villalbin)') ? 'selected' : ''; ?>>COLEGIO NACIONAL SAN RAMON (Villalbin)</option>
                    <option value="COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)" <?php echo (($_POST['colegio_egresado'] ?? '') == 'COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)') ? 'selected' : ''; ?>>COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)</option>
                    <option value="Otro Departamento" <?php echo (($_POST['colegio_egresado'] ?? '') == 'Otro Departamento') ? 'selected' : ''; ?>>Otro Departamento</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="carrera">Carrera a la que desea postularse:</label>
              <select class="form-control" id="carrera" name="carrera" required>
                <option value="">Seleccione una carrera</option>
                <option value="Licenciatura en Análisis de Sistemas" <?php echo (($_POST['carrera'] ?? '') == 'Licenciatura en Análisis de Sistemas') ? 'selected' : ''; ?>>Licenciatura en Análisis de Sistemas</option>
                <option value="Ingeniería Industrial" <?php echo (($_POST['carrera'] ?? '') == 'Ingeniería Industrial') ? 'selected' : ''; ?>>Ingeniería Industrial</option>
                <option value="Ingeniería Ambiental" <?php echo (($_POST['carrera'] ?? '') == 'Ingeniería Ambiental') ? 'selected' : ''; ?>>Ingeniería Ambiental</option>
                <option value="Lic. en Educación Física y Entrenamiento Deportivo" <?php echo (($_POST['carrera'] ?? '') == 'Lic. en Educación Física y Entrenamiento Deportivo') ? 'selected' : ''; ?>>Lic. en Educación Física y Entrenamiento Deportivo</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Condición Médica -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('medicalData')">
            <i class="fas fa-heartbeat"></i>
            Condición Médica
          </div>
          <div id="medicalData" class="card-body">
            <div class="form-group">
              <label for="condicion_medica">Seleccione su condición médica (si corresponde):</label>
              <select class="form-control" id="condicion_medica" name="condicion_medica">
                <option value="">Ninguna</option>
                <option value="Diabetes" <?php echo (($_POST['condicion_medica'] ?? '') == 'Diabetes') ? 'selected' : ''; ?>>Diabetes</option>
                <option value="Hipertensión" <?php echo (($_POST['condicion_medica'] ?? '') == 'Hipertensión') ? 'selected' : ''; ?>>Hipertensión</option>
                <option value="Otra" <?php echo (($_POST['condicion_medica'] ?? '') == 'Otra') ? 'selected' : ''; ?>>Otra (especificar abajo)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="condicion_especifica">Si seleccionó "Otra", indique su condición:</label>
              <input type="text" class="form-control" id="condicion_especifica" name="condicion_especifica" 
                     value="<?php echo $_POST['condicion_especifica'] ?? ''; ?>">
            </div>
          </div>
        </div>

        <!-- Otros Datos -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('otherData')">
            <i class="fas fa-info-circle"></i>
            Otros Datos
          </div>
          <div id="otherData" class="card-body">
            <div class="form-group">
              <label for="referencia">¿Cómo se enteró de la carrera?</label>
              <select class="form-control" id="referencia" name="referencia" required>
                <option value="">Seleccione una opción</option>
                <option value="Redes Sociales" <?php echo (($_POST['referencia'] ?? '') == 'Redes Sociales') ? 'selected' : ''; ?>>Redes Sociales</option>
                <option value="Amigos / Familiares" <?php echo (($_POST['referencia'] ?? '') == 'Amigos / Familiares') ? 'selected' : ''; ?>>Amigos / Familiares</option>
                <option value="Página Web" <?php echo (($_POST['referencia'] ?? '') == 'Página Web') ? 'selected' : ''; ?>>Página Web</option>
                <option value="Feria Educativa" <?php echo (($_POST['referencia'] ?? '') == 'Feria Educativa') ? 'selected' : ''; ?>>Feria Educativa</option>
                <option value="Otros" <?php echo (($_POST['referencia'] ?? '') == 'Otros') ? 'selected' : ''; ?>>Otros</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Documentos -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('documentsData')">
            <i class="fas fa-file"></i>
            Documentos Requeridos
          </div>
          <div id="documentsData" class="card-body">
            <div class="form-group">
              <label for="foto_carnet">Foto tipo carnet actualizada:</label>
              <input type="file" class="form-control" id="foto_carnet" name="foto_carnet" accept="image/*" required>
              <div class="file-preview" id="foto_carnet_preview"></div>
            </div>

            <div class="form-group">
              <label for="foto_anverso_cedula">Imagen Frontal de la Cédula de Identidad:</label>
              <input type="file" class="form-control" id="foto_anverso_cedula" name="foto_anverso_cedula" accept="image/*" required>
              <div class="file-preview" id="foto_anverso_cedula_preview"></div>
            </div>

            <div class="form-group">
              <label for="foto_reverso_cedula">Imagen dorsal de la Cédula de Identidad:</label>
              <input type="file" class="form-control" id="foto_reverso_cedula" name="foto_reverso_cedula" accept="image/*" required>
              <div class="file-preview" id="foto_reverso_cedula_preview"></div>
            </div>

            <div class="form-group">
              <label for="foto_anverso_certificado">Imagen Frontal del Certificado de Estudio:</label>
              <input type="file" class="form-control" id="foto_anverso_certificado" name="foto_anverso_certificado" accept="image/*" required>
              <div class="file-preview" id="foto_anverso_certificado_preview"></div>
            </div>

            <div class="form-group">
              <label for="foto_reverso_certificado">Imagen dorsal del Certificado de Estudio:</label>
              <input type="file" class="form-control" id="foto_reverso_certificado" name="foto_reverso_certificado" accept="image/*" required>
              <div class="file-preview" id="foto_reverso_certificado_preview"></div>
            </div>

            <div class="form-group">
              <label for="antecedente_policial">Imagen del Certificado del Antecedente Policial:</label>
              <input type="file" class="form-control" id="antecedente_policial" name="antecedente_policial" accept="image/*" required>
              <div class="file-preview" id="antecedente_policial_preview"></div>
            </div>

            <div class="form-group">
              <label for="cert_medic">Imagen del Certificado Médico:</label>
              <input type="file" class="form-control" id="cert_medic" name="cert_medic" accept="image/*" required>
              <div class="file-preview" id="cert_medic_preview"></div>
            </div>

            <div class="form-group">
              <label for="cert_nacim">Imagen del Certificado de Nacimiento:</label>
              <input type="file" class="form-control" id="cert_nacim" name="cert_nacim" accept="image/*" required>
              <div class="file-preview" id="cert_nacim_preview"></div>
            </div>
          </div>
        </div>

        <!-- Contraseña y Rol -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('securityData')">
            <i class="fas fa-lock"></i>
            Seguridad
          </div>
          <div id="securityData" class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="contrasena">Contraseña:</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('contrasena', 'toggleIcon')">
                      <i id="toggleIcon" class="fas fa-eye"></i>
                    </button>
                  </div>
                  <small class="text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                  <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                  <div class="invalid-feedback" id="passwordError" style="display: none; color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;">
                    Las contraseñas no coinciden.
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="rol_id">Tipo de Usuario:</label>
              <select class="form-control" id="rol_id" name="rol_id" required>
                <option value="1" <?php echo (($_POST['rol_id'] ?? '') == '1') ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo (($_POST['rol_id'] ?? '') == '2' || !isset($_POST['rol_id'])) ? 'selected' : ''; ?>>Usuario Normal</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Declaraciones -->
        <div class="form-section">
          <div class="card-header" onclick="toggleSection('declarationsData')">
            <i class="fas fa-file-signature"></i>
            Declaraciones
          </div>
          <div id="declarationsData" class="card-body">
            <div class="declaraciones">
              <div class="form-group">
                <label>
                  <input type="checkbox" name="terminos" required>
                  Acepto los <a href="#" target="_blank">términos y condiciones de uso del sistema</a>.
                </label>
              </div>

              <div class="form-group">
                <label>
                  <input type="checkbox" name="consentimiento_datos" required>
                  Autorizo el tratamiento de mis datos personales conforme a la ley de protección de datos.
                </label>
              </div>

              <div class="form-group">
                <label>
                  <input type="checkbox" name="declaracion_veracidad" required>
                  Declaro bajo juramento que los datos ingresados son verídicos.
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="btn-group">
          <button type="submit" class="btn-primary">Registrar Usuario</button>
          <button type="reset" class="btn-secondary">Limpiar Campos</button>
       </div>
    </form>
<script>
  <script>
</script>
<script>
document.querySelector('button[type="reset"]').addEventListener('click', function() {
    // Limpiar las vistas previas de archivos
    const previews = document.querySelectorAll('.file-preview');
    previews.forEach(preview => preview.innerHTML = '');
});
</script>
    <script>
      // Función para mostrar/ocultar secciones
      function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section.style.display === 'none') {
          section.style.display = 'block';
        } else {
          section.style.display = 'none';
        }
      }
      
      // Inicialmente mostrar solo la primera sección
      document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('personalData').style.display = 'block';
        document.getElementById('academicData').style.display = 'none';
        document.getElementById('medicalData').style.display = 'none';
        document.getElementById('otherData').style.display = 'none';
        document.getElementById('documentsData').style.display = 'none';
        document.getElementById('securityData').style.display = 'none';
        document.getElementById('declarationsData').style.display = 'none';
        
        // Agregar event listeners para previsualización de archivos
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
          input.addEventListener('change', function() {
            const previewId = this.id + '_preview';
            const previewElement = document.getElementById(previewId);
            
            if (this.files && this.files[0]) {
              previewElement.textContent = `Archivo seleccionado: ${this.files[0].name}`;
            } else {
              previewElement.textContent = '';
            }
          });
        });
      });
      
      // Mostrar/ocultar contraseña
      function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
      
      // Validar que las contraseñas coincidan
      document.getElementById('confirmar_contrasena').addEventListener('input', function() {
        const password = document.getElementById('contrasena').value;
        const confirmPassword = this.value;
        const errorElement = document.getElementById('passwordError');
        
        if (password !== confirmPassword && confirmPassword !== '') {
          this.setCustomValidity('Las contraseñas no coinciden');
          errorElement.style.display = 'block';
        } else {
          this.setCustomValidity('');
          errorElement.style.display = 'none';
        }
      });
      
      // Validar formulario antes de enviar
      document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('contrasena').value;
        const confirmPassword = document.getElementById('confirmar_contrasena').value;
        
        if (password !== confirmPassword) {
          e.preventDefault();
          document.getElementById('passwordError').style.display = 'block';
          alert('Las contraseñas no coinciden. Por favor, verifique.');
        }
        
        // Validar longitud de contraseña
        if (password.length < 8) {
          e.preventDefault();
          alert('La contraseña debe tener al menos 8 caracteres.');
        }
        
        // Validar checkboxes de declaraciones
        const terminos = document.querySelector('input[name="terminos"]').checked;
        const consentimiento = document.querySelector('input[name="consentimiento_datos"]').checked;
        const veracidad = document.querySelector('input[name="declaracion_veracidad"]').checked;
        
        if (!terminos || !consentimiento || !veracidad) {
          e.preventDefault();
          alert('Debe aceptar todas las declaraciones para continuar.');
        }
      });
    </script>
    <script>
  </div>
</body>
</html>
