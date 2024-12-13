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
$stmt = $conn->prepare($sql); // Prepara la consulta
$stmt->bind_param("s", $placa); // Vincula la placa como parámetro
$stmt->execute(); // Ejecuta la consulta
$result = $stmt->get_result(); // Obtiene los resultados

// Si no se encuentra el propietario o el rol, redirige o muestra un error
if ($result->num_rows === 0) {
    die("Error: No se encontró el propietario para la placa proporcionada.");
}

// Obtiene los datos del vehículo
$vehiculo = $result->fetch_assoc();
$propietario = $vehiculo['Propietario'];
$rol = $vehiculo['rol']; // Obtiene el rol del usuario

// Verifica si el rol es 'Operador'. Si no es, redirige al index de usuarios
if ($rol !== 'Operador') {
    header("Location: ../mains/index.php");
    exit();
}
?>

<script>
    function confirmLogout(event, logoutUrl) {
        // Evitar la acción predeterminada del enlace
        event.preventDefault();

        // Mostrar una ventana de confirmación
        const confirmAction = confirm("¿Estás seguro de que deseas cerrar sesión?");

        if (confirmAction) {
            // Si el usuario confirma, redirige a logout.php
            window.location.href = logoutUrl;
        }
        // Si no confirma, no se hace nada (el enlace no se sigue)
    }
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .custom-text-container {
            width: 50% !important;
            padding: 20px !important;
            text-align: left !important;
            color: #004AAD !important;
            text-shadow: 2px 2px 4px rgb(6, 0, 56) !important;
            margin: 1% !important;
            font-size: 35px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
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
                        <a href="#" class="custom-navbar-link hover-effect2">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_5.png" width="23" height="23"> Página principal
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="SoporteOP.php" class="custom-navbar-link hover-effect">
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
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="../mains/Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Operador: <?php echo htmlspecialchars($propietario); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="custom-text-container">
            <center>
                <img class="custom-img text-center" src="../mains/Assets/imgs/img_4.png" width="330" height="165">
            </center>
            <h1 class="custom-heading">Plataforma exclusiva para empleadores y operadores.</h1>
            <p class="custom-black-text">Maneja facturas y usuarios</p>
            <div class="custom-button-container">
                <a href="Ingreso.php" class="custom-link">
                    <button class="custom-button">Nuevo ingreso</button>
                </a>
                <a href="indexReservas.php" class="custom-link">
                    <button class="custom-button">Registrar salidas</button>
                </a>
                <a onclick="confirmLogout(event, '../metods/logout.php')" class="custom-link">
                    <button class="custom-button">Cerrar sesión</button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>