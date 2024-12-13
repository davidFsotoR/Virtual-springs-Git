<?php
session_start();
require '../metods/conexion.php';

if (!isset($_SESSION['placa'])) {
    header("Location: ../subs/indexUNLOG.html");
    exit();
}

$placa = $_SESSION['placa'];

// Consultar los datos del vehículo
$sql = "SELECT * FROM vehiculos WHERE Placa = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontraron datos para la placa proporcionada.");
}

$rol = $_SESSION['rol'] ?? 'Usuario';

$vehiculo = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Virtual Sptrings</title>
    <link rel="icon" href="assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="custom-nav navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Botón de colapso que solo aparece en pantallas pequeñas -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar que se colapsa -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="custom-navbar">
                    <li class="custom-navbar-item">
                        <a href="Index.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_5.png" width="23" height="23"> Página principal
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="#" class="custom-navbar-link hover-effect2">
                            <img class="custom-icon" src="Assets/imgs/img_6.png" width="23" height="23"> Soporte
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Historial.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_7.png" width="23" height="23"> Historial
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Politicas.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_9.png" width="23" height="23"> Políticas de servicio
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logo que siempre está visible en el centro -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link ms-2"> <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">
                        <?php echo htmlspecialchars($rol) . ": " . htmlspecialchars($vehiculo['Propietario']); ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content2">
        <div class="custom-text-container">
            <h1 class=" text-center">Soporte</h1>
            <h3 class="custom-black-text"> Si tienes alguna peticion, queja, pregunta o sugerencia no dudes en comunicarte con nosotros por este medio</h3>
            <form method="POST" action="../metods/guardar_soporte.php">
                <div class="custom-black-text custom-box2">
                    <label for="nombre">Enviando a nombre de</label>
                    <input type="text" class="form-control" name="propietario" value="<?php echo htmlspecialchars($vehiculo['Propietario']); ?>" readonly>
                </div>
                <div class="custom-black-text custom-box2">
                    <label for="asunto">Asunto</label>
                    <input type="text" class="form-control" name="asunto" id="asunto" placeholder="Asunto" required>
                </div>
                <div class="custom-black-text custom-box2">
                    <label for="mensaje">Mensaje</label>
                    <textarea class="form-control" name="mensaje" id="mensaje" rows="4" placeholder="Escribe tu mensaje aquí" required></textarea>
                </div>
                <div class="custom-button-container">
                    <button type="submit" class="custom-button">Enviar</button>
                </div>
            </form>

        </div>
    </div>

</body>

</html>