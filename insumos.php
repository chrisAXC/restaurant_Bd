<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$marcas = ['Coca-Cola', 'Pepsi', 'Nestlé', 'Bimbo', 'Lala', 'Danone', 'Sabritas', 'Herdez', 'La Costeña', 'Gamesa'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aceptar'])) {
    $nombre = $_POST['nombre'];
    $marca = $_POST['marca'];
    $caducidad = $_POST['caducidad'];
    $existencia = $_POST['existencia'];
    $ubicacion = $_POST['ubicacion'];

    $fecha_actual = date("Y-m-d");
    $diferencia = (strtotime($caducidad) - strtotime($fecha_actual)) / (60 * 60 * 24);

    $query = "INSERT INTO insumos (nombre, marca, caducidad, existencia, ubicacion) 
              VALUES ('$nombre', '$marca', '$caducidad', '$existencia', '$ubicacion')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Insumo registrado con éxito');</script>";
        if ($diferencia <= 5) {
            echo "<script>alert('¡Atención! El producto caduca en $diferencia días.');</script>";
        }
        header("Location: menu.php");
        exit();
    } else {
        echo "<script>alert('Error al registrar el insumo: " . mysqli_error($conn) . "');</script>";
    }
}

$insumoEncontrado = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $id_buscar = $_POST['id_buscar'];

    $query = "SELECT * FROM insumos WHERE id = '$id_buscar'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $insumoEncontrado = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('No se encontró el insumo con ID $id_buscar');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insumos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
        }

        input:focus, select:focus {
            border-color: #ff7e5f;
            box-shadow: 0px 0px 5px rgba(255, 126, 95, 0.5);
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #ff7e5f;
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #e86e4c;
            transform: scale(1.05);
        }

        .details {
            margin-top: 20px;
            background: #f8f8f8;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        .details p {
            font-size: 16px;
            margin: 5px 0;
            font-weight: 500;
        }

        .return-btn {
            display: inline-block;
            margin-top: 20px;
            background: #444;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .return-btn:hover {
            background: #222;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Insumos 📦</h2>
        <form method="POST" action="">
            <input type="text" name="nombre" placeholder="Nombre del insumo" required>
            <select name="marca" required>
                <option value="" disabled selected>Selecciona una marca</option>
                <?php foreach ($marcas as $marca) { echo "<option value='$marca'>$marca</option>"; } ?>
            </select>
            <input type="date" name="caducidad" required>
            <input type="number" name="existencia" placeholder="Existencia" required>
            <input type="text" name="ubicacion" placeholder="Ubicación" required>
            <button type="submit" name="aceptar">Registrar Insumo</button>
        </form>

        <h2>Buscar Insumo 🔍</h2>
        <form method="POST" action="">
            <input type="number" name="id_buscar" placeholder="ID del Insumo" required>
            <button type="submit" name="buscar">Buscar</button>
        </form>

        <?php if ($insumoEncontrado): ?>
            <div class="details">
                <h3>Detalles del Insumo</h3>
                <p><strong>ID:</strong> <?php echo $insumoEncontrado['id']; ?></p>
                <p><strong>Nombre:</strong> <?php echo $insumoEncontrado['nombre']; ?></p>
                <p><strong>Marca:</strong> <?php echo $insumoEncontrado['marca']; ?></p>
                <p><strong>Fecha de Caducidad:</strong> <?php echo $insumoEncontrado['caducidad']; ?></p>
                <p><strong>Existencia:</strong> <?php echo $insumoEncontrado['existencia']; ?></p>
                <p><strong>Ubicación:</strong> <?php echo $insumoEncontrado['ubicacion']; ?></p>
            </div>
        <?php endif; ?>

        <br>
        <a href="menu.php" class="return-btn">⬅️ Volver al Menú</a>
    </div>
</body>
</html>
