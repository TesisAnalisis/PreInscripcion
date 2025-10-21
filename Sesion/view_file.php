<?php
// view_file.php
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

if (isset($_POST['id']) && isset($_POST['field'])) {
    $id = intval($_POST['id']);
    $field = $_POST['field'];
    
    // Validar que el campo sea uno permitido
    $allowed_fields = [
        'foto_anverso_cedula', 'foto_reverso_cedula', 'foto_anverso_certificado',
        'foto_reverso_certificado', 'antecedente_policial', 'cert_medic',
        'cert_nacim', 'foto_carnet'
    ];
    
    if (!in_array($field, $allowed_fields)) {
        die("Campo no válido.");
    }
    
    // Obtener el archivo de la base de datos
    $sql = "SELECT $field FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($file_data);
    $stmt->fetch();
    
    if ($file_data) {
        // Determinar el tipo de contenido
        $finfo = new finfo(FILEINFO_MIME);
        $mime_type = $finfo->buffer($file_data);
        
        header("Content-Type: $mime_type");
        echo $file_data;
    } else {
        http_response_code(404);
        echo "Archivo no encontrado.";
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo "Solicitud incorrecta.";
}

$conn->close();
?>
