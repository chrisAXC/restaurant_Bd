<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
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
        }

        .menu-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 15px 0;
        }

        ul li a {
            text-decoration: none;
            background: #ff7e5f;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            display: block;
            font-size: 18px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        ul li a:hover {
            background: #e86e4c;
            transform: scale(1.05);
        }

        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            background: #444;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .logout-btn:hover {
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
    <div class="menu-container">
        <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?> 👋</h2>
        <ul>
            <li><a href="empleados.php">👨‍💼 Empleados</a></li>
            <li><a href="insumos.php">📦 Insumos</a></li>
            <li><a href="ventas.php">💰 Ventas</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">🚪 Cerrar Sesión</a>

    </div>
</body>
</html>
