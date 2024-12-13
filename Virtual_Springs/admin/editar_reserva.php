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
$query = "SELECT ubicacion, estado FROM espacios WHERE estado = 'Presencial'";
$result = $conn->query($query);

// Crear un array de espacios ocupados
$presenciales = [];
while ($row = $result->fetch_assoc()) {
    $presenciales[] = $row['ubicacion'];
}

$rol = $_SESSION['rol'] ?? 'Usuario';

$conn->close();
?>
<?php
require '../metods/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consultar los datos de la reserva
    $query = "SELECT * FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $reserva = $result->fetch_assoc();
    } else {
        echo "<script>alert('Reserva no encontrada.'); window.location.href = 'Historial.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID de reserva no proporcionado.'); window.location.href = 'Historial.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <link rel="stylesheet" href="../mains/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="reservas_admin.php" class=" hover-effect custom-navbar-link">
                        <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-navbar-item d-flex align-items-center ">
                <a class="custom-navbar-link hover-effect ms-2"> <img class="custom-icon" src="../mains/Assets/imgs/img_3.png" alt="Logo" width="35" height="35">
                    <span class="custom-propietario">
                        Administrador: <?php echo htmlspecialchars($propietario); ?>
                    </span> </a>
            </div>
        </div>
    </nav>
    <form class="custom-box" action="procesar_edicion.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($reserva['id']); ?>">


        <input type="text" class="form-control" id="placa" name="placa" value="<?php echo htmlspecialchars($reserva['placa']); ?>" required>



        <input type="text" class="form-control" id="tipo_vehiculo" name="tipo_vehiculo" value="<?php echo htmlspecialchars($reserva['tipo_vehiculo']); ?>" required>



        <input type="text" class="form-control" id="espacio" name="espacio" value="<?php echo htmlspecialchars($reserva['espacio']); ?>" required>

        <input type="datetime-local" class="custom-datetime" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo date('Y-m-d\TH:i', strtotime($reserva['fecha_ingreso'])); ?>" required>

        <input type="datetime-local" class="custom-datetime" id="fecha_hora_salida" name="fecha_hora_salida" value="<?php echo date('Y-m-d\TH:i', strtotime($reserva['fecha_hora_salida'])); ?>">
        <input type="submit" value="Guardar cambios">
    </form>
    </div>
</body>

</html>