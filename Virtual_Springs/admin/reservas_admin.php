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

// Verifica si el rol es 'Administrador'. Si no es, redirige al index de usuarios
if ($rol !== 'Administrador') {
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
            height: 100vh !important;
            width: 100vw !important;
            overflow-x: hidden;
            /* Oculta la barra de desplazamiento horizontal */

        }

        .custom-content {
            display: flex;
            flex: 1;
            justify-content: left;
            align-items: left;
            margin-top: 90px !important;
        }

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

        .action-buttons {
            display: flex;
            gap: 10px;
            /* Espaciado entre botones */
        }

        .action-buttons button {
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: transform 0.2s, background-color 0.3s;
        }

        /* Botón visualizar */
        .btn-visualizar {
            background-color: #007bff;
            /* Azul */
        }

        .btn-visualizar:hover {
            background-color: #0056b3;
        }

        /* Botón eliminar */
        .btn-eliminar {
            background-color: #dc3545;
            /* Rojo */
        }

        .btn-eliminar:hover {
            background-color: #a71d2a;
        }

        /* Botón editar */
        .btn-editar {
            background-color: #28a745;
            /* Verde */
        }

        .btn-editar:hover {
            background-color: #1e7e34;
        }

        /* Botón deshabilitado */
        .btn-disabled {
            background-color: #6c757d;
            /* Gris */
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            background-color: #6c757d;
            /* Sin efecto hover */
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
                    <span class="custom-propietario">Administrador: <?php echo htmlspecialchars($propietario); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="container mt-4">
            <h2 class="text-center text-white">Reservas</h2>
            <div class="mb-3">
                <form class="custom-box2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por placa..." onkeyup="filterTable()">
                </form>
            </div>
            <table class="table table-hover text-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Tipo Vehículo</th>
                        <th>Espacio</th>
                        <th>Fecha de Ingreso</th>
                        <th>Fecha de Salida</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../metods/conexion.php';
                    date_default_timezone_set('America/Bogota');

                    $query = "SELECT id, placa, tipo_vehiculo, espacio, fecha_ingreso, fecha_hora_salida FROM reservas WHERE fecha_hora_salida IS NOT NULL";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row['id']);
                            $placa = htmlspecialchars($row['placa']);
                            $tipoVehiculo = htmlspecialchars($row['tipo_vehiculo']);
                            $espacio = htmlspecialchars($row['espacio']);
                            $fechaIngreso = htmlspecialchars($row['fecha_ingreso']);
                            $fechaSalida = htmlspecialchars($row['fecha_hora_salida']);

                            // Consultar si hay una factura generada para esta reserva
                            $queryFactura = "SELECT * FROM facturas WHERE id_reserva = ?";
                            $stmtFactura = $conn->prepare($queryFactura);
                            $stmtFactura->bind_param("i", $id);
                            $stmtFactura->execute();
                            $resultFactura = $stmtFactura->get_result();
                            $facturaGenerada = $resultFactura->num_rows > 0;

                            echo "<tr>
                                    <td>$id</td>
                                    <td>$placa</td>
                                    <td>$tipoVehiculo</td>
                                    <td>$espacio</td>
                                    <td>$fechaIngreso</td>
                                    <td>$fechaSalida</td>
                                    <td>
                                        <div class='action-buttons'>
                                            <form action='../metods/visualizar_factura.php' method='GET'>
                                                <input type='hidden' name='id_reserva' value='$id'>
                                                <button type='submit' class='btn btn-visualizar'>Visualizar</button>
                                            </form>";

                            if (!$facturaGenerada) {
                                echo "<button type='button' class='btn btn-disabled' disabled>Sin Factura</button>";
                            }

                            echo "          <form action='eliminar_reserva.php' method='POST' onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar esta reserva?\");'>
                                                <input type='hidden' name='id' value='$id'>
                                                <button type='submit' class='btn btn-eliminar'>Eliminar</button>
                                            </form>
                                            <form action='editar_reserva.php' method='GET'>
                                                <input type='hidden' name='id' value='$id'>
                                                <button type='submit' class='btn btn-editar'>Editar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>";

                            $stmtFactura->close();
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No hay reservas registradas con salida definida</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <script>
                function confirmarEliminacion(placa) {
                    return confirm(`¿Está seguro de eliminar la reserva para el vehículo con placa ${placa}?`);
                }
            </script>
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
                        const placaCell = rows[i].getElementsByTagName('td')[1]; // Columna de la placa
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

        </div>
    </div>
</body>

</html>