<?php
session_start();
include 'conexion.php'; // Archivo para la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Verificar si el usuario está inhabilitado en la tabla empleados_historico
    $queryHist = "SELECT * FROM empleados_historico WHERE usuario='$usuario'";
    $resultHist = mysqli_query($conn, $queryHist);

    if (mysqli_num_rows($resultHist) > 0) {
        echo "<script>
                alert('Este usuario está inhabilitado y no puede iniciar sesión.');
                window.location.href = 'login.php'; // Redirige de vuelta al login
              </script>";
        exit(); // Detiene la ejecución para evitar que el usuario entre
    }

    // Buscar en la tabla de administradores
    $query = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $row['rol']; // Guarda el rol del usuario en sesión

        if ($row['rol'] == 'admin') {
            header("Location: menu.php");
        } else {
            header("Location: pedido.php");
        }
        exit();
    }

    // Buscar en la tabla de empleados (Vendedores)
    $query = "SELECT * FROM empleados WHERE usuario='$usuario' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['usuario'] = $usuario;
        $_SESSION['id_empleado'] = $row['id'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['rol'] = 'vendedor';

        header("Location: pedido.php");
        exit();
    }

    // Si no encuentra el usuario en ninguna tabla
    echo "<script>alert('Usuario o contraseña incorrectos');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Restaurante</title>
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

        .login-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 16px;
        }

        input:focus {
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
    <div class="login-container">
        <h2>Bienvenido 🍽️</h2>
        <form method="POST" action="">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
