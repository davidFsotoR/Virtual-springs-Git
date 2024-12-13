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
$sql = "SELECT Propietario, Rol FROM vehiculos WHERE Placa = ?";
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
$rol = $vehiculo['Rol']; // Obtiene el rol del usuario

// Verifica si el rol es 'Operador'. Si no es, redirige al index de usuarios
if ($rol !== 'Operador') {
    header("Location: ../mains/index.php");
    exit();
}
?>
<?php
require '../metods/conexion.php';

// Consulta para espacios virtuales
$queryVirtual = "SELECT ubicacion FROM espacios WHERE estado = 'Virtual'";
$resultVirtual = $conn->query($queryVirtual);
$Virtuales = [];
while ($row = $resultVirtual->fetch_assoc()) {
    $Virtuales[] = $row['ubicacion'];
}

// Consulta para espacios ocupados
$queryOcupado = "SELECT ubicacion FROM espacios WHERE Disponibilidad = 'Ocupado'";
$resultOcupado = $conn->query($queryOcupado);
$Ocupados = [];
while ($row = $resultOcupado->fetch_assoc()) {
    $Ocupados[] = $row['ubicacion'];
}

$conn->close();
?>

<script>
    // Pasar datos de PHP a JavaScript
    var espaciosVirtuales = <?php echo json_encode($Virtuales); ?>;
    var espaciosOcupados = <?php echo json_encode($Ocupados); ?>;
</script>
<!DOCTYPE html>
<html>

<head>
    <title>Facturas</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <link rel="stylesheet" href="../mains/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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
        }

        /* Estilo para los botones deshabilitados */
        .bike-parking:disabled {
            background-color: #5a5a5a;
            /* Color gris claro para los espacios deshabilitados */
            cursor: not-allowed;
            /* Cursor de "no permitido" */
            color: white;
        }

        /* Espacios virtuales */
        .parking-space.virtual {
            background-color: #5a5a5a;
            /* Amarillo para espacios virtuales */
            cursor: not-allowed;
            color: white;

        }

        .parking-space.ocupao {
            background-color: #dc3545;
            /* Rojo para espacios ocupados */
            cursor: not-allowed;
            color: white;
        }
        .bike-parking.virtual {
            background-color: #5a5a5a;
            /* Amarillo para espacios virtuales */
            cursor: not-allowed;
            color: white;

        }

        .bike-parking.ocupao {
            background-color: #dc3545;
            /* Rojo para espacios ocupados */
            cursor: not-allowed;
            color: white;
        }

        .color-auto {
            background-color: var(--color-auto);
        }

        .color-moto {
            background-color: var(--color-moto);
        }

        .color-virtual {
            background-color: #5a5a5a;
        }

        .color-ocupado {
            background-color: red;
        }

        .color-seleccionado {
            background-color: black;
        }
    </style>

</html>

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
                        <a href="../operators/index_operador.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_5.png" width="23" height="23"> Página
                            principal
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logo que siempre está visible en el centro -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="../mains/Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Operador: <?php echo htmlspecialchars($propietario); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content2">
        <div class="custom-text-container">
            <h1 class=" text-center">Ingreso de vehiculo</h1>
            <form id="reservaForm" action="../metods/procesar_ingreso.php" method="POST">
                <div class="form-group">
                    <label class="custom-black-text" for="placa">Placa del vehículo:</label>
                    <input id="placa" class="form-control" type="text" name="placa">
                </div>
                <!-- Tipo de vehículo -->
                <div class="form-group">
                    <label class="custom-black-text" for="vehiculo">Tipo de vehículo:</label>
                    <select class="form-control" id="tipo_vehiculo" name="tipo_vehiculo" required>
                        <option value="" disabled selected>Seleccione el tipo de vehículo...</option>
                        <option value="Carro">Carro</option>
                        <option value="Camion">Camión</option>
                        <option value="Moto">Moto</option>
                    </select>
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
                            <span class="color-box color-seleccionado"></span>
                            Espacio seleccionado
                        </li>
                        <li>
                            <span class="color-box color-virtual"></span>
                            Espacios para reservas
                        </li>
                        <li>
                            <span class="color-box color-ocupado"></span>
                            Espacios ocupados
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
            document.querySelectorAll('.parking-space, .bike-parking').forEach(function(button) {
                var espacio = button.getAttribute('data-espacio');

                // Bloquear espacios virtuales
                if (espaciosVirtuales.includes(espacio)) {
                    button.classList.add('virtual');
                    button.disabled = true;
                }

                // Bloquear espacios ocupados
                if (espaciosOcupados.includes(espacio)) {
                    button.classList.add('ocupao');
                    button.disabled = true;
                }
            });
        </script>
        <script>
            document.getElementById('registrationForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Pa' frenar el envío del formulario si hay errores

                let isValid = true;

                // Validación de la placa
                const placa = document.getElementById('placa').value;
                const placaRegex = /^[A-Z]{3}-\d{3}$/; // Placa con 3 letras, guion y 3 números
                const placaError = document.getElementById('placaError');
                if (!placaRegex.test(placa)) {
                    placaError.style.display = 'block';
                    isValid = false;
                } else {
                    placaError.style.display = 'none';
                }

                // Si todo está bien, se manda el formulario
                if (isValid) {
                    document.getElementById('registrationForm').submit();
                }
            });
        </script>
</body>

</html>