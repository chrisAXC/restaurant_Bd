<?php
include 'conexion.php';

// Consulta las ventas por platillo
$query = "SELECT p.nombre AS platillo, SUM(v.total) AS total
          FROM ventas v
          JOIN detalle_ventas d ON v.id_venta = d.id_venta
          JOIN platillos p ON d.id_platillo = p.id_platillo
          GROUP BY p.nombre
          ORDER BY total DESC";

$result = mysqli_query($conn, $query);

// Obtener datos para la gráfica
$platillos = [];
$totales = [];
while ($row = mysqli_fetch_assoc($result)) {
    $platillos[] = $row['platillo'];
    $totales[] = $row['total'];
}

// Verificar si hay datos
if (count($platillos) == 0) {
    die("No hay datos para generar la gráfica.");
}

// Crear imagen
$width = 800;
$height = 400;
$image = imagecreate($width, $height);

// Colores
$background = imagecolorallocate($image, 255, 255, 255);
$barColor = imagecolorallocate($image, 54, 162, 235);
$borderColor = imagecolorallocate($image, 0, 0, 0);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Dibujar barras
$barWidth = 50;
$maxValue = max($totales);
$x = 80; // Posición inicial de la primera barra
for ($i = 0; $i < count($platillos); $i++) {
    $barHeight = ($totales[$i] / $maxValue) * 300;
    imagefilledrectangle($image, $x, 350 - $barHeight, $x + $barWidth, 350, $barColor);
    imagerectangle($image, $x, 350 - $barHeight, $x + $barWidth, 350, $borderColor);
    imagestring($image, 3, $x, 355, substr($platillos[$i], 0, 8), $textColor);
    imagestring($image, 3, $x + 10, 340 - $barHeight, round($totales[$i]), $textColor);
    $x += 80;
}

// Guardar la imagen
imagepng($image, __DIR__ . '/grafica_ventas.png');
imagedestroy($image);
?>
