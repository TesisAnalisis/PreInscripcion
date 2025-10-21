<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 2) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';

// Obtener datos actuales
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Función para procesar archivos (CORREGIDA)
function processFile($file, $campoActual) {
    // Si no se subió archivo, mantener el actual
    if ($file['error'] === UPLOAD_ERR_NO_FILE || empty($file['tmp_name'])) {
        return $campoActual;
    }
    
    // Si hay error en la subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir el archivo: " . $file['name']);
    }
    
    // Validar tamaño (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("El archivo " . $file['name'] . " es demasiado grande (máximo 5MB)");
    }
    
    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception("Tipo de archivo no permitido: " . $file['name'] . ". Solo se permiten imágenes JPG, PNG, GIF y PDF.");
    }
    
    // Leer el contenido del archivo
    $contenido = file_get_contents($file['tmp_name']);
    if ($contenido === false) {
        throw new Exception("Error al leer el archivo: " . $file['name']);
    }
    
    return $contenido;
}

// Procesar actualización (CÓDIGO CORREGIDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recoger todos los datos del formulario
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $cedula_numero = trim($_POST['cedula_numero']);
        $telefono = trim($_POST['telefono']);
        $telefono_emergencia = trim($_POST['telefono_emergencia']);
        $direccion = trim($_POST['direccion']);
        $distrito = trim($_POST['distrito']);
        $nacionalidad = trim($_POST['nacionalidad']);
        $sexo = trim($_POST['sexo']);
        $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
        $anio_egreso = trim($_POST['anio_egreso']);
        $colegio_egresado = trim($_POST['colegio_egresado']);
        $carrera = trim($_POST['carrera']);
        $condicion_medica = trim($_POST['condicion_medica']);
        $condicion_especifica = trim($_POST['condicion_especifica']);
        $como_se_entero = trim($_POST['como_se_entero']);
         // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($cedula_numero) || empty($telefono)) {
            throw new Exception("Los campos obligatorios no pueden estar vacíos.");
        }

        // Procesar contraseña por separado
        $update_password = !empty($_POST['nueva_contrasena']);
        if ($update_password) {
            $nueva_contrasena_hash = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);
        }

        // Construir consulta dinámicamente
        $campos = [];
        $valores = [];
        $tipos = "";
        
        // Campos normales
        $campos_normales = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'cedula_numero' => $cedula_numero,
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
        ];
        
        foreach ($campos_normales as $campo => $valor) {
            $campos[] = "$campo=?";
            $valores[] = $valor;
            $tipos .= "s";
        }
        
        // Campos de archivos (solo si se subieron nuevos)
        $archivos = [
            'foto_anverso_cedula',
            'foto_reverso_cedula', 
            'foto_anverso_certificado',
            'foto_reverso_certificado',
            'antecedente_policial',
            'cert_medic',
            'cert_nacim',
            'foto_carnet'
        ];
        
        foreach ($archivos as $archivo) {
            if ($_FILES[$archivo]['error'] === UPLOAD_ERR_OK) {
                $contenido = processFile($_FILES[$archivo], null);
                $campos[] = "$archivo=?";
                $valores[] = $contenido;
                $tipos .= "b";
            }
        }
        
        // Contraseña si se proporciona
        if ($update_password) {
            $campos[] = "contrasena=?";
            $valores[] = $nueva_contrasena_hash;
            $tipos .= "s";
        }
        
        // Agregar WHERE condition
        $valores[] = $usuario_id;
        $tipos .= "i";
        
        // Construir consulta final
        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id=?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }
        
        // Bind parameters
        $stmt->bind_param($tipos, ...$valores);
        
        // Send long data para BLOBs
        $blob_index = 16; // Después de los 16 campos normales
        foreach ($archivos as $archivo) {
            if ($_FILES[$archivo]['error'] === UPLOAD_ERR_OK) {
                $stmt->send_long_data($blob_index, $valores[$blob_index]);
                $blob_index++;
            }
        }
        
        if ($stmt->execute()) {
            header('Location: perfil.php?mensaje=actualizado');
            exit();
        } else {
            throw new Exception("Error al actualizar los datos: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $mensaje = '<div class="alert error">' . $e->getMessage() . '</div>';
        error_log("Error en actualización: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Mis Datos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0;
            padding: 20px;
        }
        .header { 
            background: #2c3e50; 
            color: white; 
            padding: 1rem;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-content {
            padding: 2rem;
        }
        .section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        .section h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group { 
            margin-bottom: 1.5rem; 
        }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: bold;
            color: #34495e;
        }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        .btn { 
            background: #27ae60; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
            margin-right: 10px;
        }
        .btn:hover { 
            background: #219a52; 
        }
        .btn-cancel {
            background: #95a5a6;
            text-decoration: none;
            display: inline-block;
            padding: 12px 30px;
            border-radius: 5px;
        }
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        .alert { 
            padding: 1rem; 
            margin-bottom: 1rem; 
            border-radius: 5px; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        .form-actions {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .password-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
            font-style: italic;
        }
        .required::after {
            content: " *";
            color: #e74c3c;
        }
        .file-preview {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #e8f4fd;
            border-radius: 5px;
            border: 1px solid #b8daff;
        }
        .file-preview img {
            max-width: 200px;
            max-height: 150px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        .file-info {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .file-actions {
            margin-top: 0.5rem;
        }
        .file-btn {
            padding: 5px 10px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.8rem;
        }
        .file-btn:hover {
            background: #545b62;
        }
        .current-file {
            font-size: 0.9rem;
            color: #28a745;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Editar Mis Datos</h1>
            <p>Actualiza tu información personal, académica y documentos</p>
        </div>
        
        <div class="form-content">
            <?php echo $mensaje; ?>
            
            <form method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                
                <!-- Datos Personales -->
                <div class="section">
                    <h3>👤 Datos Personales</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Nombre:</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="required">Apellido:</label>
                            <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Cédula:</label>
                            <input type="text" name="cedula_numero" value="<?= htmlspecialchars($usuario['cedula_numero']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="required">Teléfono:</label>
                            <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Teléfono Emergencia:</label>
                            <input type="tel" name="telefono_emergencia" value="<?= htmlspecialchars($usuario['telefono_emergencia']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="required">Dirección:</label>
                            <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Distrito:</label>
                            <select name="distrito" required>
                                <option value="">Seleccione su distrito</option>
                                <?php
                                $distritos = [
                                    "Alberdi", "Cerrito", "Desmochados", "General José de Eduvigis Díaz",
                                    "Guazú Cuá", "Humaitá", "Isla Umbú", "Laureles", 
                                    "Mayor José de Jesús Martínez", "Paso de Patria", "Pilar",
                                    "San Juan Bautista del Ñeembucú", "Tacuaras", "Villa Franca",
                                    "Villa Oliva", "Villalbín", "Otros"
                                ];
                                foreach ($distritos as $distrito) {
                                    $selected = $usuario['distrito'] == $distrito ? 'selected' : '';
                                    echo "<option value=\"$distrito\" $selected>$distrito</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required">Nacionalidad:</label>
                            <input type="text" name="nacionalidad" value="<?= htmlspecialchars($usuario['nacionalidad']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Sexo:</label>
                            <select name="sexo" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Masculino" <?= $usuario['sexo'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Femenino" <?= $usuario['sexo'] == 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                <option value="Prefiero Omitir" <?= $usuario['sexo'] == 'Prefiero Omitir' ? 'selected' : ''; ?>>Prefiero Omitir</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required">Fecha Nacimiento:</label>
                            <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($usuario['fecha_nacimiento']); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Datos Académicos -->
                <div class="section">
                    <h3>🎓 Datos Académicos</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Año Egreso:</label>
                            <select name="anio_egreso" required>
                                <option value="">Seleccione el año</option>
                                <option value="anteriores" <?= $usuario['anio_egreso'] == 'anteriores' ? 'selected' : ''; ?>>Años anteriores a 2010</option>
                                <?php
                                    for ($i = 2010; $i <= 2030; $i++) {
                                        $selected = $usuario['anio_egreso'] == $i ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>$i</option>";
                                    }
                                ?>
                                <option value="posteriores" <?= $usuario['anio_egreso'] == 'posteriores' ? 'selected' : ''; ?>>Años posteriores a 2030</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required">Colegio:</label>
                            <select name="colegio_egresado" required>
                                <option value="">Seleccione su colegio</option>
                                <?php
                                $colegios = [
                                    "Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)",
                                    "COLEGIO TECNICO JUAN XXIII (Pilar)",
                                    "COL. NAC. SAN LORENZO (Pilar)",
                                    "CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)",
                                    "COL. NAC. 6º COMPAÑIA MEDINA (Pilar)",
                                    "COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)",
                                    "COL. NAC. PILAR (Pilar)",
                                    "COLEGIO SAN FRANCISCO DE ASIS (Pilar)",
                                    "COL. NAC. DE LOMAS (Alberdi)",
                                    "COL. NAC. JUAN BAUTISTA ALBERDI (Alberdi)",
                                    "COLEGIO NACIONAL CERRITO (Cerrito)",
                                    "COL. NAC. TACURUTY (Cerrito)",
                                    "COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)",
                                    "COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)",
                                    "COL. NAC. LOMA GUAZU (General Diaz)",
                                    "COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)",
                                    "COL. NAC. SAN CARLOS (Humaita)",
                                    "COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)",
                                    "COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)",
                                    "COLEGIO NACIONAL DE ISLERIA (Isla Umbu)",
                                    "COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)",
                                    "COLEGIO NACIONAL APIPE (Los Laureles)",
                                    "COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)",
                                    "COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)",
                                    "COLEGIO NACIONAL YATAITY (Mayor Martinez)",
                                    "COL. NAC. SAN PATRICIO (Paso de Patria)",
                                    "COL. NAC. SAN JUAN BAUTISTA DE ÑEEMBUCU (San Juan B. de Ñeembucú)",
                                    "COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)",
                                    "COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)",
                                    "COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)",
                                    "COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)",
                                    "COL. NAC. COLONIA MBURICA (Tacuaras)",
                                    "COL. NAC. VILLA FRANCA (Villa Franca)",
                                    "COL. NAC. LILIAN SOLALINDE (Villa Oliva)",
                                    "COLEGIO NACIONAL DE ZANJITA (Villa Oliva)",
                                    "COL. NAC. RIO PARAGUAY (Villa Oliva)",
                                    "COL. NAC. SAN FRANCISCO (Villalbin)",
                                    "COLEGIO NACIONAL SAN RAMON (Villalbin)",
                                    "COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)",
                                    "Otro Departamento"
                                ];
                                foreach ($colegios as $colegio) {
                                    $selected = $usuario['colegio_egresado'] == $colegio ? 'selected' : '';
                                    echo "<option value=\"$colegio\" $selected>$colegio</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required">Carrera:</label>
                        <select name="carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <option value="Licenciatura en Análisis de Sistemas" <?= $usuario['carrera'] == 'Licenciatura en Análisis de Sistemas' ? 'selected' : ''; ?>>Licenciatura en Análisis de Sistemas</option>
                            <option value="Ingeniería Industrial" <?= $usuario['carrera'] == 'Ingeniería Industrial' ? 'selected' : ''; ?>>Ingeniería Industrial</option>
                            <option value="Ingeniería Ambiental" <?= $usuario['carrera'] == 'Ingeniería Ambiental' ? 'selected' : ''; ?>>Ingeniería Ambiental</option>
                            <option value="Lic. en Educación Física y Entrenamiento Deportivo" <?= $usuario['carrera'] == 'Lic. en Educación Física y Entrenamiento Deportivo' ? 'selected' : ''; ?>>Lic. en Educación Física y Entrenamiento Deportivo</option>
                        </select>
                    </div>
                </div>

                <!-- Condición Médica -->
                <div class="section">
                    <h3>🏥 Condición Médica</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Condición Médica:</label>
                            <select name="condicion_medica">
                                <option value="">Ninguna</option>
                                <option value="Diabetes" <?= $usuario['condicion_medica'] == 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                                <option value="Hipertensión" <?= $usuario['condicion_medica'] == 'Hipertensión' ? 'selected' : ''; ?>>Hipertensión</option>
                                <option value="Otra" <?= $usuario['condicion_medica'] == 'Otra' ? 'selected' : ''; ?>>Otra</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Condición Específica:</label>
                            <input type="text" name="condicion_especifica" value="<?= htmlspecialchars($usuario['condicion_especifica']); ?>" placeholder="Especifique si seleccionó 'Otra'">
                        </div>
                    </div>
                </div>

                <!-- Otros Datos -->
                <div class="section">
                    <h3>📊 Otros Datos</h3>
                    <div class="form-group">
                        <label class="required">¿Cómo se enteró?</label>
                        <select name="como_se_entero" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Redes Sociales" <?= $usuario['como_se_entero'] == 'Redes Sociales' ? 'selected' : ''; ?>>Redes Sociales</option>
                            <option value="Amigos / Familiares" <?= $usuario['como_se_entero'] == 'Amigos / Familiares' ? 'selected' : ''; ?>>Amigos / Familiares</option>
                            <option value="Página Web" <?= $usuario['como_se_entero'] == 'Página Web' ? 'selected' : ''; ?>>Página Web</option>
                            <option value="Feria Educativa" <?= $usuario['como_se_entero'] == 'Feria Educativa' ? 'selected' : ''; ?>>Feria Educativa</option>
                            <option value="Otros" <?= $usuario['como_se_entero'] == 'Otros' ? 'selected' : ''; ?>>Otros</option>
                        </select>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="section">
                    <h3>📎 Documentos</h3>
                    <p class="password-note">Nota: Solo seleccione un archivo si desea reemplazar el actual. De lo contrario, se mantendrá el documento existente.</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Foto Carnet:</label>
                            <input type="file" name="foto_carnet" accept="image/*">
                            <?php if (!empty($usuario['foto_carnet'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Cédula (Anverso):</label>
                            <input type="file" name="foto_anverso_cedula" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['foto_anverso_cedula'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cédula (Reverso):</label>
                            <input type="file" name="foto_reverso_cedula" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['foto_reverso_cedula'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Certificado (Anverso):</label>
                            <input type="file" name="foto_anverso_certificado" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['foto_anverso_certificado'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Certificado (Reverso):</label>
                            <input type="file" name="foto_reverso_certificado" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['foto_reverso_certificado'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Antecedente Policial:</label>
                            <input type="file" name="antecedente_policial" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['antecedente_policial'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Certificado Médico:</label>
                            <input type="file" name="cert_medic" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['cert_medic'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Certificado de Nacimiento:</label>
                            <input type="file" name="cert_nacim" accept="image/*,application/pdf">
                            <?php if (!empty($usuario['cert_nacim'])): ?>
                                <div class="current-file">✓ Documento actual cargado</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="section">
                    <h3>🔒 Seguridad</h3>
                    <div class="form-group">
                        <label>Nueva Contraseña:</label>
                        <input type="password" name="nueva_contrasena" placeholder="Dejar vacío para mantener la actual">
                        <div class="password-note">Solo complete si desea cambiar su contraseña (mínimo 6 caracteres)</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">💾 Guardar Cambios</button>
                    <a href="perfil.php" class="btn-cancel">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validarFormulario() {
            // Validar fecha de nacimiento
            const fechaNacimiento = document.querySelector('input[name="fecha_nacimiento"]');
            if (fechaNacimiento.value) {
                const fecha = new Date(fechaNacimiento.value);
                const hoy = new Date();
                if (fecha > hoy) {
                    alert('La fecha de nacimiento no puede ser futura');
                    return false;
                }
            }

            // Validar contraseña
            const nuevaContrasena = document.querySelector('input[name="nueva_contrasena"]');
            if (nuevaContrasena.value && nuevaContrasena.value.length < 6) {
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }

            // Validar archivos (tamaño máximo 5MB)
            const archivos = document.querySelectorAll('input[type="file"]');
            for (let archivo of archivos) {
                if (archivo.files[0] && archivo.files[0].size > 5 * 1024 * 1024) {
                    alert(`El archivo ${archivo.name} es demasiado grande. Máximo 5MB.`);
                    return false;
                }
            }

            return true;
        }

        // Mostrar/ocultar campo de condición específica
        document.addEventListener('DOMContentLoaded', function() {
            const condicionMedica = document.querySelector('select[name="condicion_medica"]');
            const condicionEspecifica = document.querySelector('input[name="condicion_especifica"]');
            
            function toggleCondicionEspecifica() {
                if (condicionMedica.value === 'Otra') {
                    condicionEspecifica.required = true;
                } else {
                    condicionEspecifica.required = false;
                    condicionEspecifica.value = '';
                }
            }
            
            condicionMedica.addEventListener('change', toggleCondicionEspecifica);
            toggleCondicionEspecifica();
        });

        // Vista previa de archivos
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Aquí puedes agregar vista previa si es imagen
                    console.log('Archivo seleccionado:', file.name, 'Tamaño:', file.size);
                }
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
