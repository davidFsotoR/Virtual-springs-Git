
<?php
require '../metods/conexion.php'; // Conexión a la base de datos
date_default_timezone_set('America/Bogota'); // Cambia a tu zona horaria correspondiente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $placa = $_POST['placa'] ?? '';
    $tipo_vehiculo = $_POST['tipo_vehiculo'] ?? '';
    $espacio = $_POST['espacio'] ?? '';
    $fecha_ingreso = date('Y-m-d H:i:s'); // Fecha actual

    // Validar que los campos no estén vacíos
    if (empty($placa) || empty($tipo_vehiculo) || empty($espacio)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Verificar si la placa ya existe en la tabla 'vehiculos'
        $sqlCheckVehiculo = "SELECT COUNT(*) as existe FROM vehiculos WHERE Placa = ?";
        $stmtCheck = $conn->prepare($sqlCheckVehiculo);
        if (!$stmtCheck) {
            throw new Exception("Error al preparar la verificación de placa: " . $conn->error);
        }
        $stmtCheck->bind_param('s', $placa);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $rowCheck = $resultCheck->fetch_assoc();
        $existeVehiculo = $rowCheck['existe'] > 0;

        // Si no existe, insertar en 'vehiculos'
        if (!$existeVehiculo) {
            $sqlInsertVehiculo = "INSERT INTO vehiculos (Placa, Tipo_vehiculo) VALUES (?, ?)";
            $stmtInsertVehiculo = $conn->prepare($sqlInsertVehiculo);
            if (!$stmtInsertVehiculo) {
                throw new Exception("Error al preparar la inserción en vehiculos: " . $conn->error);
            }
            $stmtInsertVehiculo->bind_param('ss', $placa, $tipo_vehiculo);
            if (!$stmtInsertVehiculo->execute()) {
                throw new Exception("Error al ejecutar la inserción en vehiculos: " . $stmtInsertVehiculo->error);
            }
        }

        // Insertar los datos en la tabla 'Reservas'
        $sqlInsertReserva = "INSERT INTO reservas (placa, tipo_vehiculo, espacio, fecha_ingreso) 
                             VALUES (?, ?, ?, ?)";
        $stmtInsertReserva = $conn->prepare($sqlInsertReserva);
        if (!$stmtInsertReserva) {
            throw new Exception("Error al preparar la inserción en Reservas: " . $conn->error);
        }
        $stmtInsertReserva->bind_param('ssss', $placa, $tipo_vehiculo, $espacio, $fecha_ingreso);
        if (!$stmtInsertReserva->execute()) {
            throw new Exception("Error al ejecutar la inserción en Reservas: " . $stmtInsertReserva->error);
        }

        // Actualizar la disponibilidad del espacio
        $sqlUpdateEspacio = "UPDATE espacios SET Disponibilidad = 'Ocupado' WHERE ubicacion = ?";
        $stmtUpdateEspacio = $conn->prepare($sqlUpdateEspacio);
        if (!$stmtUpdateEspacio) {
            throw new Exception("Error al preparar la actualización de Espacios: " . $conn->error);
        }
        $stmtUpdateEspacio->bind_param('s', $espacio);
        if (!$stmtUpdateEspacio->execute()) {
            throw new Exception("Error al ejecutar la actualización de Espacios: " . $stmtUpdateEspacio->error);
        }

        if ($stmtUpdateEspacio->affected_rows === 0) {
            throw new Exception("No se encontró el espacio para actualizar en Espacios.");
        }

        // Confirmar la transacción
        $conn->commit();
        echo "<script>alert('Vehiculo registrado con exito!'); window.history.back();</script>";
        // Opcional: Redirigir a una página de éxito
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error al procesar la solicitud: " . $e->getMessage();
    }

    // Cerrar las declaraciones y conexión
    if (isset($stmtCheck)) $stmtCheck->close();
    if (isset($stmtInsertVehiculo)) $stmtInsertVehiculo->close();
    if (isset($stmtInsertReserva)) $stmtInsertReserva->close();
    if (isset($stmtUpdateEspacio)) $stmtUpdateEspacio->close();
    $conn->close();
} else {
    echo "Método no permitido.";
}
?>
