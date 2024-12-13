<?php
// Incluir el archivo de conexión
require 'conexion.php'; // Asegúrate de que la ruta sea correcta

// Validar datos del formulario
if (!isset($_POST['placa'], $_POST['vehiculo'], $_POST['fecha_ingreso'], $_POST['espacio'])) {
    echo "<script>alert('Error: Todos los campos son obligatorios.'); window.history.back();</script>";
    exit;
}

$placa = $_POST['placa'];
$tipo_vehiculo = $_POST['vehiculo'];
$fecha_ingreso = $_POST['fecha_ingreso'];
$ubicacion = $_POST['espacio'];

// Calcular el lapso de tiempo hasta (2 horas después del ingreso)
$lapso_hasta = date('Y-m-d H:i:s', strtotime($fecha_ingreso . ' +2 hours'));

// Consultar el tipo de vehículo permitido para el espacio seleccionado
$queryEspacio = "SELECT tipo_vehiculo FROM espacios WHERE ubicacion = ?";
$stmtEspacio = $conn->prepare($queryEspacio);

if ($stmtEspacio) {
    $stmtEspacio->bind_param("s", $ubicacion);
    $stmtEspacio->execute();
    $stmtEspacio->bind_result($tipoPermitido);
    $stmtEspacio->fetch();
    $stmtEspacio->close();

    // Comprobar si el espacio es adecuado para el tipo de vehículo
    if (strcasecmp($tipoPermitido, $tipo_vehiculo) !== 0) {
        $alertMessage = "Este es un espacio para $tipoPermitido. Por favor, selecciona un espacio adecuado para tu vehículo.";
        echo "<script>alert('$alertMessage'); window.history.back();</script>";
        exit;
    }
} else {
    echo "Error al preparar la consulta: " . $conn->error;
    exit;
}

// Validar conflictos de reserva en el mismo espacio y lapso
$queryConflicto = "SELECT COUNT(*) 
                   FROM reservas 
                   WHERE espacio = ? 
                     AND (
                         (fecha_ingreso <= ? AND lapso_hasta > ?) OR 
                         (fecha_ingreso < ? AND lapso_hasta >= ?) OR 
                         (? <= fecha_ingreso AND ? > fecha_ingreso)
                     )";
$stmtConflicto = $conn->prepare($queryConflicto);

if ($stmtConflicto) {
    $stmtConflicto->bind_param("sssssss", $ubicacion, $fecha_ingreso, $fecha_ingreso, $lapso_hasta, $lapso_hasta, $fecha_ingreso, $lapso_hasta);
    $stmtConflicto->execute();
    $stmtConflicto->bind_result($conflicto);
    $stmtConflicto->fetch();
    $stmtConflicto->close();

    if ($conflicto > 0) {
        echo "<script>alert('Error: Ya existe una reserva en conflicto para este espacio y rango de tiempo, por favor intentelo nuevamente con otro espacio.'); window.history.back();</script>";
        exit;
    }
} else {
    echo "Error al preparar la consulta de conflicto: " . $conn->error;
    exit;
}

// Insertar la reserva si todo es válido
$queryReserva = "INSERT INTO reservas (placa, fecha_ingreso, espacio, tipo_vehiculo, lapso_hasta) VALUES (?, ?, ?, ?, ?)";
$stmtReserva = $conn->prepare($queryReserva);

if ($stmtReserva) {
    $stmtReserva->bind_param("sssss", $placa, $fecha_ingreso, $ubicacion, $tipo_vehiculo, $lapso_hasta);

    if ($stmtReserva->execute()) {
        // Redirigir al usuario al dashboard
        header("Location: ../subs/vIndexreservas.html");
    } else {
        echo "Error al realizar la reserva: " . $stmtReserva->error;
    }
} else {
    echo "Error al preparar la consulta de reserva: " . $conn->error;
}

$conn->close();
