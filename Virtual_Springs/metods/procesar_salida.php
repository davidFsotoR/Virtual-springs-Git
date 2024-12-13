<?php
include '../metods/conexion.php';
date_default_timezone_set('America/Bogota'); // Cambia a tu zona horaria correspondiente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // ID de la reserva
    $espacio = $_POST['espacio']; // Espacio asociado a la reserva

    // Establecer la fecha y hora actual para la salida
    $fechaSalida = date("Y-m-d H:i:s");

    // Iniciar una transacción para garantizar consistencia
    $conn->begin_transaction();

    try {
        // 1. Actualizar la fecha de salida en la reserva
        $queryReserva = "UPDATE reservas SET fecha_hora_salida = ? WHERE id = ?";
        $stmtReserva = $conn->prepare($queryReserva);
        $stmtReserva->bind_param("si", $fechaSalida, $id);

        if (!$stmtReserva->execute()) {
            throw new Exception("Error al actualizar la fecha de salida: " . $stmtReserva->error);
        }

        // 2. Obtener los datos de la reserva para generar la factura
        $queryDatos = "SELECT placa, tipo_vehiculo, fecha_ingreso, espacio FROM reservas WHERE id = ?";
        $stmtDatos = $conn->prepare($queryDatos);
        $stmtDatos->bind_param("i", $id);
        $stmtDatos->execute();
        $result = $stmtDatos->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("No se encontró la reserva para generar la factura.");
        }

        $row = $result->fetch_assoc();
        $placa = $row['placa'];
        $tipoVehiculo = $row['tipo_vehiculo'];
        $fechaIngreso = $row['fecha_ingreso'];
        $espacioFactura = $row['espacio'];

        // 3. Obtener la tarifa por hora basada en el tipo de vehículo
        $queryTarifa = "SELECT Tarifa_por_hora FROM tarifas WHERE Tipo_vehiculo = ?";
        $stmtTarifa = $conn->prepare($queryTarifa);
        $stmtTarifa->bind_param("s", $tipoVehiculo);
        $stmtTarifa->execute();
        $resultTarifa = $stmtTarifa->get_result();

        if ($resultTarifa->num_rows === 0) {
            throw new Exception("No se encontró la tarifa para el tipo de vehículo.");
        }

        $tarifaPorHora = $resultTarifa->fetch_assoc()['Tarifa_por_hora'];

        // 4. Calcular el tiempo en minutos y el total
        $fechaIngresoTimestamp = strtotime($fechaIngreso);
        $fechaSalidaTimestamp = strtotime($fechaSalida);
        $tiempoEnMinutos = ($fechaSalidaTimestamp - $fechaIngresoTimestamp) / 60;
        $total = ($tiempoEnMinutos / 60) * $tarifaPorHora;

        // 5. Generar la factura con el total calculado
        $queryFactura = "INSERT INTO facturas (Espacio, Fecha_ingreso, Fecha_salida, Placa, Tipo_vehiculo, Total, id_reserva) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtFactura = $conn->prepare($queryFactura);
        $stmtFactura->bind_param("ssssssi", $espacioFactura, $fechaIngreso, $fechaSalida, $placa, $tipoVehiculo, $total, $id);

        if (!$stmtFactura->execute()) {
            throw new Exception("Error al generar la factura: " . $stmtFactura->error);
        }

        // 6. Actualizar la disponibilidad del espacio si el estado es presencial
        $queryEstado = "SELECT estado FROM espacios WHERE ubicacion = ?";
        $stmtEstado = $conn->prepare($queryEstado);
        $stmtEstado->bind_param("s", $espacioFactura);
        $stmtEstado->execute();
        $resultEstado = $stmtEstado->get_result();

        if ($resultEstado->num_rows === 0) {
            throw new Exception("No se encontró el espacio para actualizar.");
        }

        $estado = $resultEstado->fetch_assoc()['Estado'];

        // Si el estado es "presencial", actualizar la disponibilidad a "Libre"
        if ($estado === 'Presencial') {
            $queryActualizarEspacio = "UPDATE espacios SET Disponibilidad = 'Libre' WHERE ubicacion = ?";
            $stmtActualizarEspacio = $conn->prepare($queryActualizarEspacio);
            $stmtActualizarEspacio->bind_param("s", $espacioFactura);

            if (!$stmtActualizarEspacio->execute()) {
                throw new Exception("Error al actualizar la disponibilidad del espacio: " . $stmtActualizarEspacio->error);
            }
        }

        // Confirmar la transacción
        $conn->commit();

        // Redirigir al listado con mensaje de éxito
        header("Location: ../operators/indexReservas.php?success=true");
        exit;
    } catch (Exception $e) {
        // Revertir los cambios si ocurre algún error
        $conn->rollback();
        // Redirigir al listado con mensaje de error
        header("Location: ../operators/indexReservas.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

$conn->close();
?>
