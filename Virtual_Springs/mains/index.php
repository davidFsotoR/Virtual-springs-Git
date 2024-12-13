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

try {
    // Consulta para obtener el propietario y, opcionalmente, el rol basado en la placa
    $sql = "SELECT Propietario, Rol FROM vehiculos WHERE Placa = ?";
    $stmt = $conn->prepare($sql); // Prepara la consulta
    $stmt->bind_param("s", $placa); // Vincula la placa como parámetro
    $stmt->execute(); // Ejecuta la consulta
    $result = $stmt->get_result(); // Obtiene los resultados

    // Si no se encuentra el propietario, redirige a una página de error
    if ($result->num_rows === 0) {
        header("Location: ../subs/error.html");
        exit();
    }

    // Obtiene los datos del vehículo
    $vehiculo = $result->fetch_assoc();
    $propietario = $vehiculo['Propietario'];
    $rol = $vehiculo['Rol'] ?? 'Usuario'; // Rol opcional, 'Usuario' por defecto

    // Almacena el rol en la sesión para uso posterior
    $_SESSION['rol'] = $rol;

    // Libera recursos
    $stmt->close();
} catch (Exception $e) {
    // Manejo de errores, redirige a una página personalizada
    error_log("Error al consultar propietario: " . $e->getMessage());
    header("Location: ../subs/error.html");
    exit();
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
    <link rel="stylesheet" href="style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                            <img class="custom-icon" src="Assets/imgs/img_5.png" width="23" height="23"> Página principal
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
                    <li class="custom-navbar-item">
                        <a href="Politicas.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="Assets/imgs/img_9.png" width="23" height="23"> Políticas de servicio
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Identificador del usuario y logo -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link ms-2">
                    <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">
                        <?php echo htmlspecialchars($rol) . ": " . htmlspecialchars($propietario); ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <div class="custom-content">
        <div class="custom-text-container">
            <center>
                <img class="custom-img text-center" src="Assets/imgs/img_4.png" width="330" height="165">
            </center>
            <h1 class="custom-heading">Ten la tranquilidad de tu vehiculo en la palma de tu mano.</h1>
            <p class="custom-black-text">Reservas y pagos via Internet.</p>
            <div class="custom-button-container">
                <!-- Solo se muestran botones para usuarios registrados -->
                <a href="reservas.php" class="custom-link">
                    <button class="custom-button">Reservas</button>
                </a>
                <?php if (isset($rol)): ?>
                    <?php if ($rol === 'Operador'): ?>
                        <a href="../operators/index_operador.php" class="custom-link">
                            <button class="custom-button">Operador</button>
                        </a>
                    <?php elseif ($rol === 'Administrador'): ?>
                        <a href="../admin/index_admin.php" class="custom-link">
                            <button class="custom-button">Administrador</button>
                        </a>
                    <?php else: ?>
                        <a href="EditarP.php" class="custom-link">
                            <button class="custom-button">Editar perfil</button>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <a onclick="confirmLogout(event, '../metods/logout.php')" class="custom-link">
                    <button class="custom-button">Cerrar sesión</button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>