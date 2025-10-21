<?php
// ============================
// Configuración de la conexión
// ============================
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1"; // tu contraseña
$dbname = "preinscripcion";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// ============================
// Función para procesar archivos
// ============================
function processFile($file) {
    if (!isset($file['tmp_name']) || !$file['tmp_name'] || !file_exists($file['tmp_name'])) {
        return null;
    }
    
    // Leer el contenido del archivo
    $fileContent = file_get_contents($file['tmp_name']);
    return $fileContent;
}

// ============================
// Recoger datos del formulario
// ============================
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
$contrasena = password_hash($_POST['contrasena'] ?? '', PASSWORD_DEFAULT);

// Validar checkboxes
if (!isset($_POST['terminos'], $_POST['consentimiento_datos'], $_POST['declaracion_veracidad'])) {
    die("Debes aceptar los términos, el consentimiento y la declaración de veracidad.");
}

// ============================
// Verificar si el correo ya existe
// ============================
$check_email_sql = "SELECT id FROM usuarios WHERE correo = ?";
$check_stmt = $conn->prepare($check_email_sql);
$check_stmt->bind_param("s", $correo);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // El correo ya existe, mostrar mensaje de error
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error - Correo Ya Registrado</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f0f2f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                background: #fff;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 400px;
            }
            h2 {
                color: #dc3545;
                margin-bottom: 20px;
            }
            p {
                color: #555;
                font-size: 16px;
                margin-bottom: 20px;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background 0.3s;
            }
            .btn:hover {
                background: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h2>Error de Registro</h2>
            <p>El correo electrónico <strong>' . htmlspecialchars($correo) . '</strong> ya está registrado en nuestro sistema.</p>
            <p>Por favor, utilice un correo electrónico diferente o recupere su cuenta si ya está registrado.</p>
            <a href="javascript:history.back()" class="btn">Volver al Formulario</a>
        </div>
    </body>
    </html>';
    $check_stmt->close();
    $conn->close();
    exit();
}
$check_stmt->close();

// ============================
// Procesar archivos (guardar como datos binarios)
// ============================
$foto_anverso_cedula = processFile($_FILES['foto_anverso_cedula'] ?? null);
$foto_reverso_cedula = processFile($_FILES['foto_reverso_cedula'] ?? null);
$foto_anverso_certificado = processFile($_FILES['foto_anverso_certificado'] ?? null);
$foto_reverso_certificado = processFile($_FILES['foto_reverso_certificado'] ?? null);
$antecedente_policial = processFile($_FILES['antecedente_policial'] ?? null);
$cert_medic = processFile($_FILES['cert_medic'] ?? null);
$cert_nacim = processFile($_FILES['cert_nacim'] ?? null);
$foto_carnet = processFile($_FILES['foto_carnet'] ?? null);

// ============================
// Preparar la consulta
// ============================
$sql = "INSERT INTO usuarios (
    nombre, apellido, cedula_numero, correo, telefono, telefono_emergencia, direccion, distrito, nacionalidad,
    sexo, fecha_nacimiento, anio_egreso, colegio_egresado, carrera, condicion_medica, condicion_especifica,
    como_se_entero, foto_anverso_cedula, foto_reverso_cedula, foto_anverso_certificado, foto_reverso_certificado,
    antecedente_policial, cert_medic, cert_nacim, foto_carnet, contrasena, rol_id
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en prepare(): " . $conn->error);
}

// ============================
// Bind de parámetros (usando "b" para blobs)
// ============================
$rol_id = 2;
$null = null; // Variable para bind_param de blobs

$stmt->bind_param(
    "sssssssssssssssssbbbbbbbbsi",
    $nombre, $apellido, $cedula_numero, $correo, $telefono, $telefono_emergencia, $direccion, $distrito, $nacionalidad,
    $sexo, $fecha_nacimiento, $anio_egreso, $colegio_egresado, $carrera, $condicion_medica, $condicion_especifica,
    $como_se_entero, 
    $null, $null, $null, $null, $null, $null, $null, $null, 
    $contrasena, $rol_id
);

// Ahora bindeamos los blobs individualmente
$stmt->send_long_data(17, $foto_anverso_cedula);
$stmt->send_long_data(18, $foto_reverso_cedula);
$stmt->send_long_data(19, $foto_anverso_certificado);
$stmt->send_long_data(20, $foto_reverso_certificado);
$stmt->send_long_data(21, $antecedente_policial);
$stmt->send_long_data(22, $cert_medic);
$stmt->send_long_data(23, $cert_nacim);
$stmt->send_long_data(24, $foto_carnet);

// ============================
// Ejecutar y verificar
// ============================
if ($stmt->execute()) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro Exitoso</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f0f2f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .success-container {
                background: #fff;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 400px;
            }
            h2 {
                color: #28a745;
                margin-bottom: 20px;
            }
            p {
                color: #555;
                font-size: 16px;
            }
        </style>
    </head>
    <body>
        <div class="success-container">
            <h2>¡Registro exitoso!</h2>
            <p>Su inscripción ha sido guardada correctamente.</p>
        </div>
    </body>
    </html>';
} else {
    echo "Error al guardar: " . $stmt->error;
}

// Redirigir al inicio de sesión después de 3 segundos
header("refresh:3;url=http://localhost/PreInscripcion/PreInscripcion/");
$stmt->close();
$conn->close();
?>
