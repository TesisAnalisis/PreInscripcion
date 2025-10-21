<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');

// ============================
// Conexión BD
// ============================
$servername = "localhost";
$username   = "root";
$password   = "Azteca1@1#1@1";
$dbname     = "preinscripcion";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error conexión: " . $conn->connect_error);
}

if (!isset($_GET['cedula'])) {
    die("Cédula no proporcionada");
}
$cedula = $conn->real_escape_string($_GET['cedula']);

// ============================
// Buscar estudiante
// ============================
$sql = "SELECT * FROM usuarios WHERE cedula_numero='$cedula' AND rol_id = 2 LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("No se encontró el alumno");
}
$user = $result->fetch_assoc();

// ============================
// Crear PDF
// ============================
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica','B',14);
$pdf->Cell(0,10,"Ficha Completa de Preinscripción",0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica','',11);

// ============================
// Información general
// ============================
$html = "
<h3>Datos Personales</h3>
<table border='1' cellpadding='6'>
<tr><td><b>Nombre</b></td><td>{$user['nombre']} {$user['apellido']}</td></tr>
<tr><td><b>Cédula</b></td><td>{$user['cedula_numero']}</td></tr>
<tr><td><b>Correo</b></td><td>{$user['correo']}</td></tr>
<tr><td><b>Teléfono</b></td><td>{$user['telefono']}</td></tr>
<tr><td><b>Teléfono Emergencia</b></td><td>{$user['telefono_emergencia']}</td></tr>
<tr><td><b>Dirección</b></td><td>{$user['direccion']}</td></tr>
<tr><td><b>Distrito</b></td><td>{$user['distrito']}</td></tr>
<tr><td><b>Sexo</b></td><td>{$user['sexo']}</td></tr>
<tr><td><b>Fecha de Nacimiento</b></td><td>{$user['fecha_nacimiento']}</td></tr>
<tr><td><b>Nacionalidad</b></td><td>{$user['nacionalidad']}</td></tr>
</table>
<br>

<h3>Datos Académicos</h3>
<table border='1' cellpadding='6'>
<tr><td><b>Colegio de Egreso</b></td><td>{$user['colegio_egresado']}</td></tr>
<tr><td><b>Año de Egreso</b></td><td>{$user['anio_egreso']}</td></tr>
<tr><td><b>Carrera Elegida</b></td><td>{$user['carrera']}</td></tr>
<tr><td><b>¿Cómo se enteró?</b></td><td>{$user['como_se_entero']}</td></tr>
</table>
<br>

<h3>Condiciones Médicas</h3>
<table border='1' cellpadding='6'>
<tr><td><b>Condición Médica</b></td><td>{$user['condicion_medica']}</td></tr>
<tr><td><b>Condición Específica</b></td><td>{$user['condicion_especifica']}</td></tr>
</table>
<br>
";
$pdf->writeHTML($html, true, false, true, false, '');

// ============================
// Función para mostrar imágenes
// ============================
function mostrarDocumento($pdf, $label, $binario) {
    $pdf->SetFont('helvetica','B',11);
    $pdf->Cell(0,8,$label,0,1);

    if (!empty($binario)) {
        // Detectar MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->buffer($binario);

        // Archivo temporal en JPG
        $tmpFile = tempnam(sys_get_temp_dir(), "img_") . ".jpg";

        // Si es PNG → convertir a JPG
        if ($mime === 'image/png') {
            $im = imagecreatefromstring($binario);
            if ($im !== false) {
                // Fondo blanco para evitar transparencia
                $bg = imagecreatetruecolor(imagesx($im), imagesy($im));
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $im, 0, 0, 0, 0, imagesx($im), imagesy($im));
                imagejpeg($bg, $tmpFile, 90);
                imagedestroy($im);
                imagedestroy($bg);
            } else {
                file_put_contents($tmpFile, $binario); // fallback
            }
        } else {
            file_put_contents($tmpFile, $binario);
        }

        // Mostrar en PDF
        if (@getimagesize($tmpFile)) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Image($tmpFile, $x, $y, 60, 0, '', '', 'T', true);
            $pdf->Ln(65);
        } else {
            $pdf->SetFont('helvetica','',10);
            $pdf->Cell(0,6,"Guardado pero no es una imagen válida",0,1);
        }

        unlink($tmpFile);
    } else {
        $pdf->SetFont('helvetica','',10);
        $pdf->Cell(0,6,"No disponible",0,1);
    }

    $pdf->Ln(3);
}

// ============================
// Documentos adjuntos
// ============================
$pdf->Ln(5);
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,10,"Documentos Adjuntos",0,1);

$documentos = [
    "Foto Anverso Cédula"       => "foto_anverso_cedula",
    "Foto Reverso Cédula"       => "foto_reverso_cedula",
    "Certificado Anverso"       => "foto_anverso_certificado",
    "Certificado Reverso"       => "foto_reverso_certificado",
    "Antecedente Policial"      => "antecedente_policial",
    "Certificado Médico"        => "cert_medic",
    "Certificado de Nacimiento" => "cert_nacim",
    "Foto Carnet"               => "foto_carnet"
];

foreach ($documentos as $label => $campo) {
    mostrarDocumento($pdf, $label, $user[$campo]);
}

// ============================
// Descargar PDF
// ============================
$pdf->Output("ficha_completa_{$user['cedula_numero']}.pdf","I");

$conn->close();
?>

