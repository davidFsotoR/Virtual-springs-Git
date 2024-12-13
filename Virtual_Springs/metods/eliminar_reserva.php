<?php
require 'conexion.php';

// Obtener el ID de la reserva a eliminar
$reserva_id = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($reserva_id)) {
    // Eliminar la reserva
    $sqlEliminar = "DELETE FROM reservas WHERE id = ?";
    $stmtEliminar = $conn->prepare($sqlEliminar);
    $stmtEliminar->bind_param('i', $reserva_id);

    if ($stmtEliminar->execute()) {
        echo "<script>alert('Reserva eliminada exitosamente.'); window.location.href = '../mains/Historial.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la reserva.'); window.location.href = '../mains/Historial.php';</script>";
    }

    $stmtEliminar->close();
}

$conn->close();
?>
