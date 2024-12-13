<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $placaOriginal = $_POST['placa_original']; // Placa original para buscar
    $placaNueva = $_POST['placa'];            // Nueva placa a guardar
    $tipoVehiculo = $_POST['tipo_vehiculo'];  // Nuevo tipo de vehículo

    // Validar campos
    if (empty($placaOriginal) || empty($placaNueva) || empty($tipoVehiculo)) {
        die("Error: Todos los campos son obligatorios.");
    }

    if (!preg_match('/^[A-Z]{3}-\d{3}$/', $placaNueva)) {
        die("Error: La placa debe tener un formato válido (ejemplo: JCX-505).");
    }

    // Actualizar los datos en la base de datos
    $sql = "UPDATE vehiculos 
            SET `Placa` = ?, `Tipo_vehiculo` = ? 
            WHERE `Placa` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $placaNueva, $tipoVehiculo, $placaOriginal);

    if ($stmt->execute()) {
        // Actualizar la sesión con la nueva placa
        $_SESSION['placa'] = $placaNueva;

        header("Location: ../mains/reservas.php");
        exit();
    } else {
        die("Error al actualizar los datos: " . $stmt->error);
    }
} else {
    die("Acceso no autorizado.");
}
?>
