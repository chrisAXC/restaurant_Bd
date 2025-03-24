<?php
require(__DIR__ . '/fpdf186/fpdf.php');
include 'conexion.php';

// Generar la gráfica antes de crear el PDF
if (file_exists(__DIR__ . '/generar_grafica.php')) {
    include(__DIR__ . '/generar_grafica.php');
} else {
    die("Error: El archivo generar_grafica.php no existe.");
}

// Verificar que la imagen de la gráfica existe antes de agregarla al PDF
if (!file_exists(__DIR__ . '/grafica_ventas.png')) {
    die("Error: La imagen de la gráfica no se generó correctamente.");
}

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Reporte de Ventas', 1, 1, 'C');
$pdf->Ln(5);

// Definir encabezados
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, 'ID Venta', 1, 0, 'C');
$pdf->Cell(35, 10, 'Fecha', 1, 0, 'C');
$pdf->Cell(20, 10, 'Mesa', 1, 0, 'C');
$pdf->Cell(45, 10, 'Empleado', 1, 0, 'C');
$pdf->Cell(35, 10, 'Platillo', 1, 0, 'C');
$pdf->Cell(15, 10, 'Cant.', 1, 0, 'C');
$pdf->Cell(20, 10, 'Total', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Consultar la base de datos
$query = "SELECT v.id_venta, v.fecha_venta, v.mesa, v.total, 
                 p.nombre AS platillo, d.cantidad, e.nombre AS empleado
          FROM ventas v
          JOIN detalle_ventas d ON v.id_venta = d.id_venta
          JOIN platillos p ON d.id_platillo = p.id_platillo
          JOIN empleados e ON v.id_empleado = e.id
          ORDER BY v.fecha_venta DESC";

$result = mysqli_query($conn, $query);

// Array para la gráfica de barras
$ventas_por_platillo = [];

while ($venta = mysqli_fetch_assoc($result)) {
    $pdf->Cell(20, 10, $venta['id_venta'], 1, 0, 'C');
    $pdf->Cell(35, 10, $venta['fecha_venta'], 1, 0, 'C');
    $pdf->Cell(20, 10, $venta['mesa'], 1, 0, 'C');
    $pdf->Cell(45, 10, utf8_decode($venta['empleado']), 1, 0, 'C');
    $pdf->Cell(35, 10, utf8_decode($venta['platillo']), 1, 0, 'C');
    $pdf->Cell(15, 10, $venta['cantidad'], 1, 0, 'C');
    $pdf->Cell(20, 10, number_format($venta['total'], 2), 1, 1, 'C');

    // Guardar datos para la gráfica
    if (!isset($ventas_por_platillo[$venta['platillo']])) {
        $ventas_por_platillo[$venta['platillo']] = 0;
    }
    $ventas_por_platillo[$venta['platillo']] += $venta['total'];
}

// Crear una nueva página para la gráfica
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Grafica de Ventas por Platillo', 0, 1, 'C');
$pdf->Ln(5);

// Agregar la imagen de la gráfica al PDF
$pdf->Image(__DIR__ . '/grafica_ventas.png', 20, 30, 170);

// Generar PDF
$pdf->Output();
?>
