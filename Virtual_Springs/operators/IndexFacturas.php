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
<!DOCTYPE html>
<html xmlns:th="http://www.thymeleaf.org">

<head>
    <title>Facturas</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body {
            background-image: url(../mains/Assets/imgs/img_16.png) !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            background-attachment: fixed !important;
            overflow-x: hidden !important;
            height: 100vh !important;
            /* Esto garantiza que el fondo ocupe la altura total de la ventana */
            width: 100vw !important;
            /* Esto garantiza que el fondo ocupe el ancho total de la ventana */
        }

        .custom-content {
            display: flex;
            flex: 1;
            justify-content: left;
            align-items: left;
            margin-top: 90px !important;
        }

        /* Table Styling */
        .table {
            margin-top: 20px;
            background-color: #767676;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #011933;
            color: #ffffff;
        }

        /* Button Primary Styling */
        /* Button Primary Styling */
        .btn-primary {
            background-color: #007bff;
            border: none;
            margin-bottom: 15px;
            transition: background-color 0.3s;
            color: #ffffff !important;
            /* Texto en color blanco */
            font-weight: bold;
            /* Texto grueso */
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Warning and Danger Buttons Styling */
        .btn-warning,
        .btn-danger {
            transition: transform 0.2s;
            color: #ffffff !important;
            /* Texto en color blanco */
            font-weight: bold;
            /* Texto grueso */
        }

        .btn-warning:hover,
        .btn-danger:hover {
            transform: scale(1.05);
        }

        /* Si quieres poner la fuente en negrita para todo el contenido de la tabla */
        .table td,
        .table th {
            font-weight: bold;
            /* Hacer el texto en negrita */
        }

        .custom-box2 input[type="text"],
        .custom-box2 input[type="password"] {
            border: 0;
            background: none;
            border: 2px solid #343fdb;
            padding: 10px 10px;
            width: 1110px;
            /* Conservado el tamaño */
            outline: none;
            color: white;
            border-radius: 24px;
            /* Conservado el efecto */
        }

        .custom-box2 input[type="text"]:focus,
        .custom-box2 input[type="password"]:focus {
            width: 1110px;
            border-color: rgb(108, 117, 250) !important;
        }
    </style>

    <script>
        function confirmDelete(event) {
            if (!confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                event.preventDefault();
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
                        <a href="SoporteOP.php" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_6.png" width="23" height="23"> Soporte
                        </a>
                    </li>
                    <li class="custom-navbar-item">
                        <a href="#" class="custom-navbar-link hover-effect2">
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

            <!-- Logo que siempre está visible en el centro -->
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="../mains/Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">Operador: <?php echo htmlspecialchars($propietario); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="container mt-4">
            <h2 class="text-center text-white">Facturas</h2>
            <div class="mb-3">
                <form class="custom-box2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por placa..." onkeyup="filterTable()">
                </form>
            </div>
            <table class="table table-hover text-white">
                <thead>
                    <tr>
                        <th>ID Factura</th>
                        <th>Espacio</th>
                        <th>Fecha de Ingreso</th>
                        <th>Fecha de Salida</th>
                        <th>Placa</th>
                        <th>Tipo Vehículo</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../metods/conexion.php'; // Archivo de conexión a la base de datos

                    $query = "SELECT id_factura, Espacio, Fecha_ingreso, Fecha_salida, Placa, Tipo_vehiculo, Total FROM facturas";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $idFactura = htmlspecialchars($row['id_factura']);
                            $Espacio = htmlspecialchars($row['Espacio']);
                            $fechaIngreso = htmlspecialchars($row['Fecha_ingreso']);
                            $fechaSalida = htmlspecialchars($row['Fecha_salida']);
                            $placa = htmlspecialchars($row['Placa']);
                            $tipoVehiculo = htmlspecialchars($row['Tipo_vehiculo']);
                            $total = number_format($row['Total'], 2);

                            echo "<tr>
                                <td>$idFactura</td>
                                <td>$Espacio</td>
                                <td>$fechaIngreso</td>
                                <td>$fechaSalida</td>
                                <td>$placa</td>
                                <td>$tipoVehiculo</td>
                                <td>$$total</td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No hay facturas registradas</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function filterTable() {
            // Obtener el valor del input y convertirlo a minúsculas
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();

            // Obtener la tabla y sus filas
            const table = document.querySelector('.table tbody');
            const rows = table.getElementsByTagName('tr');

            // Recorrer las filas y mostrar/ocultar según el filtro
            for (let i = 0; i < rows.length; i++) {
                const placaCell = rows[i].getElementsByTagName('td')[4]; // Columna de la placa
                if (placaCell) {
                    const placaText = placaCell.textContent || placaCell.innerText;
                    if (placaText.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = ''; // Mostrar fila si coincide
                    } else {
                        rows[i].style.display = 'none'; // Ocultar fila si no coincide
                    }
                }
            }
        }
    </script>
</body>

</html>