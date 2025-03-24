<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$ventas = [];
$busqueda = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $id_empleado = $_POST['id_empleado'];
    $busqueda = true;

    // Corrección en la consulta SQL para evitar valores NULL en cantidad y precio_total
    $query = "SELECT v.id_venta, v.fecha_venta, v.mesa, v.subtotal, v.iva, v.total, 
                     p.nombre AS platillo, 
                     IFNULL(d.cantidad, 0) AS cantidad, 
                     IFNULL(d.precio_unitario, 0) AS precio_unitario, 
                     IFNULL(d.precio_total, 0) AS precio_total
              FROM ventas v
              LEFT JOIN detalle_ventas d ON v.id_venta = d.id_venta
              LEFT JOIN platillos p ON d.id_platillo = p.id_platillo
              WHERE v.id_empleado = '$id_empleado'
              ORDER BY v.fecha_venta DESC";

    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $ventas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; }
        .container { width: 50%; margin: auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); }
        h2 { margin-bottom: 20px; }
        label { font-weight: bold; }
        select, input, button { margin: 10px; padding: 10px; width: 80%; font-size: 16px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid black; text-align: center; }
        th { background-color: #343a40; color: white; }
        .btn { padding: 10px; font-size: 16px; border: none; cursor: pointer; border-radius: 5px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; }
    </style>
</head>
<body>

    <div class="container">
        <h2>📋 Ventas</h2>

        <form method="POST" action="">
            <label for="tipo_venta">Tipo de Venta:</label>
            <select name="tipo_venta" id="tipo_venta">
                <option value="General">General</option>
            </select>

            <label for="criterio">Buscar por:</label>
            <select name="criterio">
                <option value="id_empleado">Clave del Empleado</option>
            </select>

            <input type="text" name="id_empleado" placeholder="Ingrese ID del Empleado" required>
            <button type="submit" name="buscar" class="btn btn-primary">🔍 Buscar</button>
        </form>

        <br>
        <button onclick="location.href='todas_las_ventas.php'" class="btn btn-secondary">📊 Ver Todas las Ventas</button>

        <?php if ($busqueda && count($ventas) > 0): ?>
            <h3>Resultados de la Búsqueda</h3>
            <table>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Mesa</th>
                    <th>Platillo</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?php echo $venta['id_venta']; ?></td>
                        <td><?php echo $venta['fecha_venta']; ?></td>
                        <td><?php echo $venta['mesa']; ?></td>
                        <td><?php echo $venta['platillo']; ?></td>
                        <td><?php echo $venta['cantidad']; ?></td>
                        <td><?php echo number_format($venta['precio_unitario'], 2); ?></td>
                        <td><?php echo number_format($venta['precio_total'], 2); ?></td>
                        <td><?php echo number_format($venta['subtotal'], 2); ?></td>
                        <td><?php echo number_format($venta['iva'], 2); ?></td>
                        <td><?php echo number_format($venta['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($busqueda): ?>
            <p>No se encontraron ventas para el ID de empleado ingresado.</p>
        <?php endif; ?>
    </div>

</body>
</html>