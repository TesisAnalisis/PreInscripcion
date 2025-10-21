<?php
// update_user.php
session_start();

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

// Inicializar variables
$error = '';
$success = '';
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

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

// Lista de campos de imagen
$image_fields = [
    'foto_anverso_cedula', 'foto_reverso_cedula', 'foto_anverso_certificado',
    'foto_reverso_certificado', 'antecedente_policial', 'cert_medic',
    'cert_nacim', 'foto_carnet'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_id > 0) {
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
    
    // Obtener datos actuales del usuario para comparar archivos
    $sql_user = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $current_user_data = $user_result->fetch_assoc();
    } else {
        $error = "Usuario no encontrado.";
    }
    $stmt->close();
    
    if (empty($error)) {
        // Procesar archivos (mantener existentes si no se suben nuevos)
        $file_updates = [];
        $file_params = [];
        $file_types = "";
        
        foreach ($image_fields as $field) {
            $processed_file = processFile($_FILES[$field] ?? null, $current_user_data[$field] ?? null);
            
            if ($processed_file === false) {
                $error = "Error en el archivo $field: tipo no permitido o demasiado grande.";
                break;
            }
            
            if ($processed_file !== $current_user_data[$field]) {
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
                    $_SESSION['success_message'] = $success;
                    
                    // Redirigir de vuelta a la página de edición
                    header("Location: admin_edit.php?action=edit&id=" . $user_id . "&success=1");
                    exit();
                } else {
                    $error = "Error al actualizar el usuario: " . $stmt->error;
                    $_SESSION['error_message'] = $error;
                    header("Location: admin_edit.php?action=edit&id=" . $user_id . "&error=1");
                    exit();
                }
                $stmt->close();
            } else {
                $error = "Error al preparar la consulta: " . $conn->error;
                $_SESSION['error_message'] = $error;
                header("Location: admin_edit.php?action=edit&id=" . $user_id . "&error=1");
                exit();
            }
        }
    }
} else {
    $error = "Solicitud inválida.";
    $_SESSION['error_message'] = $error;
    header("Location: admin_edit.php?action=view");
    exit();
}

// Si llegamos aquí, hubo un error
$_SESSION['error_message'] = $error;
header("Location: admin_edit.php?action=edit&id=" . $user_id . "&error=1");
exit();
?>
