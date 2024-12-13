<?php
// Conectamos a la base de datos
$conn = new mysqli("localhost", "senatpsc_Soto", "JuliSoto13", "senatpsc_nueva_virtualsp");

// Verificamos la conexión
if ($conn->connect_error) {
    // Si la conexión falla
    die("Connection failed: " . $conn->connect_error);
}

// Verificamos si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Realizamos una consulta para verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM vehiculos WHERE Correo_propietario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Si no se encuentra el correo
    if ($stmt->num_rows == 0) {
        // Redirigimos con un mensaje de error
        header("Location: ../subs/recuperar.php?error=El correo no está registrado en nuestra base de datos.");
        exit;
    }

    // Si el correo existe, generamos un token para la recuperación
    $token = bin2hex(random_bytes(50)); // Generamos un token aleatorio

    // Actualizamos el registro con el token
    $stmt->close();
    $sql = "UPDATE vehiculos SET token_recuperacion = ? WHERE Correo_propietario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    // Enviamos el correo de recuperación
    $to = $email;
    $subject = "Recuperación de Contraseña";
    $message = "Haga clic en el siguiente enlace para recuperar su contraseña: \n\n";
    $message .= "http://virtualsprings.senatps.club/metods/reestablecer.php?token=" . $token;
    $headers = "From: no-reply@virtualsprings.senatps.club";

    if (mail($to, $subject, $message, $headers)) {
        // Si el correo es enviado, redirigimos a la página de éxito sin alerta
        header("Location: ../subs/correo_enviado.html");
        exit;
    } else {
        // Si no se puede enviar el correo
        header("Location: ../subs/recuperar.php?error=Hubo un problema al enviar el correo. Intenta de nuevo.");
        exit;
    }
}

$conn->close();
?>

