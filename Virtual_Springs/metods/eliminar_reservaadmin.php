<?php
require 'conexion.php';

// Verificar que el método de la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = isset($_POST['id']) ? $_POST['id'] : '';

    // Validar que el ID sea un entero válido
    if (!filter_var($reserva_id, FILTER_VALIDATE_INT)) {
        echo "<script>alert('ID de reserva inválido.'); window.location.href = '../mains/Historial.php';</script>";
        exit();
    }

    // Eliminar la reserva
    $sqlEliminar = "DELETE FROM reservas WHERE id = ?";
    $stmtEliminar = $conn->prepare($sqlEliminar);
    $stmtEliminar->bind_param('i', $reserva_id);

    if ($stmtEliminar->execute()) {
        echo "<script>alert('Reserva eliminada exitosamente.'); window.location.href = '../mains/Historial.php';</script>";
    } else {
        // Registrar el error en el log
        error_log("Error al eliminar la reserva con ID $reserva_id: " . $conn->error);
        echo "<script>alert('No se pudo eliminar la reserva. Por favor, inténtalo nuevamente.'); window.location.href = '../mains/Historial.php';</script>";
    }

    $stmtEliminar->close();
    $conn->close();
} else {
    echo "<script>alert('Método de solicitud no permitido.'); window.location.href = '../mains/Historial.php';</script>";
    exit();
}
?>
