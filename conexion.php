<?php
// Archivo de conexión a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Usuario de MySQL
$password = ""; // Contraseña de MySQL (cambiar si tienes una configurada)
$database = "restaurante_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>