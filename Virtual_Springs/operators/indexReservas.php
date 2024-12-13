<?php
// Inicia la sesión para acceder a las variables de sesión
session_start();
require '../metods/conexion.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['placa'])) {
    header("Location: ../subs/indexUNLOG.html");
    exit();
}

$_SESSION['rol'] = 'Operador'; // Esto asegura que el rol esté definido si no lo está ya

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
include '../metods/conexion.php'; // Archivo de conexión a la base de datos
date_default_timezone_set('America/Bogota'); // Cambia a tu zona horaria correspondiente

// Número de resultados por página
$results_per_page = 10;

// Determinamos la página actual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Consulta SQL con ordenación para mostrar las reservas sin salida primero
$query = "
    SELECT id, placa, tipo_vehiculo, espacio, fecha_ingreso, fecha_hora_salida 
    FROM reservas
    ORDER BY CASE WHEN fecha_hora_salida IS NULL THEN 0 ELSE 1 END, fecha_ingreso DESC
    LIMIT ?, ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html>

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
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = htmlspecialchars($row['id']);
                        $placa = htmlspecialchars($row['placa']);
                        $tipoVehiculo = htmlspecialchars($row['tipo_vehiculo']);
                        $espacio = htmlspecialchars($row['espacio']);
                        $fechaIngreso = htmlspecialchars($row['fecha_ingreso']);
                        $fechaSalida = $row['fecha_hora_salida'] ? htmlspecialchars($row['fecha_hora_salida']) : 'Pendiente';

                        // Consultar si hay una factura generada para esta reserva
                        $queryFactura = "SELECT * FROM facturas WHERE ID_reserva = ?";
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
                        <td>";

                        // Opciones según el estado de la reserva
                        if ($fechaSalida === 'Pendiente') {
                            // Mostrar botón para registrar salida (incluye la generación de factura)
                            echo "<form action='../metods/procesar_salida.php' method='POST' onsubmit='return confirmarSalida(\"$placa\")'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='espacio' value='$espacio'>
                            <button class='btn btn-warning' type='submit'>Registrar Salida</button>
                          </form>";
                        } else {
                            if ($facturaGenerada) {
                                // Mostrar botón para visualizar factura
                                echo "<form action='../metods/visualizar_factura.php' method='GET'>
                                <input type='hidden' name='id_reserva' value='$id'>
                                <button class='btn btn-info' type='submit'>Visualizar Factura</button>
                              </form>";
                            }
                        }

                        echo "</td>
                    </tr>";

                        $stmtFactura->close();
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No hay reservas registradas</td></tr>";
                }

                // Obtener el total de reservas para la paginación
                $queryTotal = "SELECT COUNT(*) AS total FROM reservas";
                $resultTotal = $conn->query($queryTotal);
                $totalRow = $resultTotal->fetch_assoc();
                $total_pages = ceil($totalRow['total'] / $results_per_page);

                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>

        <script>
            function confirmarSalida(placa) {
                return confirm(`¿Está seguro de registrar salida para el vehículo con placa ${placa}?`);
            }

            function filterTable() {
                const input = document.getElementById('searchInput');
                const filter = input.value.toLowerCase();
                const table = document.querySelector('.table tbody');
                const rows = table.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const placaCell = rows[i].getElementsByTagName('td')[1];
                    if (placaCell) {
                        const placaText = placaCell.textContent || placaCell.innerText;
                        if (placaText.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            }
        </script>
    </div>
</div>
</body>

</html>