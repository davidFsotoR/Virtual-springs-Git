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

$vehiculo = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <style>
        /* Estilo para los mensajes de error */
        .error-message {
            color: red;
            font-size: 0.9em;
            display: none;
        }

        /* Asegurar que los placeholders sean visibles */
        input::placeholder {
            color: #6c757d;
            opacity: 1;
        }
    </style>
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="index.php" class="custom-navbar-link hover-effect">
                        <img class="custom-icon" src="Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Usuario: <?php echo htmlspecialchars($vehiculo['Propietario']); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <form action="../metods/editardatos.php" method="POST" class="custom-box" id="registrationForm">
        <h1>Edite los datos que necesite...</h1>
        <input type="hidden" name="placa_original" value="<?php echo $placa; ?>">

        <input type="text" name="placa" value="<?php echo htmlspecialchars($vehiculo['Placa']); ?>">
        <span class="error-message" id="placaError">La placa debe tener un formato como este "JCX-505".</span>

        <input type="text" name="propietario" value="<?php echo htmlspecialchars($vehiculo['Propietario']); ?>" required>

        <input type="text" name="numero_propietario" value="<?php echo htmlspecialchars($vehiculo['Numero_propietario']); ?>" required>

        <input type="text" name="correo_propietario" value="<?php echo htmlspecialchars($vehiculo['Correo_propietario']); ?>" required>
        <span class="error-message" id="emailError">Por favor, ingrese un correo válido.</span>

        <!-- Combobox para seleccionar el tipo de vehículo -->
        <select name="tipo_vehiculo" required>
            <option value="" disabled>Seleccione el tipo de vehículo...</option>
            <option value="Carro" <?php if ($vehiculo['Tipo_vehiculo'] === 'Carro') echo 'selected'; ?>>Carro</option>
            <option value="Camion" <?php if ($vehiculo['Tipo_vehiculo'] === 'Camion') echo 'selected'; ?>>Camión</option>
            <option value="Moto" <?php if ($vehiculo['Tipo_vehiculo'] === 'Moto') echo 'selected'; ?>>Moto</option>
        </select>
        <input type="submit" value="Actualizar datos">
    </form>

    </script>
</body>

</html>