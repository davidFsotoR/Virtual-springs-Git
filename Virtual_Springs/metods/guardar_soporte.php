<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "nueva_virtualsp");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$propietario = $_POST['propietario'];
$asunto = $_POST['asunto'];
$mensaje = $_POST['mensaje'];

// Obtener correo y teléfono del propietario desde la base de datos
$sql = "SELECT Correo_propietario, Numero_propietario FROM vehiculos WHERE Propietario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $propietario);
$stmt->execute();
$result = $stmt->get_result();

//rectifica el numero de atributos requeridos//
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); 
    $correo = $user['Correo_propietario'];
    $telefono = $user['Numero_propietario'];

    // Insertar datos en la tabla de soporte
    $sql_insert = "INSERT INTO soporte (Correo, Telefono, Propietario, Asunto, Mensaje) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssss", $correo, $telefono, $propietario, $asunto, $mensaje);

    if ($stmt_insert->execute()) {
        header("Location: ../subs/volverindexsoporte.html");
    } else {
        echo "Error al enviar el soporte: " . $stmt_insert->error;
    }
} else {
    echo "No se encontraron datos del propietario.";
}

// Cerrar conexiones
$stmt->close();
$stmt_insert->close();
$conn->close();
?>
