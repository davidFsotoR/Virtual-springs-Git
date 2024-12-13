<?php
include '../metods/conexion.php';
session_start(); // Asegúrate de iniciar la sesión

// Inicializar variables para evitar errores de referencia
$fechaIngreso = $fechaSalida = $placa = $tipoVehiculo = $total = $espacio = null;

// Verificar si 'id_reserva' está presente en la URL
if (isset($_GET['id_reserva'])) {
    $idReserva = $_GET['id_reserva'];

    // Consultar los datos de la factura correspondiente, incluyendo el campo Espacio
    $queryFactura = "SELECT f.Fecha_ingreso, f.Fecha_salida, f.Placa, f.Tipo_vehiculo, f.Total, f.Espacio 
                     FROM facturas f 
                     WHERE f.id_reserva = ?";
    $stmt = $conn->prepare($queryFactura);

    if ($stmt === false) {
        die('Error al preparar la consulta: ' . $conn->error);
    }

    $stmt->bind_param("i", $idReserva);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fechaIngreso = $row['Fecha_ingreso'];
        $fechaSalida = $row['Fecha_salida'];
        $placa = $row['Placa'];
        $tipoVehiculo = $row['Tipo_vehiculo'];
        $total = number_format($row['Total'], 2); // Formatear el total
        $espacio = $row['Espacio'];
    } else {
        echo "No se encontró una factura para esta reserva.";
    }

    $stmt->close();
}

$conn->close();

session_start(); // Inicia la sesión

$rol = $_SESSION['rol'] ?? null; // Obtiene el rol desde la sesión
$url = "";
if ($rol === 'Usuario') {
    $url = "../mains/Historial.php";
} elseif ($rol === 'Operador') {
    $url = "../operators/indexReservas.php";
} else {
    $url = "../default.php"; // Opcional
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <link href="https://fonts.googleapis.com/css?family=Fjalla+One|Libre+Baskerville" rel="stylesheet">
    <title>Virtual Springs</title>
    <link rel="icon" href="../mains/assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
/* Añadir el CSS aquí */
.custom-card {
    background-color: #333;
    color: white;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.custom-card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.4);
}

.custom-card-title {
    font-family: 'Fjalla One', sans-serif;
    font-size: 1.5rem;
}

.custom-card-text {
    font-family: 'Libre Baskerville', serif;
    font-size: 1rem;
}

body {
    font-family: 'Libre Baskerville', serif;
    background-color: #f4f4f4;
}

/* Media query para dispositivos móviles */
@media screen and (max-width: 768px) {
    .custom-card {
        border-radius: 5px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        margin: 10px auto; /* Centramos verticalmente */
        padding: 15px;
        width: 90%; /* Ocupa el 90% del ancho del dispositivo */
        max-width: 100%; /* Aseguramos que no se exceda del contenedor */
    }

    .custom-card-title {
        font-size: 1.2rem; /* Reduce el tamaño del título */
        text-align: center;
    }

    .custom-card-text {
        font-size: 0.9rem; /* Reduce el tamaño del texto */
        text-align: justify;
    }
}
</style>
</head>

<body>
    <nav class="custom-nav navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="custom-navbar">
                    <li class="custom-navbar-item">
                        <a href="<?php echo $url; ?>" class="custom-navbar-link hover-effect">
                            <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Volver
                        </a>
                    </li>
                </ul>
            </div>
            <div class="custom-logo">
                <img src="../mains/Assets/imgs/img_3.png" alt="Logo">
            </div>
        </div>
    </nav>
    <div class="custom-content">
        <div class="custom-text-container">
            <?php if ($fechaIngreso && $fechaSalida && $placa && $tipoVehiculo && $total && $espacio): ?>
                <div class="card custom-card" style="width: 50rem;">
                    <div class="card-body">
                        <h5 class="card-title custom-card-title">Factura para <?php echo htmlspecialchars($tipoVehiculo); ?> - <?php echo htmlspecialchars($placa); ?></h5>
                        <p class="card-text custom-card-text"><strong>Espacio:</strong> <?php echo htmlspecialchars($espacio); ?></p>
                        <p class="card-text custom-card-text"><strong>Fecha de Ingreso:</strong> <?php echo htmlspecialchars($fechaIngreso); ?></p>
                        <p class="card-text custom-card-text"><strong>Fecha de Salida:</strong> <?php echo htmlspecialchars($fechaSalida); ?></p>
                        <p class="card-text custom-card-text"><strong>Total:</strong> $<?php echo htmlspecialchars($total); ?></p>
                        <p class="card-text custom-card-text"><strong>Placa:</strong> <?php echo htmlspecialchars($placa); ?></p>
                        <p class="card-text custom-card-text"><strong>Tipo de Vehículo:</strong> <?php echo htmlspecialchars($tipoVehiculo); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p>No se encontraron datos para esta factura.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>