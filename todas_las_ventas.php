<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT v.id_venta, v.fecha_venta, v.mesa, v.subtotal, v.iva, v.total, 
                 p.nombre AS platillo, d.cantidad, d.precio_unitario, d.precio_total, e.nombre AS empleado
          FROM ventas v
          JOIN detalle_ventas d ON v.id_venta = d.id_venta
          JOIN platillos p ON d.id_platillo = p.id_platillo
          JOIN empleados e ON v.id_empleado = e.id
          ORDER BY v.fecha_venta DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Todas las Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; }
        .container { width: 80%; margin: auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); }
        h2 { margin-bottom: 20px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid black; text-align: center; }
        th { background-color: #343a40; color: white; }
        .btn { padding: 10px; font-size: 16px; border: none; cursor: pointer; border-radius: 5px; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h2>📊 Todas las Ventas Registradas</h2>

        <table>
            <tr>
                <th>ID Venta</th>
                <th>Fecha</th>
                <th>Mesa</th>
                <th>Empleado</th>
                <th>Platillo</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Precio Total</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
            <?php while ($venta = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $venta['id_venta']; ?></td>
                    <td><?php echo $venta['fecha_venta']; ?></td>
                    <td><?php echo $venta['mesa']; ?></td>
                    <td><?php echo $venta['empleado']; ?></td>
                    <td><?php echo $venta['platillo']; ?></td>
                    <td><?php echo $venta['cantidad']; ?></td>
                    <td><?php echo number_format($venta['precio_unitario'], 2); ?></td>
                    <td><?php echo number_format($venta['precio_total'], 2); ?></td>
                    <td><?php echo number_format($venta['subtotal'], 2); ?></td>
                    <td><?php echo number_format($venta['iva'], 2); ?></td>
                    <td><?php echo number_format($venta['total'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <button onclick="location.href='ventas.php'" class="btn btn-secondary">⬅ Volver</button>
        <button onclick="location.href='generar_pdf.php'" class="btn btn-primary">📄 Imprimir PDF</button>
    </div>

</body>
</html>
