<?php
// Código PHP para procesar recuperación de contraseña (opcional, dependiendo de tu lógica)
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Conectar a la base de datos
    $conn = new mysqli("localhost", "senatpsc_Soto", "JuliSoto13", "senatpsc_nueva_virtualsp");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Verificar si el token es válido
    $sql = "SELECT placa FROM vehiculos WHERE token_recuperacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token válido, mostrar formulario de cambio de contraseña
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <title>Recuperar Contraseña</title>
    <link rel="icon" href="../mains/assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="../mains/iniciarsesion.php" class="custom-navbar-link hover-effect">
                        <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-logo">
                <img src="../mains/Assets/imgs/img_3.png">
            </div>
        </div>
    </nav>

    <form class="custom-box" action="actualizar_contrasenia.php" method="POST">
                <input type="hidden" name="token" value="' . $token . '">
                <h1>Escribe una nueva contraseña</h1>
                <input type="password" name="nueva_password" required>
                <input type="submit" value="Restablecer Contraseña">
              </form>
</body>
</html>';
    } else {
        echo "Token inválido o expirado.";
    }

    $conn->close();
}
?>

