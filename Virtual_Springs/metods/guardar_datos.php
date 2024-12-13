<?php
require 'conexion.php'; // Asegúrate de que tienes la conexión configurada

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $placa = $_POST['placa'];
    $propietario = $_POST['propietario'];
    $numero_propietario = $_POST['numero_propietario'];
    $correo_propietario = $_POST['correo_propietario'];
    $tipo_vehiculo = $_POST['tipo_vehiculo'];
    $contrasenia = $_POST['contrasenia']; // Recogemos la contraseña

    // El rol siempre será "Usuario"
    $rol = 'Usuario';

    // Validar que los datos no estén vacíos
    if (empty($placa) || empty($propietario) || empty($numero_propietario) || empty($correo_propietario) || empty($tipo_vehiculo) || empty($contrasenia)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Encriptar la contraseña antes de guardarla (por seguridad)
    $contraseniaEncriptada = password_hash($contrasenia, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $sql = "INSERT INTO vehiculos (Placa, Propietario, Numero_propietario, Correo_propietario, Tipo_vehiculo, Contrasenia, Rol)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissss", $placa, $propietario, $numero_propietario, $correo_propietario, $tipo_vehiculo, $contraseniaEncriptada, $rol);

    if ($stmt->execute()) {
        // Redirigir a volverindex.html después de un registro exitoso
        header("Location: ../subs/volverindex.html");
        exit(); // Asegúrate de terminar la ejecución aquí
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    die("Acceso no autorizado.");
}
?>
