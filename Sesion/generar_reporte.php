<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');

// ============================
// Conexión BD
// ============================
$servername = "localhost";
$username = "root";
$password = "Azteca1@1#1@1";
$dbname = "preinscripcion";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error conexión: " . $conn->connect_error);
}

// ============================
// Crear PDF
// ============================
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Sistema de Preinscripción");
$pdf->SetTitle("Reporte General de Preinscripción");
$pdf->AddPage();

$pdf->SetFont('helvetica','B',16);
$pdf->Cell(0,10,"Reporte General de Preinscripción",0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica','',11);

// ============================
// Función para hacer tablas HTML bonitas
// ============================
function buildTable($headers, $rows) {
    $html = '<table border="1" cellpadding="6"><tr style="background-color:#f2f2f2;">';
    foreach ($headers as $h) {
        $html .= "<th><b>$h</b></th>";
    }
    $html .= "</tr>";
    foreach ($rows as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) {
            $html .= "<td>$cell</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table><br>";
    return $html;
}

// ============================
// Totales
// ============================
$res = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol_id=2");
$total = $res->fetch_assoc()['total'];

$pdf->writeHTML("<h3>Resumen General</h3><p><b>Total de inscritos:</b> $total</p>", true, false, true, false, '');

// ============================
// Distribución por carrera
// ============================
$res = $conn->query("SELECT carrera, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY carrera ORDER BY cantidad DESC");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $porc = $total > 0 ? round(($r['cantidad']/$total)*100,2)."%" : "0%";
    $rows[] = [$r['carrera'], $r['cantidad'], $porc];
}
$pdf->writeHTML("<h3>Distribución por Carrera</h3>".buildTable(["Carrera","Cantidad","Porcentaje"],$rows), true, false, true, false, '');

// ============================
// Distribución por sexo
// ============================
$res = $conn->query("SELECT sexo, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY sexo");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $porc = $total > 0 ? round(($r['cantidad']/$total)*100,2)."%" : "0%";
    $rows[] = [$r['sexo'], $r['cantidad'], $porc];
}
$pdf->writeHTML("<h3>Distribución por Sexo</h3>".buildTable(["Sexo","Cantidad","Porcentaje"],$rows), true, false, true, false, '');

// ============================
// Condiciones médicas
// ============================
$res = $conn->query("SELECT condicion_medica, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY condicion_medica ORDER BY cantidad DESC");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [$r['condicion_medica'], $r['cantidad']];
}
$pdf->writeHTML("<h3>Condiciones Médicas</h3>".buildTable(["Condición Médica","Cantidad"],$rows), true, false, true, false, '');

// ============================
// Fuentes de información
// ============================
$res = $conn->query("SELECT como_se_entero, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY como_se_entero ORDER BY cantidad DESC");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $porc = $total > 0 ? round(($r['cantidad']/$total)*100,2)."%" : "0%";
    $rows[] = [$r['como_se_entero'], $r['cantidad'], $porc];
}
$pdf->writeHTML("<h3>Fuentes de Información</h3>".buildTable(["Fuente","Cantidad","Porcentaje"],$rows), true, false, true, false, '');

// ============================
// Top 10 Colegios
// ============================
$res = $conn->query("SELECT colegio_egresado, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY colegio_egresado ORDER BY cantidad DESC LIMIT 10");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [$r['colegio_egresado'], $r['cantidad']];
}
$pdf->writeHTML("<h3>Top 10 Colegios de Egreso</h3>".buildTable(["Colegio","Cantidad"],$rows), true, false, true, false, '');

// ============================
// Top 10 Distritos
// ============================
$res = $conn->query("SELECT distrito, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY distrito ORDER BY cantidad DESC LIMIT 10");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [$r['distrito'], $r['cantidad']];
}
$pdf->writeHTML("<h3>Top 10 Distritos de Residencia</h3>".buildTable(["Distrito","Cantidad"],$rows), true, false, true, false, '');

// ============================
// Distribución por año de egreso
// ============================
$res = $conn->query("SELECT anio_egreso, COUNT(*) as cantidad FROM usuarios WHERE rol_id=2 GROUP BY anio_egreso ORDER BY anio_egreso ASC");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [$r['anio_egreso'], $r['cantidad']];
}
$pdf->writeHTML("<h3>Distribución por Año de Egreso</h3>".buildTable(["Año de Egreso","Cantidad"],$rows), true, false, true, false, '');

// ============================
// Descargar PDF
// ============================
$pdf->Output("reporte_general.pdf","I");

$conn->close();
?>

