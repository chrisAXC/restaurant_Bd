<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Obtener información del vendedor
$query = "SELECT * FROM empleados WHERE usuario='$usuario'";
$result = mysqli_query($conn, $query);
$empleado = mysqli_fetch_assoc($result);

// Generar folio aleatorio
$folio = rand(100000, 999999);

// Generar un token de seguridad para evitar reenvíos
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Obtener platillos
$platillosQuery = "SELECT * FROM platillos";
$platillosResult = mysqli_query($conn, $platillosQuery);

// Procesar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vender'])) {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        die("Error: Petición inválida.");
    }

    unset($_SESSION['token']); // Eliminar el token para evitar reenvíos múltiples

    $id_empleado = $empleado['id'];
    $mesa = $_POST['mesa'];
    $fecha = $_POST['fecha'];
    $subtotal = $_POST['subtotal'];
    $iva = $_POST['iva'];
    $total = $_POST['total'];
    $id_platillo = $_POST['platillo'];
    $cantidad = $_POST['cantidad'];

    // Obtener el precio unitario del platillo seleccionado
    $queryPrecio = "SELECT precio FROM platillos WHERE id_platillo = '$id_platillo'";
    $resultPrecio = mysqli_query($conn, $queryPrecio);
    $rowPrecio = mysqli_fetch_assoc($resultPrecio);
    $precio_unitario = $rowPrecio['precio'];

    // Insertar en la tabla de ventas
    $queryVenta = "INSERT INTO ventas (id_empleado, mesa, subtotal, iva, total) 
                   VALUES ('$id_empleado', '$mesa', '$subtotal', '$iva', '$total')";
    if (mysqli_query($conn, $queryVenta)) {
        $id_venta = mysqli_insert_id($conn); // Obtener el ID de la venta recién insertada

        // Insertar en la tabla detalle_ventas
        $precio_total = $precio_unitario * $cantidad;
        $queryDetalle = "INSERT INTO detalle_ventas (id_venta, id_platillo, cantidad, precio_unitario, precio_total) 
                         VALUES ('$id_venta', '$id_platillo', '$cantidad', '$precio_unitario', '$precio_total')";
        
        if (mysqli_query($conn, $queryDetalle)) {
            echo "<script>alert('Venta exitosa'); window.location.href = 'pedido.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al registrar detalle de venta: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Error al registrar la venta: " . mysqli_error($conn) . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    
    body {
        background: linear-gradient(135deg, #ff9966, #ff5e62);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
    }

    .container {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
        width: 950px; /* Se hizo más ancho */
        min-height: 500px; /* Se redujo la altura mínima */
        max-height: 650px; /* Para evitar que sea demasiado largo */
        overflow-y: auto; /* Agrega scroll si el contenido es demasiado grande */
        transition: transform 0.3s ease-in-out;
    }

    .container:hover {
        transform: scale(1.02);
    }

    h2 {
        color: #333;
        margin-bottom: 10px;
        font-size: 22px;
        font-weight: 600;
    }

    p {
        font-size: 14px;
        color: #555;
        margin-bottom: 6px;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        display: block;
        margin-top: 8px;
        text-align: left;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin: 6px 0;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: 0.3s;
    }

    input:focus, select:focus {
        border-color: #ff5e62;
        outline: none;
        box-shadow: 0px 0px 8px rgba(255, 94, 98, 0.5);
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        background: #ff5e62;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: 0.3s ease-in-out;
    }

    button:hover {
        background: #e74c3c;
        transform: translateY(-2px);
        box-shadow: 0px 4px 10px rgba(255, 94, 98, 0.5);
    }

    .return-btn {
        display: inline-block;
        margin-top: 12px;
        background: #444;
        color: white;
        padding: 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }

    .return-btn:hover {
        background: #222;
        transform: scale(1.05);
    }
</style>



</head>
<body>
    <div class="container">
        <h2>Bienvenido, <?php echo $empleado['nombre']; ?> 👤</h2>
        <p>ID Empleado: <?php echo $empleado['id']; ?></p>
        <p>Folio: <?php echo $folio; ?></p>

        <form id="pedidoForm" method="POST">
    <input type="hidden" name="id_empleado" value="<?php echo $empleado['id']; ?>">
    <input type="hidden" id="hidden_subtotal" name="subtotal">
    <input type="hidden" id="hidden_iva" name="iva">
    <input type="hidden" id="hidden_total" name="total">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"> <!-- Agregado aquí -->

    <label>Fecha:</label>
    <input type="date" name="fecha" required>

    <label>Mesa:</label>
    <select name="mesa">
        <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>Mesa $i</option>"; ?>
    </select>

    <label>Hora:</label>
    <input type="text" id="hora" readonly>

    <label>Platillo:</label>
    <select id="platillo" name="platillo" onchange="calcular()">
        <option value="" selected disabled>Seleccione un platillo</option>
        <?php while ($row = mysqli_fetch_assoc($platillosResult)) {
            echo "<option value='{$row['id_platillo']}' data-precio='{$row['precio']}'>{$row['nombre']}</option>";
        } ?>
    </select>

    <label>Cantidad:</label>
    <input type="number" id="cantidad" name="cantidad" min="1" value="1" oninput="calcular()">

    <label>Observaciones:</label>
    <input type="text" name="observaciones">

    <label>Precio Unitario:</label>
    <input type="text" id="precio_unitario" readonly>

    <label>Precio Parcial:</label>
    <input type="text" id="precio_parcial" readonly>

    <label>Subtotal:</label>
    <input type="text" id="subtotal" readonly>

    <label>IVA (16%):</label>
    <input type="text" id="iva" readonly>

    <label>Total:</label>
    <input type="text" id="total" readonly>

    <button type="submit" name="vender">Vender</button>
    <button type="reset" onclick="limpiarCampos()">Cancelar</button>
</form>

    </div>

    <script>
        function actualizarHora() {
            let ahora = new Date();
            document.getElementById("hora").value = ahora.toLocaleTimeString();
        }
        setInterval(actualizarHora, 1000);

        function calcular() {
            let platillo = document.getElementById("platillo");
            let cantidad = parseInt(document.getElementById("cantidad").value);

            if (platillo.value === "" || isNaN(cantidad) || cantidad <= 0) {
                return;
            }

            let precioUnitario = parseFloat(platillo.options[platillo.selectedIndex].getAttribute("data-precio"));
            let precioParcial = precioUnitario * cantidad;
            let subtotal = precioParcial;
            let iva = subtotal * 0.16;
            let total = subtotal + iva;

            // Llenar los campos de la interfaz
            document.getElementById("precio_unitario").value = precioUnitario.toFixed(2);
            document.getElementById("precio_parcial").value = precioParcial.toFixed(2);
            document.getElementById("subtotal").value = subtotal.toFixed(2);
            document.getElementById("iva").value = iva.toFixed(2);
            document.getElementById("total").value = total.toFixed(2);

            // Llenar los campos ocultos para enviar los valores correctos
            document.getElementById("hidden_subtotal").value = subtotal.toFixed(2);
            document.getElementById("hidden_iva").value = iva.toFixed(2);
            document.getElementById("hidden_total").value = total.toFixed(2);
        }

        function limpiarCampos() {
            document.getElementById("precio_unitario").value = "";
            document.getElementById("precio_parcial").value = "";
            document.getElementById("subtotal").value = "";
            document.getElementById("iva").value = "";
            document.getElementById("total").value = "";
        }
    </script>
</body>
</html>
