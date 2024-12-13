<?php
session_start();
require '../metods/conexion.php';

if (!isset($_SESSION['placa'])) {
    header("Location: ../subs/indexUNLOG.html");
    exit();
}

$placa = $_SESSION['placa'];

// Consultar los datos del vehículo, incluyendo el atributo Propietario
$sql = "SELECT * FROM vehiculos WHERE Placa = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontraron datos para la placa proporcionada.");
}

$vehiculo = $result->fetch_assoc();

// Acceder al atributo Propietario
$propietario = $vehiculo['Propietario']; // Aquí se obtiene el nombre del propietario
?>

<?php
// Conectar y obtener los espacios ocupados
require '../metods/conexion.php';
$query = "SELECT Ubicacion, estado FROM Espacios WHERE estado = 'Presencial'";
$result = $conn->query($query);

// Crear un array de espacios ocupados
$presenciales = [];
while ($row = $result->fetch_assoc()) {
    $presenciales[] = $row['Ubicacion'];
}

$rol = $_SESSION['rol'] ?? 'Usuario';

$conn->close();
?>
<script>
    // Pasar los espacios ocupados desde PHP a JavaScript
    var espaciosPresenciales = <?php echo json_encode($presenciales); ?>;
</script>
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
        .custom-black-text {
            margin-top: 20px;
        }

        button.disabled {
            pointer-events: none;
            /* Deshabilitar interacción */
            cursor: not-allowed;
            /* Cursor de prohibido */
            opacity: 0.5;
            /* Hacerlo visualmente diferente */
        }

        /* Estilo para los botones ocupados */
        .parking-space.selected {
            background-color: #5a5a5a;
            cursor: not-allowed;
            /* Cursor de "no permitido" */
        }

        /* Estilo para los botones deshabilitados */
        .parking-space:disabled {
            background-color: #5a5a5a;
            /* Color gris claro para los espacios deshabilitados */
            cursor: not-allowed;
            /* Cursor de "no permitido" */
        }

        .bike-parking.selected {
            background-color: #5a5a5a;
            cursor: not-allowed;
            /* Cursor de "no permitido" */
        }

        /* Estilo para los botones deshabilitados */
        .bike-parking:disabled {
            background-color: #5a5a5a;
            /* Color gris claro para los espacios deshabilitados */
            cursor: not-allowed;
            /* Cursor de "no permitido" */
        }
    </style>
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="Index.php" class=" hover-effect custom-navbar-link">
                        <img class="custom-icon" src="Assets/imgs/img_10.png" width="23" height="23"> Volver al inicio
                    </a>
                </li>
            </ul>
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">
                        <?php echo htmlspecialchars($rol) . ": " . htmlspecialchars($propietario); ?>
                    </span> </a>
            </div>
        </div>
    </nav>
    <div class="custom-content2">
        <div class="custom-text-container">
            <h1 class=" text-center">Reserva tu Parqueadero</h1>
            <form id="reservaForm" action="../metods/procesar_reserva.php" method="POST">
                <div class="form-group">
                    <label class="custom-black-text" for="placa">Placa del vehículo:</label>
                    <span><?php echo htmlspecialchars($placa); ?></span>
                    <input type="hidden" name="placa" value="<?php echo htmlspecialchars($placa); ?>">
                    <a href="../subs/editaR.php">
                        <img class="custom-icon" src="Assets/imgs/img_15.png" width="30" height="30">
                    </a>
                </div>
                <!-- Tipo de vehículo -->
                <div class="form-group">
                    <label class="custom-black-text" for="vehiculo">Tipo de vehículo:</label>
                    <span><?php echo htmlspecialchars($vehiculo['Tipo_vehiculo']); ?></span>
                    <input type="hidden" name="vehiculo" value="<?php echo htmlspecialchars($vehiculo['Tipo_vehiculo']); ?>">
                    <a href="../subs/editaR.php">
                        <img class="custom-icon" src="Assets/imgs/img_15.png" width="30" height="30">
                    </a>
                </div>
                <!-- Fecha y hora de ingreso -->
                <div class="form-group">
                    <label class="custom-black-text" for="fecha_ingreso">Fecha y hora de ingreso:</label>
                    <input type="datetime-local" class="form-control" id="fecha_ingreso" name="fecha_ingreso" required>
                </div>
                <div class="form-group">
                    <label for="espacioSeleccionado" class="custom-black-text">Espacio Seleccionado:</label>
                    <input class="form-control" type="text" id="selected-input" name="espacio" readonly required>

                </div>

                <h4 class="text-center">Selecciona un espacio de parqueo:</h4>
                <ul class="espacios-list">
                    <h3 class="custom-black-text">
                        <li>
                            <span class="color-box color-auto"></span>
                            Espacios para Carros/Camiones
                        </li>
                        <li>
                            <span class="color-box color-moto"></span>
                            Espacios para Motos
                        </li>
                        <li>
                            <span class="color-box color-ocupado"></span>
                            Espacios para autos presenciales
                        </li>
                        <li>
                            <span class="color-box color-seleccionado"></span>
                            Espacio seleccionado
                        </li>
                    </h3>
                </ul>

                <div id="parking-lot">

                    <!-- Fila 1 -->
                    <div class="empty-space">↓</div>
                    <button class="parking-space" data-espacio="CAM1">CAM1</button>
                    <button class="parking-space" data-espacio="CAM2">CAM2</button>
                    <button class="parking-space" data-espacio="CAM3">CAM3</button>
                    <button class="parking-space" data-espacio="CAM4">CAM4</button>
                    <button class="parking-space" data-espacio="CAM5">CAM5</button>
                    <button class="parking-space" data-espacio="CAM6">CAM6</button>
                    <button class="parking-space" data-espacio="CAM7">CAM7</button>
                    <button class="parking-space" data-espacio="CAM8">CAM8</button>


                    <!-- Fila 2 -->
                    <div class="empty-space">↓</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>

                    <!-- Fila 3 -->
                    <div class="empty-space">↓</div>
                    <button class="parking-space" data-espacio="CAR9">CAR9</button>
                    <button class="parking-space" data-espacio="CAR10">CAR10</button>
                    <button class="parking-space" data-espacio="CAR11">CAR11</button>
                    <button class="parking-space" data-espacio="CAR12">CAR12</button>
                    <button class="parking-space" data-espacio="CAR13">CAR13</button>
                    <button class="parking-space" data-espacio="CAR14">CAR14</button>
                    <button class="parking-space" data-espacio="CAR15">CAR15</button>
                    <button class="parking-space" data-espacio="CAR16">CAR16</button>

                    <!-- Fila 4 -->
                    <div class="empty-space">↓</div>
                    <button class="bike-parking" data-espacio="M1">M1</button>
                    <button class="bike-parking" data-espacio="M2">M2</button>
                    <button class="bike-parking" data-espacio="M3">M3</button>
                    <button class="bike-parking" data-espacio="M4">M4</button>
                    <button class="bike-parking" data-espacio="M5">M5</button>
                    <button class="bike-parking" data-espacio="M6">M6</button>
                    <button class="bike-parking" data-espacio="M7">M7</button>
                    <button class="bike-parking" data-espacio="M8">M8</button>
                    <!-- Fila 5 -->
                    <div class="empty-space">↓</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>

                    <!-- Fila 6 -->
                    <div class="empty-space">↓</div>
                    <button class="bike-parking" data-espacio="M9">M9</button>
                    <button class="bike-parking" data-espacio="M10">M10</button>
                    <button class="bike-parking" data-espacio="M11">M11</button>
                    <button class="bike-parking" data-espacio="M12">M12</button>
                    <button class="bike-parking" data-espacio="M13">M13</button>
                    <button class="bike-parking" data-espacio="M14">M14</button>
                    <button class="bike-parking" data-espacio="M15">M15</button>
                    <button class="bike-parking" data-espacio="M16">M16</button>

                    <!-- Fila 7 -->
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                    <div class="empty-space">→</div>
                </div>
                <div class="custom-button-container">
                    <button type="submit" class="custom-button">Reservar</button>
                </div>
            </form>
        </div>
        <script src="../metods/reservas.js"></script>
        <script>
            document.querySelectorAll('.parking-space, .bike-parking').forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevenir acción por defecto
                    const espacio = event.target.dataset.espacio; // Obtener el espacio
                    document.getElementById('selected-input').value = espacio; // Asignar al campo oculto
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Seleccionamos todos los botones de espacio de parqueo y los de bike-parking
                const parkingSpaces = document.querySelectorAll('.parking-space, .bike-parking');

                // Recorrer todos los botones de los espacios
                parkingSpaces.forEach(button => {
                    const espacio = button.getAttribute('data-espacio'); // Obtener el identificador del espacio (por ejemplo, C1, C2, etc.)

                    // Si el espacio está en la lista de ocupados, deshabilitar el botón y darle un estilo "ocupado"
                    if (espaciosPresenciales.includes(espacio)) {
                        button.classList.add('selected'); // Cambiar el estilo visual
                        button.disabled = true; // Deshabilitar el botón para que no sea clickeable
                    }
                });
            });
        </script>
</body>

</html>