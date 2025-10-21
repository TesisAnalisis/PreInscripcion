<?php
session_start();

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

if (!isset($_SESSION['usuario_id'])) {
    die("No ha iniciado sesión.");
}

$usuario_id = $_SESSION['usuario_id'];

// Recibir datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
$anio_egreso = $_POST['anio_egreso'] ?? null;
$colegio_egresado = $_POST['colegio_egresado'] ?? '';

$contrasena = $_POST['contrasena'] ?? '';

function procesarArchivo($campo) {
    if (isset($_FILES[$campo]) && $_FILES[$campo]['tmp_name'] != '') {
        return addslashes(file_get_contents($_FILES[$campo]['tmp_name']));
    }
    return null;
}

// Archivos opcionales
$foto_anverso_cedula = procesarArchivo('foto_anverso_cedula');
$foto_reverso_cedula = procesarArchivo('foto_reverso_cedula');
$foto_anverso_certificado = procesarArchivo('foto_anverso_certificado');
$foto_reverso_certificado = procesarArchivo('foto_reverso_certificado');
$antecedente_policial = procesarArchivo('antecedente_policial');
$cert_medic = procesarArchivo('cert_medic');
$cert_nacim = procesarArchivo('cert_nacim');

// Iniciar la consulta UPDATE
$sql = "UPDATE usuarios SET 
        nombre=?, apellido=?, correo=?, direccion=?, telefono=?, sexo=?, fecha_nacimiento=?, anio_egreso=?, colegio_egresado=?";

// Parámetros para bind_param
$tipos = "sssssssis"; // s: string, i: int (anio_egreso puede ser int o string, lo ajustamos como string para evitar error)
$params = [$nombre, $apellido, $correo, $direccion, $telefono, $sexo, $fecha_nacimiento, $anio_egreso, $colegio_egresado];

// Agregar los campos de archivos si tienen datos
if ($foto_anverso_cedula !== null) {
    $sql .= ", foto_anverso_cedula=?";
    $tipos .= "s";
    $params[] = $foto_anverso_cedula;
}
if ($foto_reverso_cedula !== null) {
    $sql .= ", foto_reverso_cedula=?";
    $tipos .= "s";
    $params[] = $foto_reverso_cedula;
}
if ($foto_anverso_certificado !== null) {
    $sql .= ", foto_anverso_certificado=?";
    $tipos .= "s";
    $params[] = $foto_anverso_certificado;
}
if ($foto_reverso_certificado !== null) {
    $sql .= ", foto_reverso_certificado=?";
    $tipos .= "s";
    $params[] = $foto_reverso_certificado;
}
if ($antecedente_policial !== null) {
    $sql .= ", antecedente_policial=?";
    $tipos .= "s";
    $params[] = $antecedente_policial;
}
if ($cert_medic !== null) {
    $sql .= ", cert_medic=?";
    $tipos .= "s";
    $params[] = $cert_medic;
}
if ($cert_nacim !== null) {
    $sql .= ", cert_nacim=?";
    $tipos .= "s";
    $params[] = $cert_nacim;
}

// Contraseña (solo si cambió)
if (!empty($contrasena)) {
    $hash_contra = password_hash($contrasena, PASSWORD_BCRYPT);
    $sql .= ", contrasena=?";
    $tipos .= "s";
    $params[] = $hash_contra;
}

$sql .= " WHERE id=?";

$tipos .= "i";
$params[] = $usuario_id;

// Preparar y ejecutar
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param($tipos, ...$params);

if ($stmt->execute()) {
    // Éxito, mostrar mensaje estilizado
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8" />
      <title>Perfil actualizado</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          background: #f0f0f0;
          margin: 0;
          padding: 0;
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .mensaje {
          background: rgba(240, 240, 240, 0.95);
          padding: 30px 40px;
          border-radius: 15px;
          box-shadow: 0 8px 20px rgba(0,0,0,0.15);
          text-align: center;
          max-width: 400px;
          color: #2c3e50;
        }
        .mensaje h2 {
          margin-bottom: 25px;
        }
        .btn-volver {
          display: inline-block;
          background-color: #007bff;
          color: white;
          text-decoration: none;
          padding: 12px 25px;
          border-radius: 8px;
          font-weight: bold;
          transition: background-color 0.3s ease;
        }
        .btn-volver:hover {
          background-color: #0056b3;
        }
      </style>
    </head>
    <body>
      <div class="mensaje">
        <h2>Perfil actualizado correctamente</h2>
        <a class="btn-volver" href="perfil.php">Volver a mi perfil</a>
      </div>
    </body>
    </html>
    <?php
} else {
    echo "Error al actualizar perfil: " . $stmt->error;
}

$stmt->close();
$conn->close();

