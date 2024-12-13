<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $placaOriginal = $_POST['placa_original']; // Placa original para buscar
    $placaNueva = $_POST['placa'];            // Nueva placa a guardar
    $propietario = $_POST['propietario'];
    $numeroPropietario = $_POST['numero_propietario'];
    $correoPropietario = $_POST['correo_propietario'];
    $tipoVehiculo = $_POST['tipo_vehiculo'];

    // Validar campos
    if (empty($placaOriginal) || empty($placaNueva) || empty($propietario) || empty($numeroPropietario) || empty($correoPropietario) || empty($tipoVehiculo)) {
        die("Error: Todos los campos son obligatorios.");
    }

    if (!preg_match('/^[A-Z]{3}-\d{3}$/', $placaNueva)) {
        die("Error: La placa debe tener un formato válido (ejemplo: JCX-505).");
    }

    // Actualizar los datos en la base de datos
    $sql = "UPDATE vehiculos 
            SET Placa = ?, Propietario = ?, Numero_propietario = ?, Correo_propietario = ?, Tipo_vehiculo = ? 
            WHERE Placa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $placaNueva, $propietario, $numeroPropietario, $correoPropietario, $tipoVehiculo, $placaOriginal);

    if ($stmt->execute()) {
        // Actualizar la sesión con la nueva placa
        $_SESSION['placa'] = $placaNueva;

        // Redirigir al dashboard
        header("Location: ../subs/datoseditados.html");
        exit();
    } else {
        die("Error al actualizar los datos: " . $stmt->error);
    }
} else {
    die("Acceso no autorizado.");
}
?>

