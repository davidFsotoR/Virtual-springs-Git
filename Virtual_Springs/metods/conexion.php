<?php
$servername = "localhost";
$username = "senatpsc_Soto";  // Cambia esto según tu configuración
$password = "JuliSoto13";      // Cambia esto si tienes una contraseña
$dbname = "senatpsc_nueva_virtualsp";  // nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
