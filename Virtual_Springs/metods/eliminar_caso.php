<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Validar que el ID sea un número
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit();
    }

    // Consulta para eliminar el mensaje
    $sql = "DELETE FROM Soporte WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
    }

    $stmt->close();
    $conn->close();
}
?>
