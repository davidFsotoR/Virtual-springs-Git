<?php
session_start();
require '../metods/conexion.php';

if (!isset($_SESSION['placa'])) {
    header("Location: ../subs/indexUNLOG.html");
    exit();
}

$placa = $_SESSION['placa']; // Obtener la placa del vehículo desde la sesión

// Consultar los datos del vehículo y el propietario
$sql = "SELECT * FROM vehiculos WHERE Placa = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontraron datos para la placa proporcionada.");
}

$vehiculo = $result->fetch_assoc();

$rol = $_SESSION['rol'] ?? 'Usuario';

// Consultar el historial de reservas para esa placa
$sqlReserva = "SELECT * FROM reservas WHERE placa = ? ORDER BY fecha_ingreso DESC";
$stmtReserva = $conn->prepare($sqlReserva);
$stmtReserva->bind_param("s", $placa);
$stmtReserva->execute();
$resultReserva = $stmtReserva->get_result();
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
    <style>
        .custom-text-container {
            width: 50% !important;
            padding: 28px !important;
            text-align: left !important;
            color: #004AAD !important;
            text-shadow: 2px 2px 4px rgb(6, 0, 56) !important;
            margin: 1% !important;
            font-size: 35px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        @media (max-width: 1440px) {
            .custom-text-container {
                width: 64% !important;
                padding: 28px !important;
                text-align: left !important;
            }
        }
    </style>
</head>

<body>
    <!-- Barra de navegación -->
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
                        <a href="index.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_5.png" width="23" height="23"> Página principal
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Soporte.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_6.png" width="23" height="23"> Soporte
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="Historial.php" class="custom-navbar-link hover-effect2">
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

            <!-- Identificador del usuario y logo -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">
                        <?php echo htmlspecialchars($rol) . ": " . htmlspecialchars($vehiculo['Propietario']); ?>
                    </span> 
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="custom-text-container">
            <div class="container">
                <h2 class="text-center">Historial de Reservas</h2>
                <div class="row mt-4">
                    <?php if ($resultReserva->num_rows > 0) {
                        while ($reserva = $resultReserva->fetch_assoc()) {
                            $fecha_salida = empty($reserva['fecha_hora_salida']) ? null : htmlspecialchars($reserva['fecha_hora_salida']);
                    ?>
                            <div class="col-12 mb-4">
                                <div class="card custom-card" style="background-color: black; color: white; border-radius: 10px; border: 2px solid #343fdb;">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo $fecha_salida ? 'Reserva realizada' : 'Reserva activa'; ?>
                                        </h5>
                                        <p class="card-text"><strong>Vehículo:</strong> <?php echo htmlspecialchars($reserva['tipo_vehiculo']); ?></p>
                                        <p class="card-text"><strong>Placa:</strong> <?php echo htmlspecialchars($reserva['placa']); ?></p>
                                        <p class="card-text"><strong>Espacio:</strong> <?php echo htmlspecialchars($reserva['espacio']); ?></p>
                                        <p class="card-text"><strong>Fecha y hora de reserva:</strong> <?php echo htmlspecialchars($reserva['fecha_ingreso']); ?></p>

                                        <?php if ($fecha_salida) { ?>
                                            <p class="card-text"><strong>Fecha de salida:</strong> <?php echo $fecha_salida; ?></p>
                                        <?php } ?>

                                        <?php if (!$fecha_salida) { ?>
                                            <div class="text-center mt-3">
                                                <button class="cancelar-button mt-3" onclick="confirmarEliminacion('<?php echo $reserva['id']; ?>')">Cancelar reserva</button>
                                            </div>
                                        <?php } ?>
                                        <?php if ($fecha_salida) { ?>
                                            <form action='../metods/visualizar_factura.php' method='GET'>
                                                <input type='hidden' name='id_reserva' value='<?php echo $reserva['id']; ?>'>
                                                <button class='custom-button' type='submit'>Visualizar Factura</button>
                                            </form>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                    <?php }
                    } else {
                        echo "<p class='custom-black-text text-center col-12'>No hay reservas para este vehículo.</p>";
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmarEliminacion(reservaId) {
            // Mostrar una alerta de confirmación
            if (confirm('¿Está seguro de que desea eliminar esta reserva?')) {
                // Si el usuario confirma, se ejecutará la solicitud de eliminación
                window.location.href = '../metods/eliminar_reserva.php?id=' + reservaId;
            }
        }
    </script>
</body>

</html>