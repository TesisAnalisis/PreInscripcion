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
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Función para detectar el tipo de archivo
function detectFileType($data) {
    if (empty($data)) return 'empty';
    
    // Verificar si es imagen
    $imageInfo = @getimagesizefromstring($data);
    if ($imageInfo !== false) {
        return 'image';
    }
    
    // Verificar si es PDF (cabecera PDF)
    if (strlen($data) > 4 && substr($data, 0, 4) == "%PDF") {
        return 'pdf';
    }
    
    // Por defecto, tratar como binario/descargable
    return 'binary';
}

// Función para obtener la extensión del archivo basado en el contenido
function getFileExtension($data) {
    $signatures = [
        'jpg' => "\xFF\xD8\xFF",
        'png' => "\x89\x50\x4E\x47",
        'gif' => "GIF",
        'pdf' => "%PDF",
        'jpeg' => "\xFF\xD8\xFF"
    ];
    
    foreach ($signatures as $ext => $sig) {
        if (substr($data, 0, strlen($sig)) === $sig) {
            return $ext;
        }
    }
    
    return 'bin';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Datos Completos</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 0;
        }
        .header { 
            background: #2c3e50; 
            color: white; 
            padding: 1rem; 
            text-align: center;
        }
        .container { 
            max-width: 1200px; 
            margin: 2rem auto; 
            background: white; 
            padding: 2rem; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .section { 
            margin-bottom: 2rem; 
            padding-bottom: 1rem; 
            border-bottom: 1px solid #eee; 
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 1rem; 
        }
        .info-item { 
            margin-bottom: 1rem; 
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .doc-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 1.5rem; 
            margin-top: 1rem;
        }
        .doc-item { 
            text-align: center; 
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .doc-item img { 
            max-width: 100%; 
            max-height: 300px; 
            border: 1px solid #ccc; 
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .doc-preview {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .btn { 
            display: inline-block; 
            padding: 8px 16px; 
            background: #3498db; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 0.25rem;
            font-size: 0.9rem;
        }
        .btn:hover { 
            background: #2980b9; 
        }
        .btn-download {
            background: #27ae60;
        }
        .btn-download:hover {
            background: #219a52;
        }
        .no-doc {
            color: #666;
            font-style: italic;
        }
        .doc-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
        }
        .back-btn:hover {
            background: #7f8c8d;
        }
        .doc-actions {
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mis Datos Completos</h1>
        <p>Bienvenido, <?= htmlspecialchars($usuario['nombre'] . ' ' . htmlspecialchars($usuario['apellido'])); ?></p>
    </div>
    
    <div class="container">
        <!-- Datos Personales -->
        <div class="section">
            <h2>📋 Datos Personales</h2>
            <div class="info-grid">
                <div class="info-item"><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']); ?></div>
                <div class="info-item"><strong>Apellido:</strong> <?= htmlspecialchars($usuario['apellido']); ?></div>
                <div class="info-item"><strong>Cédula:</strong> <?= htmlspecialchars($usuario['cedula_numero']); ?></div>
                <div class="info-item"><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']); ?></div>
                <div class="info-item"><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']); ?></div>
                <div class="info-item"><strong>Teléfono Emergencia:</strong> <?= htmlspecialchars($usuario['telefono_emergencia']); ?></div>
                <div class="info-item"><strong>Dirección:</strong> <?= htmlspecialchars($usuario['direccion']); ?></div>
                <div class="info-item"><strong>Distrito:</strong> <?= htmlspecialchars($usuario['distrito']); ?></div>
                <div class="info-item"><strong>Nacionalidad:</strong> <?= htmlspecialchars($usuario['nacionalidad']); ?></div>
                <div class="info-item"><strong>Sexo:</strong> <?= htmlspecialchars($usuario['sexo']); ?></div>
                <div class="info-item"><strong>Fecha Nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento']); ?></div>
            </div>
        </div>

        <!-- Datos Académicos -->
        <div class="section">
            <h2>🎓 Datos Académicos</h2>
            <div class="info-grid">
                <div class="info-item"><strong>Año Egreso:</strong> <?= htmlspecialchars($usuario['anio_egreso']); ?></div>
                <div class="info-item"><strong>Colegio:</strong> <?= htmlspecialchars($usuario['colegio_egresado']); ?></div>
                <div class="info-item"><strong>Carrera:</strong> <?= htmlspecialchars($usuario['carrera']); ?></div>
            </div>
        </div>

        <!-- Otros Datos -->
        <div class="section">
            <h2>📊 Otros Datos</h2>
            <div class="info-grid">
                <div class="info-item"><strong>Condición Médica:</strong> <?= htmlspecialchars($usuario['condicion_medica'] ?: 'No especificada'); ?></div>
                <div class="info-item"><strong>Condición Específica:</strong> <?= htmlspecialchars($usuario['condicion_especifica'] ?: 'No especificada'); ?></div>
                <div class="info-item"><strong>Como se enteró:</strong> <?= htmlspecialchars($usuario['como_se_entero']); ?></div>
            </div>
        </div>

        <!-- Documentos Adjuntos -->
        <div class="section">
            <h2>📎 Documentos Adjuntos</h2>
            <div class="doc-grid">
                <?php
                $docs = [
                    'foto_anverso_cedula' => '📷 Cédula (Anverso)',
                    'foto_reverso_cedula' => '📷 Cédula (Reverso)',
                    'foto_anverso_certificado' => '📄 Certificado (Anverso)',
                    'foto_reverso_certificado' => '📄 Certificado (Reverso)',
                    'antecedente_policial' => '👮 Antecedente Policial',
                    'cert_medic' => '🏥 Certificado Médico',
                    'cert_nacim' => '👶 Certificado de Nacimiento',
                    'foto_carnet' => '🖼️ Foto Carnet'
                ];

                foreach ($docs as $campo => $titulo) {
                    echo "<div class='doc-item'>";
                    echo "<div class='doc-title'>$titulo</div>";
                    
                    if (!empty($usuario[$campo])) {
                        $data = $usuario[$campo];
                        $base64 = base64_encode($data);
                        $fileType = detectFileType($data);
                        $fileExt = getFileExtension($data);
                        
                        switch ($fileType) {
                            case 'image':
                                // Mostrar imagen
                                echo "<img src='data:image/jpeg;base64,$base64' alt='$titulo' onerror='this.style.display=\"none\"'>";
                                echo "<div class='doc-actions'>";
                                echo "<a href='data:image/jpeg;base64,$base64' download='$titulo.$fileExt' class='btn btn-download'>Descargar Imagen</a>";
                                echo "</div>";
                                break;
                                
                            case 'pdf':
                                // Mostrar PDF embebido con opción de descarga
                                echo "<embed class='doc-preview' src='data:application/pdf;base64,$base64' type='application/pdf'>";
                                echo "<div class='doc-actions'>";
                                echo "<a href='data:application/pdf;base64,$base64' download='$titulo.pdf' class='btn btn-download'>Descargar PDF</a>";
                                echo "</div>";
                                break;
                                
                            default:
                                // Para otros tipos de archivo, solo ofrecer descarga
                                echo "<p>Documento disponible para descarga</p>";
                                echo "<div class='doc-actions'>";
                                echo "<a href='data:application/octet-stream;base64,$base64' download='$titulo.$fileExt' class='btn btn-download'>Descargar Archivo</a>";
                                echo "</div>";
                                break;
                        }
                    } else {
                        echo "<p class='no-doc'>Documento no cargado</p>";
                    }
                    
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="perfil.php" class="back-btn">← Volver al Panel Principal</a>
            <a href="editar_datos.php" class="btn" style="margin-left: 1rem;">✏️ Editar Mis Datos</a>
        </div>
    </div>

    <script>
        // Función para manejar errores en la carga de imágenes/embeds
        document.addEventListener('DOMContentLoaded', function() {
            const embeds = document.querySelectorAll('embed');
            embeds.forEach(embed => {
                embed.addEventListener('error', function() {
                    this.replaceWith(document.createTextNode('No se pudo cargar la vista previa. Use el botón de descarga.'));
                });
            });
            
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    const container = this.parentElement;
                    const message = document.createElement('p');
                    message.textContent = 'Imagen no disponible para vista previa';
                    message.className = 'no-doc';
                    container.insertBefore(message, this.nextSibling);
                });
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
