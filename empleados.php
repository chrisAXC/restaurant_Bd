<?php
session_start();
include 'conexion.php';

// Generar clave y contraseña sin encriptar
$clave_generada = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
$password_generada = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aceptar'])) {
    $clave = $clave_generada;
    $nombre = trim($_POST['nombre']);
    $curp = $_POST['curp'];
    $rfc = $_POST['rfc'];
    $fecha_alta = $_POST['fecha_alta'];
    $puesto = $_POST['puesto'];
    $turno = $_POST['turno'];
    $sueldo = $_POST['sueldo'];
    $sexo = $_POST['sexo'];

    // Generar usuario
    $usuario_base = strtolower(str_replace(" ", "", $nombre));
    $usuario = $usuario_base;
    $contador = 1;

    // Verificar si el usuario ya existe
    while (true) {
        $queryCheck = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultCheck = mysqli_query($conn, $queryCheck);
        if (mysqli_num_rows($resultCheck) == 0) {
            break;
        }
        $usuario = $usuario_base . $contador;
        $contador++;
    }

    // Insertar en empleados
    $query = "INSERT INTO empleados (clave, nombre, curp, rfc, fecha_alta, puesto, turno, sueldo, sexo, usuario, password) 
              VALUES ('$clave', '$nombre', '$curp', '$rfc', '$fecha_alta', '$puesto', '$turno', '$sueldo', '$sexo', '$usuario', '$password_generada')";

    if (mysqli_query($conn, $query)) {
        // Insertar en la tabla de usuarios
        $queryUsuario = "INSERT INTO usuarios (usuario, password, rol) VALUES ('$usuario', '$password_generada', 'vendedor')";
        if (mysqli_query($conn, $queryUsuario)) {
            echo "<script>alert('Empleado registrado con éxito. Usuario: $usuario | Contraseña: $password_generada');</script>";
        } else {
            echo "<script>alert('Error al crear el usuario en la tabla usuarios: " . mysqli_error($conn) . "');</script>";
        }
        header("Location: empleados.php");
        exit();
    } else {
        echo "<script>alert('Error al registrar el empleado en la tabla empleados: " . mysqli_error($conn) . "');</script>";
    }
}

// Variable para evitar errores de variable no definida
$empleadoEncontrado = null;

// Buscar empleado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $id_buscar = $_POST['id_buscar'];

    // Primero, verificamos si el empleado está en la tabla histórica
    $queryHist = "SELECT * FROM empleados_historico WHERE id = '$id_buscar'";
    $resultHist = mysqli_query($conn, $queryHist);
    
    if (mysqli_num_rows($resultHist) > 0) {
        echo "<script>alert('El usuario está inhabilitado.');</script>";
    } else {
        $query = "SELECT * FROM empleados WHERE id = '$id_buscar'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $empleadoEncontrado = mysqli_fetch_assoc($result);
        } else {
            echo "<script>alert('No se encontró el empleado con ID $id_buscar');</script>";
        }
    }
}

// Inhabilitar empleado (mover a tabla histórica)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inhabilitar'])) {
    $id_inhabilitar = $_POST['id_buscar'];

    // Verificar si el empleado existe antes de moverlo
    $queryEmpleado = "SELECT * FROM empleados WHERE id = '$id_inhabilitar'";
    $resultEmpleado = mysqli_query($conn, $queryEmpleado);
    
    if (mysqli_num_rows($resultEmpleado) > 0) {
        $empleado = mysqli_fetch_assoc($resultEmpleado);

        // Insertar en tabla histórica
        $queryHist = "INSERT INTO empleados_historico (clave, nombre, curp, rfc, fecha_alta, puesto, turno, sueldo, sexo, usuario, password) 
                      VALUES ('{$empleado['clave']}', '{$empleado['nombre']}', '{$empleado['curp']}', '{$empleado['rfc']}', 
                      '{$empleado['fecha_alta']}', '{$empleado['puesto']}', '{$empleado['turno']}', '{$empleado['sueldo']}', 
                      '{$empleado['sexo']}', '{$empleado['usuario']}', '{$empleado['password']}')";

        if (mysqli_query($conn, $queryHist)) {
            // Eliminar de la tabla original
            $queryDel = "DELETE FROM empleados WHERE id = '$id_inhabilitar'";
            mysqli_query($conn, $queryDel);
            echo "<script>alert('Empleado inhabilitado y movido a historial.');</script>";
        } else {
            echo "<script>alert('Error al mover a la tabla histórica: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('No se encontró el empleado para inhabilitar.');</script>";
    }
}

