<?php
// Inicia la sesión para acceder a las variables de sesión
session_start();
require '../metods/conexion.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['placa'])) {
    header("Location: ../subs/indexUNLOG.html");
    exit();
}

// Obtiene la placa del vehículo desde la sesión
$placa = $_SESSION['placa'];

// Consulta para obtener el propietario y el rol del vehículo basado en la placa
$sql = "SELECT Propietario, rol FROM vehiculos WHERE Placa = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el propietario para la placa proporcionada.");
}

$vehiculo = $result->fetch_assoc();
$propietario = $vehiculo['Propietario'];
$rol = $vehiculo['rol'];

if ($rol !== 'Operador') {
    header("Location: ../mains/index.php");
    exit();
}

// Consulta para obtener todos los mensajes de la tabla Soporte
$sqlSoporte = "SELECT Correo, Telefono, Propietario, Asunto, Mensaje, Fecha FROM soporte ORDER BY Fecha DESC";
$resultSoporte = $conn->query($sqlSoporte);

$mensajes = [];
if ($resultSoporte->num_rows > 0) {
    while ($fila = $resultSoporte->fetch_assoc()) {
        $mensajes[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="../Assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        .custom-message-box {
            margin-bottom: 20px;
            /* Espacio entre cajas */
            padding: 15px;
            /* Relleno interno */
            background-color: #f8f9fa;
            /* Fondo claro */
            border: 1px solid #ddd;
            /* Borde */
            border-radius: 5px;
            /* Esquinas redondeadas */
            overflow: auto;
            /* Agrega scroll si el contenido excede la caja */
            max-height: 250px;
            /* Altura máxima */
            word-wrap: break-word;
            /* Ajusta palabras largas */
            text-overflow: ellipsis;
            /* Texto largo se corta con puntos */
        }

        .custom-message-box h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .custom-message-box p {
            margin: 5px 0;
        }

        .custom-message-box small {
            color: #6c757d;
        }
    </style>
    <script>
        function atenderCaso(id) {
            if (confirm("¿Está seguro de que desea marcar este caso como atendido y eliminarlo?")) {
                // Enviar solicitud al servidor
                fetch('../metods/eliminar_caso.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar visualmente el mensaje de la lista
                            document.getElementById(`message-${id}`).remove();
                            alert("Caso atendido y eliminado correctamente.");
                        } else {
                            alert("Error al eliminar el caso. Intente de nuevo.");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Hubo un problema con la solicitud.");
                    });
            }
        }
    </script>

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
                        <a href="index_operador.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_5.png" width="23" height="23"> Página principal
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="#" class="custom-navbar-link hover-effect2">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_6.png" width="23" height="23"> Soporte
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="IndexFacturas.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_9.png" width="23" height="23"> Facturas
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="../mains/index.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Pagina de usuarios
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Identificador del usuario y logo -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2">
                    <img class="custom-icon" src="../mains/Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Operador: <?php echo htmlspecialchars($propietario); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="custom-text-container">
            <h2 class="text-center">Mensajes de Soporte</h2>
            <?php if (empty($mensajes)) : ?>
                <div class="alert alert-info">No hay mensajes disponibles.</div>
            <?php else : ?>
                <div class="list-group">
                    <?php foreach ($mensajes as $mensaje) : ?>
                        <div class="list-group-item custom-message-box">
                            <p><strong>Propietario:</strong> <?php echo htmlspecialchars($mensaje['Propietario']); ?></p>
                            <p><strong>Asunto:</strong> <?php echo htmlspecialchars($mensaje['Asunto']); ?></p>
                            <p><strong>Correo:</strong> <?php echo htmlspecialchars($mensaje['Correo']); ?></p>
                            <h5><strong>Teléfono:</strong> <?php echo htmlspecialchars($mensaje['Telefono']); ?></h5>
                            <h5><strong>Mensaje:</strong> <?php echo nl2br(htmlspecialchars($mensaje['Mensaje'])); ?></h5>
                            <h5><small><strong>Fecha:</strong> <?php echo htmlspecialchars($mensaje['Fecha']); ?></small></h5>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>