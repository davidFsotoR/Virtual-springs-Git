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

$_SESSION['rol'] = $rol;

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <nav class="custom-nav navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Botón de colapso que solo aparece en pantallas pequeñas -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar que se colapsa -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="custom-navbar">
                    <li class="custom-navbar-item">
                        <a href="Index.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_5.png" width="23" height="23"> Página
                            principal
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Soporte.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_6.png" width="23" height="23"> Soporte
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Historial.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_7.png" width="23" height="23"> Historial
                        </a>
                    </li>
                    <li class="custom-navbar-item ">
                        <a href="#" class="custom-navbar-link hover-effect2">
                            <img class="custom-icon" src="Assets/imgs/img_9.png" width="23" height="23"> Políticas de
                            servicio
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logo que siempre está visible en el centro -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Usuario: <?php echo htmlspecialchars($vehiculo['Propietario']); ?></span>
                </a>
            </div>
        </div>
    </nav>
        <div class="custom-text-container">
            <h2 class="custom-black-text">1. Introducción</h2>
            Bienvenido a nuestro sistema de reservas de parqueadero. Al realizar una reserva, usted acepta los siguientes términos y condiciones. Por favor, lea detenidamente antes de continuar.

            <h2 class="custom-black-text">2. Proceso de Reserva</h2>
            Las reservas de espacios de parqueadero se realizan exclusivamente a través de nuestro sitio web. Una vez completada la solicitud de reserva, recibirá una confirmación a través del correo electrónico registrado.

            <h2 class="custom-black-text">3. Pagos</h2>
            Nuestro servicio no acepta pagos virtuales. Todos los pagos deben realizarse directamente en el establecimiento antes de utilizar el espacio reservado. Asegúrese de llevar efectivo o cualquier método de pago aceptado por el negocio.

            <h2 class="custom-black-text">4. Cancelaciones y Modificaciones</h2>
            Las cancelaciones de reservas deben realizarse con al menos 24 horas de anticipación llamando al número de contacto indicado en nuestra página. Las modificaciones de horario están sujetas a disponibilidad y deben ser solicitadas con suficiente antelación.

            <h2 class="custom-black-text">5. Responsabilidad del Usuario</h2>
            Usted es responsable de proporcionar información veraz al momento de realizar la reserva. Además, deberá respetar las normas del establecimiento y los horarios establecidos para su reserva.

            <h2 class="custom-black-text">6. Responsabilidad del Parqueadero</h2>
            El parqueadero se compromete a garantizar un espacio reservado, siempre que se respeten las condiciones indicadas. Sin embargo, no nos hacemos responsables por pérdidas o daños a vehículos o pertenencias dejados dentro de los mismos.

            <h2 class="custom-black-text">7. Modificaciones a los Términos</h2>
            Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento. Los cambios se publicarán en nuestro sitio web y entrarán en vigor inmediatamente.

            <h2 class="custom-black-text">8. Contacto</h2>
            Para cualquier consulta o problema relacionado con su reserva, contáctenos a través de nuestro correo electrónico o número de atención al cliente proporcionado en la página.
        </div>
    </div>
</body>

</html>