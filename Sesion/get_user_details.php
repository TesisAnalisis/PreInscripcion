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

// Obtener ID del usuario desde la solicitud
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Consulta para obtener todos los datos del usuario
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Función para verificar si un documento existe
        function documentExists($data) {
            return !empty($data) && $data !== null;
        }
        
        // Mostrar detalles del usuario
        echo '<div class="user-details">';
        echo '<div class="row">';
        
        // Información personal
        echo '<div class="col-md-6">';
        echo '<h5 class="mb-3"><i class="fas fa-user me-2"></i>Información Personal</h5>';
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Nombre Completo</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Cédula</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['cedula_numero'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Fecha de Nacimiento</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['fecha_nacimiento'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Género</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['sexo'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Nacionalidad</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['nacionalidad'] ?? 'No especificado') . '</div>';
        echo '</div>';
        echo '</div>'; // cierre col-md-6
        
        // Información de contacto
        echo '<div class="col-md-6">';
        echo '<h5 class="mb-3"><i class="fas fa-address-card me-2"></i>Información de Contacto</h5>';
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Correo Electrónico</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['correo']) . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Teléfono</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['telefono']) . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Teléfono de Emergencia</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['telefono_emergencia'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Dirección</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['direccion']) . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Distrito</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['distrito']) . '</div>';
        echo '</div>';
        echo '</div>'; // cierre col-md-6
        echo '</div>'; // cierre row
        
        // Información académica
        echo '<div class="row mt-4">';
        echo '<div class="col-md-6">';
        echo '<h5 class="mb-3"><i class="fas fa-graduation-cap me-2"></i>Información Académica</h5>';
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Facultad/Carrera</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['carrera']) . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Colegio de Egreso</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['colegio_egresado'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Año de Egreso</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['anio_egreso'] ?? 'No especificado') . '</div>';
        echo '</div>';
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Cómo se enteró</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['como_se_entero'] ?? 'No especificado') . '</div>';
        echo '</div>';
        echo '</div>'; // cierre col-md-6
        
        // Información médica y documentos
        echo '<div class="col-md-6">';
        echo '<h5 class="mb-3"><i class="fas fa-file-medical me-2"></i>Información Médica y Documentos</h5>';
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Condición Médica</div>';
        echo '<div class="user-detail-value">' . htmlspecialchars($user['condicion_medica'] ?? 'Ninguna') . '</div>';
        echo '</div>';
        
        if (!empty($user['condicion_especifica'])) {
            echo '<div class="user-detail-item">';
            echo '<div class="user-detail-label">Condición Específica</div>';
            echo '<div class="user-detail-value">' . htmlspecialchars($user['condicion_especifica']) . '</div>';
            echo '</div>';
        }
        
        echo '<div class="user-detail-item">';
        echo '<div class="user-detail-label">Documentos Subidos</div>';
        echo '<div class="user-detail-value">';
        echo '<span class="document-badge ' . (documentExists($user['foto_anverso_cedula']) ? 'document-present' : 'document-missing') . '">Cédula (anverso)</span>';
        echo '<span class="document-badge ' . (documentExists($user['foto_reverso_cedula']) ? 'document-present' : 'document-missing') . '">Cédula (reverso)</span>';
        echo '<span class="document-badge ' . (documentExists($user['foto_anverso_certificado']) ? 'document-present' : 'document-missing') . '">Certificado (anverso)</span>';
        echo '<span class="document-badge ' . (documentExists($user['foto_reverso_certificado']) ? 'document-present' : 'document-missing') . '">Certificado (reverso)</span>';
        echo '<span class="document-badge ' . (documentExists($user['antecedente_policial']) ? 'document-present' : 'document-missing') . '">Antecedentes</span>';
        echo '<span class="document-badge ' . (documentExists($user['cert_medic']) ? 'document-present' : 'document-missing') . '">Cert. Médico</span>';
        echo '<span class="document-badge ' . (documentExists($user['cert_nacim']) ? 'document-present' : 'document-missing') . '">Cert. Nacimiento</span>';
        echo '<span class="document-badge ' . (documentExists($user['foto_carnet']) ? 'document-present' : 'document-missing') . '">Foto Carnet</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // cierre col-md-6
        echo '</div>'; // cierre row
        
        echo '</div>'; // cierre user-details
    } else {
        echo '<div class="alert alert-danger">Usuario no encontrado.</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">ID de usuario no especificado.</div>';
}

$conn->close();
?>