// Habilitar empleado (sacarlo de la tabla histórica y devolverlo a empleados)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['habilitar'])) {
    $id_habilitar = $_POST['id_buscar'];

    // Verificar si el empleado está en la tabla histórica
    $queryHist = "SELECT * FROM empleados_historico WHERE id = '$id_habilitar'";
    $resultHist = mysqli_query($conn, $queryHist);

    if (mysqli_num_rows($resultHist) > 0) {
        $empleado = mysqli_fetch_assoc($resultHist);

        // Insertar de nuevo en empleados
        $queryEmpleado = "INSERT INTO empleados (clave, nombre, curp, rfc, fecha_alta, puesto, turno, sueldo, sexo, usuario, password) 
                          VALUES ('{$empleado['clave']}', '{$empleado['nombre']}', '{$empleado['curp']}', '{$empleado['rfc']}', 
                          '{$empleado['fecha_alta']}', '{$empleado['puesto']}', '{$empleado['turno']}', '{$empleado['sueldo']}', 
                          '{$empleado['sexo']}', '{$empleado['usuario']}', '{$empleado['password']}')";

        if (mysqli_query($conn, $queryEmpleado)) {
            // Eliminar de la tabla histórica
            $queryDel = "DELETE FROM empleados_historico WHERE id = '$id_habilitar'";
            mysqli_query($conn, $queryDel);
            echo "<script>alert('Empleado habilitado correctamente y regresado a la tabla de empleados.');</script>";
        } else {
            echo "<script>alert('Error al habilitar el empleado: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('No se encontró el empleado en la tabla histórica.');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
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
    display: flex;
    justify-content: center;
    align-items: flex-start;  /* Alinea los items al inicio */
    height: 100vh;
    padding: 10px; /* Agrega padding a la pantalla para evitar que el contenido toque los bordes */
    text-align: center;
    overflow-y: auto; /* Para permitir el desplazamiento en pantallas pequeñas */
}

.container {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 350px;  /* Limita el tamaño máximo del formulario */
    transition: transform 0.3s;
    margin-top: 20px;  /* Separación del formulario desde arriba */
    overflow: hidden;  /* Evita que los elementos se desborden */
    box-sizing: border-box;
}

.container:hover {
    transform: scale(1.02);
}

h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: 600;
}

input, select, button {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 13px;
    transition: 0.3s;
}

input:focus, select:focus {
    border-color: #ff7e5f;
    outline: none;
    box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.5);
}

button {
    background: #e86e4c;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    border: none;
}

button:hover {
    background: #e86e4c;
    transform: translateY(-2px);
    box-shadow: 0px 4px 10px rgba(0, 91, 187, 0.5);
}

.return-btn {
    display: inline-block;
    margin-top: 20px;
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

.result {
    background: #f8f8f8;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
    margin-top: 12px;
    text-align: left;
}

.result p {
    margin: 3px 0;
    font-size: 13px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;  /* Espacio entre los inputs */
}

       .form-group input,
       .form-group select {
           flex: 1;
       }
    </style>
</head>

<body>
    <div class="container">
        <h2>Registro de Empleados 👨‍💼</h2>

        <form method="POST" action="">
            <input type="text" name="clave" value="<?php echo isset($clave_generada) ? $clave_generada : ''; ?>" readonly placeholder="Clave Generada">
            
            <input type="text" name="nombre" placeholder="Nombre del Empleado" required>

            <div class="form-group">
                <input type="text" name="curp" placeholder="CURP" required>
                <input type="text" name="rfc" placeholder="RFC" required>
            </div>

            <label style="font-size: 14px; color: #666;">Fecha de Alta:</label>
            <input type="date" name="fecha_alta" required>

            <div class="form-group">
                <input type="text" name="puesto" placeholder="Puesto" required>
                <select name="turno" required>
                    <option value="" disabled selected>Turno</option>
                    <option value="Matutino">Matutino</option>
                    <option value="Vespertino">Vespertino</option>
                    <option value="Nocturno">Nocturno</option>
                </select>
            </div>

            <div class="form-group">
                <input type="number" name="sueldo" placeholder="Sueldo" required>
                <select name="sexo" required>
                    <option value="" disabled selected>Sexo</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                </select>
            </div>

            <button type="submit" name="aceptar">Registrar Empleado</button>
        </form>

        <form method="POST" action="">
            <label>Buscar Empleado por ID:</label>
            <input type="text" name="id_buscar" required>
            <button type="submit" name="buscar">Buscar</button>
        </form>

        <!-- Mostrar datos del empleado encontrado -->
        <?php if ($empleadoEncontrado): ?>
            <div class="result">
                <h3>Detalles del Empleado</h3>
                <p><strong>ID:</strong> <?php echo $empleadoEncontrado['id']; ?></p>
                <p><strong>Nombre:</strong> <?php echo $empleadoEncontrado['nombre']; ?></p>
                <p><strong>CURP:</strong> <?php echo $empleadoEncontrado['curp']; ?></p>
                <p><strong>RFC:</strong> <?php echo $empleadoEncontrado['rfc']; ?></p>
                <p><strong>Fecha Alta:</strong> <?php echo $empleadoEncontrado['fecha_alta']; ?></p>
                <p><strong>Puesto:</strong> <?php echo $empleadoEncontrado['puesto']; ?></p>
                <p><strong>Turno:</strong> <?php echo $empleadoEncontrado['turno']; ?></p>
                <p><strong>Sueldo:</strong> <?php echo $empleadoEncontrado['sueldo']; ?></p>
                <p><strong>Sexo:</strong> <?php echo $empleadoEncontrado['sexo']; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Eliminar Empleado por ID:</label>
            <input type="text" name="id_buscar" required>
            <button type="submit" name="eliminar" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">Eliminar</button>
        </form>
        <form method="POST" action="">
    <label>Habilitar/Inhabilitar Empleado por ID:</label>
    <input type="text" name="id_buscar" required>
    <button type="submit" name="inhabilitar" onclick="return confirm('¿Seguro que quieres inhabilitar este empleado?')">Inhabilitar</button>
    <button type="submit" name="habilitar" onclick="return confirm('¿Seguro que quieres habilitar este empleado?')">Habilitar</button>
</form>
        <br>
        <a href="menu.php" class="return-btn">⬅️ Volver al Menú</a>
    </div>
</body>
</html>