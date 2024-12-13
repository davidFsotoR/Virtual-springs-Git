<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $nueva_password = password_hash($_POST['nueva_password'], PASSWORD_DEFAULT); // Cifrar la nueva contraseña

    // Conectar a la base de datos
    $conn = new mysqli("localhost", "senatpsc_Soto", "JuliSoto13", "senatpsc_nueva_virtualsp");

    if ($conn->connect_error) {
        echo "<script>alert('Error en la conexión con la base de datos. Inténtelo nuevamente.');</script>";
        exit;
    }

    // Actualizar la contraseña
    $sql = "UPDATE vehiculos SET contrasenia = ?, token_recuperacion = NULL WHERE token_recuperacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nueva_password, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>
            alert('Contraseña actualizada con éxito.');
            window.location.href = 'https://virtualsprings.senatps.club/subs/indexUNLOG.html';
            </script>";
    } else {
        echo "<script>alert('Hubo un error al actualizar la contraseña. Por favor, inténtelo nuevamente.');</script>";
    }

    $conn->close();
}
?>
