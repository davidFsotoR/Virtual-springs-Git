<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $placa = $_POST['placa'];
    $tipoVehiculo = $_POST['tipo_vehiculo'];
    $espacio = $_POST['espacio'];
    $fechaIngreso = $_POST['fecha_ingreso'];
    $fechaSalida = !empty($_POST['fecha_hora_salida']) ? $_POST['fecha_hora_salida'] : null;

    $query = "UPDATE reservas SET placa = ?, tipo_vehiculo = ?, espacio = ?, fecha_ingreso = ?, fecha_hora_salida = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $placa, $tipoVehiculo, $espacio, $fechaIngreso, $fechaSalida, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Reserva actualizada exitosamente.'); window.location.href = '../admin/reservas_admin.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la reserva.'); window.location.href = '../admin/editar_reserva.php?id=$id';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
