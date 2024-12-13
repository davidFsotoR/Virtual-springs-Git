<?php
// Inicia la sesión para acceder a las variables de sesión
session_start();

// Incluye el archivo de conexión a la base de datos
require '../metods/conexion.php';

// Verifica si no existe la variable de sesión 'placa', lo que significa que el usuario no está autenticado
if (!isset($_SESSION['placa'])) {
    // Redirige al usuario a la página de inicio si no está autenticado
    header("Location: ../mains/indexUNLOG.html");
    exit();
}

// Asigna la placa del vehículo almacenada en la variable de sesión a la variable $placa
$placa = $_SESSION['placa'];

// Consulta los datos del vehículo correspondiente a la placa en la base de datos
$sql = "SELECT * FROM vehiculos WHERE Placa = ?";
$stmt = $conn->prepare($sql); // Prepara la consulta SQL
$stmt->bind_param("s", $placa); // Vincula el parámetro de la consulta con la placa
$stmt->execute(); // Ejecuta la consulta
$result = $stmt->get_result(); // Obtiene el resultado de la consulta

// Si no se encuentra ningún vehículo con la placa proporcionada, muestra un mensaje de error
if ($result->num_rows === 0) {
    die("Error: No se encontraron datos para la placa proporcionada.");
}

// Obtiene los datos del vehículo en un arreglo asociativo
$vehiculo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlaza la hoja de estilos CSS -->
    <link rel="stylesheet" href="../mains/style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <!-- Enlaza los estilos de Bootstrap desde un CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Enlaza los scripts de Bootstrap desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <style>
        /* Estilo personalizado para los mensajes de error */
        .error-message {
            color: red;
            font-size: 0.9em;
            display: none; /* Los mensajes de error están ocultos por defecto */
        }

        /* Asegura que los placeholders sean visibles */
        input::placeholder {
            color: #6c757d;
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Barra de navegación personalizada -->
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="../mains/reservas.php" class="custom-navbar-link hover-effect">
                        <!-- Icono de cancelar reserva -->
                        <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-logo">
                <img src="../mains/Assets/imgs/img_3.png">
            </div>
        </div>
    </nav>
    
    <!-- Formulario para editar los datos del vehículo -->
    <form action="../metods/editarreserva.php" method="POST" class="custom-box" id="registrationForm">
        <h1></h1> <!-- Título vacío (se puede añadir más contenido si es necesario) -->
        
        <!-- Campo oculto que envía la placa original -->
        <input type="hidden" name="placa_original" value="<?php echo $placa; ?>">

        <!-- Campo de entrada para la placa del vehículo con el valor actual de la placa -->
        <input type="text" name="placa" value="<?php echo htmlspecialchars($vehiculo['Placa']); ?>">
        <span class="error-message" id="placaError">La placa debe tener un formato como este "JCX-505".</span>

        <!-- Combobox para seleccionar el tipo de vehículo -->
        <select name="tipo_vehiculo" required>
            <option value="" disabled>Seleccione el tipo de vehículo...</option>
            <!-- Las opciones se seleccionan automáticamente si el tipo de vehículo coincide con el valor almacenado en la base de datos -->
            <option value="Carro" <?php if ($vehiculo['Tipo_vehiculo'] === 'Carro') echo 'selected'; ?>>Carro</option>
            <option value="Camion" <?php if ($vehiculo['Tipo_vehiculo'] === 'Camion') echo 'selected'; ?>>Camión</option>
            <option value="Moto" <?php if ($vehiculo['Tipo_vehiculo'] === 'Moto') echo 'selected'; ?>>Moto</option>
        </select>
        
        <!-- Botón para enviar el formulario y actualizar los datos del vehículo -->
        <input type="submit" value="Actualizar datos">
    </form>

</body>

</html>
